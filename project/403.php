<?php
/**
 * 403 Forbidden Page
 * Study Hub LMS
 */

require_once 'config/config.php';
$pageTitle = '403 Forbidden - Study Hub';
include 'includes/header.php';
?>

<style>
    body {
        background: linear-gradient(135deg, #0A8BCB 0%, #1E9ED8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
        padding: 20px;
    }
    
    .error-container {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        text-align: center;
        max-width: 600px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    
    .error-icon {
        font-size: 6rem;
        color: #EF4444;
        margin-bottom: 1rem;
    }
    
    .error-code {
        font-size: 4rem;
        font-weight: 700;
        color: var(--primary-blue);
        margin: 0;
    }
    
    .error-message {
        font-size: 1.5rem;
        color: var(--dark-text);
        margin: 1rem 0;
    }
    
    .error-description {
        color: var(--gray);
        margin-bottom: 2rem;
    }
    
    .btn-home {
        display: inline-block;
        padding: 0.75rem 2rem;
        background: var(--primary-blue);
        color: white;
        text-decoration: none;
        border-radius: var(--radius-md);
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-home:hover {
        background: var(--secondary-blue);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(10, 139, 203, 0.3);
    }
</style>

<div class="error-container">
    <div class="error-icon">
        <i class="fas fa-ban"></i>
    </div>
    <h1 class="error-code">403</h1>
    <h2 class="error-message">Access Forbidden</h2>
    <p class="error-description">
        You don't have permission to access this page. This usually happens when you try to access a page that requires different user privileges.
    </p>
    <a href="<?php echo SITE_URL; ?>/index.php" class="btn-home">
        <i class="fas fa-home"></i> Go to Home
    </a>
    <?php if (isLoggedIn()): ?>
    <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="btn-home" style="background: #6B7280; margin-left: 10px;">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
