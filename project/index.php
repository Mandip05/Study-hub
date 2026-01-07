<?php
/**
 * Landing Page / Home
 * Study Hub LMS
 */

require_once 'config/config.php';

// Fetch popular courses
$db = new Database();
$conn = $db->getConnection();

$coursesQuery = "SELECT c.*, u.full_name as teacher_name, 
                 (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrolled_count
                 FROM courses c 
                 JOIN users u ON c.teacher_id = u.id 
                 WHERE c.status = 'published' AND c.approval_status = 'approved'
                 ORDER BY enrolled_count DESC 
                 LIMIT 6";
$coursesStmt = $conn->prepare($coursesQuery);
$coursesStmt->execute();
$popularCourses = $coursesStmt->fetchAll();

$pageTitle = 'Study Hub - Modern Learning Management System';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #0A8BCB 0%, #1E9ED8 100%);
        color: white;
        padding: 6rem 0;
        position: relative;
        overflow: hidden;
    }
    
    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path fill="%23ffffff" fill-opacity="0.1" d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"></path></svg>');
        background-size: cover;
        opacity: 0.1;
    }
    
    .hero-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
        position: relative;
        z-index: 1;
    }
    
    .hero-text h1 {
        font-size: 3.5rem;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: white;
    }
    
    .hero-text p {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.6;
    }
    
    .hero-buttons {
        display: flex;
        gap: 1rem;
    }
    
    .hero-buttons .btn {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }
    
    .btn-white {
        background: white;
        color: var(--primary-blue);
    }
    
    .btn-white:hover {
        background: var(--background);
    }
    
    .hero-image {
        position: relative;
    }
    
    .hero-image img {
        width: 100%;
        border-radius: var(--radius-xl);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    
    /* Search Bar */
    .hero-search {
        display: flex;
        background: white;
        border-radius: var(--radius-full);
        padding: 0.5rem;
        box-shadow: var(--shadow-xl);
        margin-top: 2rem;
    }
    
    .hero-search input {
        flex: 1;
        border: none;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        outline: none;
    }
    
    .hero-search button {
        padding: 1rem 2rem;
        border-radius: var(--radius-full);
    }
    
    /* Categories Section */
    .categories {
        padding: 4rem 0;
        background: white;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .section-header h2 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .section-header p {
        color: var(--gray);
        font-size: 1.1rem;
    }
    
    .category-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    .category-card {
        background: var(--background);
        padding: 2rem;
        border-radius: var(--radius-lg);
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary-blue);
        background: white;
        box-shadow: var(--shadow-lg);
    }
    
    .category-icon {
        font-size: 3rem;
        color: var(--primary-blue);
        margin-bottom: 1rem;
    }
    
    .category-card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    .category-card p {
        color: var(--gray);
        font-size: 0.9rem;
    }
    
    /* Popular Courses Section */
    .popular-courses {
        padding: 4rem 0;
        background: var(--background);
    }
    
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    /* Stats Section */
    .stats {
        background: var(--dark-text);
        color: white;
        padding: 4rem 0;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 3rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        text-align: center;
    }
    
    .stat-item h3 {
        font-size: 3rem;
        color: var(--secondary-blue);
        margin-bottom: 0.5rem;
    }
    
    .stat-item p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.1rem;
    }
    
    /* CTA Section */
    .cta {
        background: linear-gradient(135deg, #0A8BCB 0%, #1E9ED8 100%);
        color: white;
        padding: 5rem 0;
        text-align: center;
    }
    
    .cta h2 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: white;
    }
    
    .cta p {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        color: rgba(255, 255, 255, 0.9);
    }
    
    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .category-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .courses-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .hero-text h1 {
            font-size: 2.5rem;
        }
        
        .category-grid,
        .courses-grid,
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .hero-buttons {
            flex-direction: column;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-text fade-in">
            <h1>Learn Without Limits</h1>
            <p>Access high-quality courses, connect with expert teachers, and achieve your academic goals with Study Hub's modern learning platform.</p>
            
            <div class="hero-buttons">
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo getCurrentUserRole(); ?>/dashboard.php" class="btn btn-white btn-lg">
                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                    </a>
                <?php else: ?>
                    <a href="auth/register.php" class="btn btn-white btn-lg">
                        <i class="fas fa-user-plus"></i> Get Started Free
                    </a>
                    <a href="courses.php" class="btn btn-outline btn-lg" style="border-color: white; color: white;">
                        <i class="fas fa-book"></i> Explore Courses
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="hero-search">
                <input type="text" placeholder="Search for courses, topics, or skills..." id="heroSearch">
                <button class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
        
        <div class="hero-image fade-in" style="animation-delay: 0.2s;">
            <div style="background: linear-gradient(135deg, rgba(10, 139, 203, 0.2), rgba(30, 158, 216, 0.2)); padding: 3rem; border-radius: var(--radius-xl); text-align: center;">
                <i class="fas fa-laptop-code" style="font-size: 8rem; color: white; opacity: 0.9;"></i>
                <h3 style="color: white; margin-top: 2rem; font-size: 1.5rem;">Modern Learning Platform</h3>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories">
    <div class="section-header">
        <h2>Browse by Category</h2>
        <p>Choose from our diverse range of academic programs</p>
    </div>
    
    <div class="category-grid">
        <a href="courses.php?category=CSIT" class="category-card">
            <div class="category-icon">
                <i class="fas fa-laptop-code"></i>
            </div>
            <h3>CSIT</h3>
            <p>Computer Science & IT courses for aspiring technologists</p>
        </a>
        
        <a href="courses.php?category=BCA" class="category-card">
            <div class="category-icon">
                <i class="fas fa-code"></i>
            </div>
            <h3>BCA</h3>
            <p>Bachelor of Computer Applications program</p>
        </a>
        
        <a href="courses.php?category=Engineering" class="category-card">
            <div class="category-icon">
                <i class="fas fa-cogs"></i>
            </div>
            <h3>Engineering</h3>
            <p>Engineering fundamentals and advanced topics</p>
        </a>
        
        <a href="courses.php?category=Entrance%20Prep" class="category-card">
            <div class="category-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h3>Entrance Prep</h3>
            <p>Comprehensive entrance exam preparation</p>
        </a>
    </div>
</section>

<!-- Popular Courses Section -->
<section class="popular-courses">
    <div class="section-header">
        <h2>Popular Courses</h2>
        <p>Join thousands of students in our most popular courses</p>
    </div>
    
    <div class="courses-grid">
        <?php if (count($popularCourses) > 0): ?>
            <?php foreach ($popularCourses as $course): ?>
                <div class="course-card">
                    <div class="course-thumbnail">
                        <?php if ($course['thumbnail']): ?>
                            <img src="<?php echo SITE_URL . '/' . $course['thumbnail']; ?>" alt="<?php echo $course['title']; ?>">
                        <?php endif; ?>
                    </div>
                    <div class="course-content">
                        <span class="course-category"><?php echo $course['category']; ?></span>
                        <h3 class="course-title"><?php echo $course['title']; ?></h3>
                        <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 1rem;">
                            <?php echo substr($course['description'], 0, 100); ?>...
                        </p>
                        <div class="course-meta">
                            <span><i class="fas fa-user"></i> <?php echo $course['teacher_name']; ?></span>
                            <span><i class="fas fa-users"></i> <?php echo $course['enrolled_count']; ?> enrolled</span>
                        </div>
                        <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                            View Course
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                <i class="fas fa-book" style="font-size: 4rem; color: var(--gray); opacity: 0.5;"></i>
                <h3 style="margin-top: 1rem; color: var(--gray);">No courses available yet</h3>
                <p style="color: var(--gray);">Check back soon for new courses!</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 3rem;">
        <a href="courses.php" class="btn btn-outline btn-lg">
            <i class="fas fa-book-open"></i> View All Courses
        </a>
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="stats-grid">
        <div class="stat-item fade-in">
            <h3>1,000+</h3>
            <p>Active Students</p>
        </div>
        <div class="stat-item fade-in" style="animation-delay: 0.1s;">
            <h3>50+</h3>
            <p>Expert Teachers</p>
        </div>
        <div class="stat-item fade-in" style="animation-delay: 0.2s;">
            <h3>100+</h3>
            <p>Quality Courses</p>
        </div>
        <div class="stat-item fade-in" style="animation-delay: 0.3s;">
            <h3>95%</h3>
            <p>Success Rate</p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <h2>Ready to Start Learning?</h2>
        <p>Join Study Hub today and unlock your potential</p>
        <?php if (!isLoggedIn()): ?>
            <a href="auth/register.php" class="btn btn-white btn-lg">
                <i class="fas fa-rocket"></i> Get Started Now
            </a>
        <?php endif; ?>
    </div>
</section>

<script>
    // Hero search functionality
    document.getElementById('heroSearch')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                window.location.href = 'courses.php?search=' + encodeURIComponent(query);
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
