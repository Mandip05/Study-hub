<?php
/**
 * Course Listing Page
 * Study Hub LMS
 */

require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

// Get filters
$category = $_GET['category'] ?? '';
$level = $_GET['level'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$whereConditions = ["c.status = 'published'", "c.approval_status = 'approved'"];
$params = [];

if ($category) {
    $whereConditions[] = "c.category = :category";
    $params[':category'] = $category;
}

if ($level) {
    $whereConditions[] = "c.level = :level";
    $params[':level'] = $level;
}

if ($search) {
    $whereConditions[] = "(c.title LIKE :search OR c.description LIKE :search)";
    $params[':search'] = "%$search%";
}

$whereClause = implode(' AND ', $whereConditions);

$coursesQuery = "SELECT c.*, u.full_name as teacher_name, 
                 (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrolled_count
                 FROM courses c 
                 JOIN users u ON c.teacher_id = u.id 
                 WHERE $whereClause
                 ORDER BY c.created_at DESC";

$stmt = $conn->prepare($coursesQuery);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$courses = $stmt->fetchAll();

$pageTitle = 'Courses - Study Hub';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
    .courses-header {
        background: linear-gradient(135deg, #0A8BCB 0%, #1E9ED8 100%);
        color: white;
        padding: 3rem 0;
        text-align: center;
    }
    
    .courses-header h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: white;
    }
    
    .courses-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 3rem 2rem;
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
    }
    
    .filters-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }
    
    .filter-card {
        background: white;
        padding: 1.5rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        margin-bottom: 1.5rem;
    }
    
    .filter-card h3 {
        font-size: 1.125rem;
        margin-bottom: 1rem;
        color: var(--dark-text);
    }
    
    .filter-option {
        display: block;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .filter-option:hover {
        background: var(--background);
    }
    
    .filter-option.active {
        background: var(--primary-blue);
        color: white;
    }
    
    .filter-option input[type="radio"] {
        margin-right: 0.5rem;
    }
    
    .courses-main {
        min-height: 500px;
    }
    
    .courses-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        background: white;
        padding: 1rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
    }
    
    .results-count {
        color: var(--gray);
        font-size: 0.95rem;
    }
    
    .view-toggle {
        display: flex;
        gap: 0.5rem;
    }
    
    .view-btn {
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .view-btn.active {
        background: var(--primary-blue);
        color: white;
        border-color: var(--primary-blue);
    }
    
    @media (max-width: 1024px) {
        .courses-container {
            grid-template-columns: 1fr;
        }
        
        .filters-sidebar {
            position: relative;
            top: 0;
        }
    }
</style>

<!-- Header -->
<div class="courses-header">
    <div class="container">
        <h1>Explore Courses</h1>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.9);">
            <?php echo $search ? "Search results for \"$search\"" : "Discover your next learning adventure"; ?>
        </p>
    </div>
</div>

<!-- Main Content -->
<div class="courses-container">
    <!-- Filters Sidebar -->
    <aside class="filters-sidebar">
        <div class="filter-card">
            <h3><i class="fas fa-sliders-h"></i> Categories</h3>
            <a href="courses.php" class="filter-option <?php echo !$category ? 'active' : ''; ?>">
                All Categories
            </a>
            <?php
            $categories = ['CSIT', 'BCA', 'Engineering', 'Entrance Prep', 'Other'];
            foreach ($categories as $cat):
            ?>
                <a href="courses.php?category=<?php echo urlencode($cat); ?><?php echo $level ? '&level=' . $level : ''; ?>" 
                   class="filter-option <?php echo $category === $cat ? 'active' : ''; ?>">
                    <?php echo $cat; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="filter-card">
            <h3><i class="fas fa-layer-group"></i> Level</h3>
            <a href="courses.php<?php echo $category ? '?category=' . $category : ''; ?>" 
               class="filter-option <?php echo !$level ? 'active' : ''; ?>">
                All Levels
            </a>
            <?php
            $levels = ['Beginner', 'Intermediate', 'Advanced'];
            foreach ($levels as $lvl):
            ?>
                <a href="courses.php?<?php echo $category ? 'category=' . $category . '&' : ''; ?>level=<?php echo urlencode($lvl); ?>" 
                   class="filter-option <?php echo $level === $lvl ? 'active' : ''; ?>">
                    <?php echo $lvl; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="filter-card">
            <a href="courses.php" class="btn btn-outline btn-block">
                <i class="fas fa-redo"></i> Clear Filters
            </a>
        </div>
    </aside>
    
    <!-- Courses Grid -->
    <main class="courses-main">
        <div class="courses-toolbar">
            <div class="results-count">
                <strong><?php echo count($courses); ?></strong> courses found
            </div>
        </div>
        
        <div class="grid grid-cols-3">
            <?php if (count($courses) > 0): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-card fade-in">
                        <div class="course-thumbnail" style="background: linear-gradient(135deg, <?php echo '#' . substr(md5($course['id']), 0, 6); ?>, <?php echo '#' . substr(md5($course['id'] . 'salt'), 0, 6); ?>); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-book" style="font-size: 4rem; color: white; opacity: 0.7;"></i>
                        </div>
                        <div class="course-content">
                            <div class="flex-between" style="margin-bottom: 0.75rem;">
                                <span class="course-category"><?php echo $course['category']; ?></span>
                                <span class="badge badge-info"><?php echo $course['level']; ?></span>
                            </div>
                            <h3 class="course-title"><?php echo $course['title']; ?></h3>
                            <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 1rem;">
                                <?php echo substr($course['description'], 0, 100); ?>...
                            </p>
                            <div class="course-meta">
                                <span><i class="fas fa-user"></i> <?php echo $course['teacher_name']; ?></span>
                                <span><i class="fas fa-users"></i> <?php echo $course['enrolled_count']; ?></span>
                            </div>
                            <?php if ($course['duration']): ?>
                                <div style="margin-top: 0.5rem; color: var(--gray); font-size: 0.875rem;">
                                    <i class="fas fa-clock"></i> <?php echo $course['duration']; ?> hours
                                </div>
                            <?php endif; ?>
                            <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 5rem 2rem;">
                    <i class="fas fa-search" style="font-size: 5rem; color: var(--gray); opacity: 0.3;"></i>
                    <h3 style="margin-top: 2rem; color: var(--gray);">No courses found</h3>
                    <p style="color: var(--gray); margin-bottom: 1.5rem;">Try adjusting your filters or search terms</p>
                    <a href="courses.php" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Clear Filters
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
