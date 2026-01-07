<?php
/**
 * Library Page
 * Study Hub LMS - Digital Library
 */

require_once 'config/config.php';

$db = new Database();
$conn = $db->getConnection();

// Get filters
$category = $_GET['category'] ?? '';
$course_category = $_GET['course_category'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$whereConditions = [];
$params = [];

if ($category) {
    $whereConditions[] = "category = :category";
    $params[':category'] = $category;
}

if ($course_category) {
    $whereConditions[] = "course_category = :course_category";
    $params[':course_category'] = $course_category;
}

if ($search) {
    $whereConditions[] = "(title LIKE :search OR description LIKE :search)";
    $params[':search'] = "%$search%";
}

$whereClause = count($whereConditions) > 0 ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

$libraryQuery = "SELECT l.*, u.full_name as uploaded_by_name 
                 FROM library l
                 JOIN users u ON l.uploaded_by = u.id
                 $whereClause
                 ORDER BY l.created_at DESC";

$stmt = $conn->prepare($libraryQuery);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$libraryItems = $stmt->fetchAll();

$pageTitle = 'Library - Study Hub';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
    .library-header {
        background: linear-gradient(135deg, #0A8BCB 0%, #1E9ED8 100%);
        color: white;
        padding: 3rem 0;
        text-align: center;
    }
    
    .library-header h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: white;
    }
    
    .library-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 3rem 2rem;
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
    }
    
    .library-item {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        display: flex;
        gap: 1.5rem;
    }
    
    .library-item:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }
    
    .library-icon {
        font-size: 3rem;
        color: var(--primary-blue);
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--background);
        border-radius: var(--radius-md);
        flex-shrink: 0;
    }
    
    .library-content {
        flex: 1;
    }
    
    .library-actions {
        display: flex;
        gap: 0.5rem;
        flex-direction: column;
        justify-content: center;
    }
</style>

<!-- Header -->
<div class="library-header">
    <div class="container">
        <h1><i class="fas fa-book-reader"></i> Digital Library</h1>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.9);">
            Access books, notes, and question banks for your studies
        </p>
    </div>
</div>

<!-- Main Content -->
<div class="library-container">
    <!-- Filters Sidebar -->
    <aside class="filters-sidebar">
        <div class="filter-card">
            <h3><i class="fas fa-filter"></i> Resource Type</h3>
            <a href="library.php" class="filter-option <?php echo !$category ? 'active' : ''; ?>">
                All Resources
            </a>
            <?php
            $categories = ['Books', 'Notes', 'Question Bank'];
            foreach ($categories as $cat):
            ?>
                <a href="library.php?category=<?php echo urlencode($cat); ?><?php echo $course_category ? '&course_category=' . $course_category : ''; ?>" 
                   class="filter-option <?php echo $category === $cat ? 'active' : ''; ?>">
                    <i class="fas fa-<?php echo $cat === 'Books' ? 'book' : ($cat === 'Notes' ? 'file-alt' : 'question-circle'); ?>"></i>
                    <?php echo $cat; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="filter-card">
            <h3><i class="fas fa-graduation-cap"></i> Subject</h3>
            <a href="library.php<?php echo $category ? '?category=' . $category : ''; ?>" 
               class="filter-option <?php echo !$course_category ? 'active' : ''; ?>">
                All Subjects
            </a>
            <?php
            $subjects = ['CSIT', 'BCA', 'Engineering', 'Entrance Prep', 'General'];
            foreach ($subjects as $subj):
            ?>
                <a href="library.php?<?php echo $category ? 'category=' . $category . '&' : ''; ?>course_category=<?php echo urlencode($subj); ?>" 
                   class="filter-option <?php echo $course_category === $subj ? 'active' : ''; ?>">
                    <?php echo $subj; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="filter-card">
            <a href="library.php" class="btn btn-outline btn-block">
                <i class="fas fa-redo"></i> Clear Filters
            </a>
        </div>
    </aside>
    
    <!-- Library Items -->
    <main>
        <div class="card" style="margin-bottom: 2rem;">
            <div class="flex-between">
                <div class="results-count">
                    <strong><?php echo count($libraryItems); ?></strong> resources found
                </div>
                <form method="GET" style="display: flex; gap: 0.5rem;">
                    <input type="text" name="search" class="form-control" placeholder="Search resources..." 
                           value="<?php echo htmlspecialchars($search); ?>" style="width: 300px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php if (count($libraryItems) > 0): ?>
                <?php foreach ($libraryItems as $item): ?>
                    <div class="library-item fade-in">
                        <div class="library-icon">
                            <i class="fas fa-<?php 
                                echo $item['category'] === 'Books' ? 'book' : 
                                     ($item['category'] === 'Notes' ? 'file-alt' : 'question-circle'); 
                            ?>"></i>
                        </div>
                        <div class="library-content">
                            <div class="flex-between" style="margin-bottom: 0.5rem;">
                                <h3 style="margin: 0; font-size: 1.25rem;"><?php echo $item['title']; ?></h3>
                                <div style="display: flex; gap: 0.5rem;">
                                    <span class="badge badge-primary"><?php echo $item['category']; ?></span>
                                    <span class="badge badge-info"><?php echo $item['course_category']; ?></span>
                                </div>
                            </div>
                            <?php if ($item['description']): ?>
                                <p style="color: var(--gray); margin-bottom: 0.75rem; line-height: 1.6;">
                                    <?php echo $item['description']; ?>
                                </p>
                            <?php endif; ?>
                            <div style="display: flex; gap: 1.5rem; color: var(--gray); font-size: 0.9rem;">
                                <span><i class="fas fa-user"></i> <?php echo $item['uploaded_by_name']; ?></span>
                                <span><i class="fas fa-download"></i> <?php echo $item['download_count']; ?> downloads</span>
                                <span><i class="fas fa-calendar"></i> <?php echo formatDate($item['created_at']); ?></span>
                                <span><i class="fas fa-file"></i> <?php echo round($item['file_size'] / 1024 / 1024, 2); ?> MB</span>
                            </div>
                        </div>
                        <div class="library-actions">
                            <a href="download.php?file=<?php echo $item['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <button class="btn btn-outline" onclick="alert('Preview feature coming soon!')">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 5rem 2rem;">
                    <i class="fas fa-book-open" style="font-size: 5rem; color: var(--gray); opacity: 0.3;"></i>
                    <h3 style="margin-top: 2rem; color: var(--gray);">No resources found</h3>
                    <p style="color: var(--gray); margin-bottom: 1.5rem;">Try adjusting your filters or search terms</p>
                    <a href="library.php" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Clear Filters
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
