<?php
/**
 * Login Page
 * Study Hub LMS
 */

require_once '../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectByRole(getCurrentUserRole());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "SELECT id, full_name, email, password, role, status FROM users WHERE email = :email";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                // Check if account is active
                if ($user['status'] !== 'active') {
                    $error = 'Your account is inactive. Please contact support.';
                } elseif (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['logged_in_time'] = time();
                    
                    // Update last login
                    $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = :id";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bindParam(':id', $user['id']);
                    $updateStmt->execute();
                    
                    // Redirect based on role
                    redirectByRole($user['role']);
                } else {
                    $error = 'Invalid email or password';
                }
            } else {
                $error = 'Invalid email or password';
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please try again later.';
        }
    }
}

$pageTitle = 'Login - Study Hub';
include '../includes/header.php';
?>

<style>
    body {
        background: linear-gradient(135deg, #0A8BCB 0%, #1E9ED8 100%);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .auth-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }
    
    .auth-container {
        width: 100%;
        max-width: 420px;
        padding: 1rem;
    }
    
    footer {
        margin-top: 0 !important;
    }
    
    .auth-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    
    .auth-logo {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .auth-logo i {
        font-size: 2.5rem;
        color: var(--primary-blue);
        margin-bottom: 0.5rem;
    }
    
    .auth-logo h1 {
        font-size: 1.75rem;
        color: var(--dark-text);
        margin-bottom: 0.5rem;
    }
    
    .auth-logo p {
        color: var(--gray);
        font-size: 0.95rem;
    }
    
    .auth-divider {
        text-align: center;
        margin: 1.25rem 0;
        position: relative;
    }
    
    .auth-divider::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 100%;
        height: 1px;
        background: #E5E7EB;
    }
    
    .auth-divider span {
        background: white;
        padding: 0 1rem;
        position: relative;
        color: var(--gray);
        font-size: 0.9rem;
    }
    
    .demo-accounts {
        background: var(--background);
        padding: 1rem;
        border-radius: var(--radius-md);
        margin-top: 1.5rem;
        font-size: 0.85rem;
        display: none;
    }
    
    .demo-accounts h4 {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        color: var(--dark-text);
    }
    
    .demo-accounts p {
        margin: 0.25rem 0;
        color: var(--gray);
    }
</style>

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-card fade-in">
        <div class="auth-logo">
            <i class="fas fa-graduation-cap"></i>
            <h1>Welcome Back</h1>
            <p>Login to access your Study Hub account</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="your.email@example.com"
                    value="<?php echo $email ?? ''; ?>"
                    required
                >
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Enter your password"
                    required
                >
            </div>
            
            <div class="flex-between" style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="forgot-password.php" style="font-size: 0.9rem;">Forgot Password?</a>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="auth-divider">
            <span>Don't have an account?</span>
        </div>
        
        <a href="register.php" class="btn btn-outline btn-block">
            <i class="fas fa-user-plus"></i> Create Account
        </a>
        
        <div class="demo-accounts">
            <h4>Demo Accounts:</h4>
            <p><strong>Admin:</strong> admin@studyhub.com / admin123</p>
            <p><strong>Teacher:</strong> teacher@studyhub.com / admin123</p>
            <p><strong>Student:</strong> student@studyhub.com / admin123</p>
        </div>
    </div>
</div>
</div>

<?php include '../includes/footer.php'; ?>
