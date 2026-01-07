<?php
/**
 * About Page
 * Study Hub LMS
 */

require_once 'config/config.php';

$pageTitle = 'About Us - Study Hub';
$pageDescription = 'Learn more about Study Hub - Modern Learning Management System';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
    /* About Hero Section */
    .about-hero {
        background: linear-gradient(135deg, #0A8BCB 0%, #1E9ED8 100%);
        color: white;
        padding: 4rem 0;
        text-align: center;
    }
    
    .about-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: white;
    }
    
    .about-hero p {
        font-size: 1.25rem;
        max-width: 800px;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.9);
    }
    
    /* Content Sections */
    .about-section {
        max-width: 1200px;
        margin: 4rem auto;
        padding: 0 2rem;
    }
    
    .mission-vision {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin: 3rem 0;
    }
    
    .mission-box, .vision-box {
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
    }
    
    .mission-box h2, .vision-box h2 {
        color: var(--primary-blue);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .mission-box i, .vision-box i {
        font-size: 1.5rem;
    }
    
    /* Features Grid */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin: 3rem 0;
    }
    
    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        text-align: center;
        transition: var(--transition);
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .feature-card i {
        font-size: 3rem;
        color: var(--primary-blue);
        margin-bottom: 1rem;
    }
    
    .feature-card h3 {
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }
    
    .feature-card p {
        color: var(--text-secondary);
        line-height: 1.6;
    }
    
    /* Stats Section */
    .stats-section {
        background: var(--background);
        padding: 4rem 2rem;
        text-align: center;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 2rem auto 0;
    }
    
    .stat-card {
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
    }
    
    .stat-card .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: var(--primary-blue);
        margin-bottom: 0.5rem;
    }
    
    .stat-card .stat-label {
        color: var(--text-secondary);
        font-size: 1rem;
    }
    
    /* Team Section */
    .team-section {
        max-width: 1200px;
        margin: 4rem auto;
        padding: 0 2rem;
    }
    
    .team-section h2 {
        text-align: center;
        margin-bottom: 3rem;
        color: var(--text-primary);
    }
    
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }
    
    .team-member {
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        text-align: center;
    }
    
    .team-member .avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: var(--primary-blue);
        color: white;
        font-size: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }
    
    .team-member h3 {
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }
    
    .team-member .role {
        color: var(--primary-blue);
        font-weight: 500;
        margin-bottom: 1rem;
    }
    
    .team-member p {
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.6;
    }
</style>

<!-- Hero Section -->
<div class="about-hero">
    <div class="container">
        <h1><i class="fas fa-graduation-cap"></i> About Study Hub</h1>
        <p>Empowering learners and educators through innovative technology and collaborative learning experiences</p>
    </div>
</div>

<!-- Mission & Vision -->
<section class="about-section">
    <div class="mission-vision">
        <div class="mission-box">
            <h2><i class="fas fa-bullseye"></i> Our Mission</h2>
            <p>To provide accessible, high-quality education through a modern learning management system that connects students and teachers worldwide. We believe in making education engaging, interactive, and available to everyone.</p>
        </div>
        <div class="vision-box">
            <h2><i class="fas fa-eye"></i> Our Vision</h2>
            <p>To revolutionize online learning by creating a platform that fosters creativity, collaboration, and continuous growth. We envision a future where education knows no boundaries.</p>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="about-section">
    <h2 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary);">Why Choose Study Hub?</h2>
    <div class="features-grid">
        <div class="feature-card">
            <i class="fas fa-book-reader"></i>
            <h3>Interactive Learning</h3>
            <p>Engage with courses through videos, quizzes, and interactive materials designed for effective learning.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-users"></i>
            <h3>Expert Teachers</h3>
            <p>Learn from experienced educators who are passionate about sharing their knowledge.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-library"></i>
            <h3>Digital Library</h3>
            <p>Access a comprehensive collection of educational resources, books, and research materials.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-certificate"></i>
            <h3>Certifications</h3>
            <p>Earn certificates upon course completion to showcase your achievements.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-mobile-alt"></i>
            <h3>Mobile Friendly</h3>
            <p>Learn anywhere, anytime with our responsive platform accessible on all devices.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-chart-line"></i>
            <h3>Progress Tracking</h3>
            <p>Monitor your learning journey with detailed analytics and progress reports.</p>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <h2 style="margin-bottom: 1rem; color: var(--text-primary);">Study Hub in Numbers</h2>
    <p style="color: var(--text-secondary); margin-bottom: 2rem;">Making an impact in online education</p>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">10,000+</div>
            <div class="stat-label">Active Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">500+</div>
            <div class="stat-label">Expert Teachers</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">200+</div>
            <div class="stat-label">Quality Courses</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">5,000+</div>
            <div class="stat-label">Library Resources</div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <h2>Meet Our Team</h2>
    <div class="team-grid">
        <div class="team-member">
            <div class="avatar">
                <i class="fas fa-user"></i>
            </div>
            <h3>Development Team</h3>
            <div class="role">Platform Developers</div>
            <p>Dedicated to building and maintaining a robust learning platform with cutting-edge technology.</p>
        </div>
        <div class="team-member">
            <div class="avatar">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h3>Content Team</h3>
            <div class="role">Educational Experts</div>
            <p>Curating high-quality educational content and ensuring excellent learning experiences.</p>
        </div>
        <div class="team-member">
            <div class="avatar">
                <i class="fas fa-headset"></i>
            </div>
            <h3>Support Team</h3>
            <div class="role">Customer Success</div>
            <p>Providing round-the-clock support to ensure smooth learning journeys for all users.</p>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="about-section" style="text-align: center; padding: 4rem 2rem; background: var(--background); margin-top: 4rem;">
    <h2 style="margin-bottom: 1rem; color: var(--text-primary);">Ready to Start Learning?</h2>
    <p style="color: var(--text-secondary); margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
        Join thousands of students and teachers who are already part of the Study Hub community.
    </p>
    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        <?php if(!isLoggedIn()): ?>
            <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-primary">Get Started Free</a>
            <a href="<?php echo SITE_URL; ?>/courses.php" class="btn btn-outline">Browse Courses</a>
        <?php else: ?>
            <a href="<?php echo SITE_URL; ?>/courses.php" class="btn btn-primary">Browse Courses</a>
            <a href="<?php echo SITE_URL; ?>/library.php" class="btn btn-outline">Explore Library</a>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
