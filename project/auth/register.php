<?php
/**
 * Registration Page
 * Study Hub LMS
 */

require_once '../config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectByRole(getCurrentUserRole());
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = sanitize($_POST['role'] ?? 'student');
    
    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            // Check if email already exists
            $checkQuery = "SELECT id FROM users WHERE email = :email";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                $error = 'Email already registered';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $insertQuery = "INSERT INTO users (full_name, email, password, role, status) VALUES (:full_name, :email, :password, :role, 'active')";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bindParam(':full_name', $full_name);
                $insertStmt->bindParam(':email', $email);
                $insertStmt->bindParam(':password', $hashed_password);
                $insertStmt->bindParam(':role', $role);
                
                if ($insertStmt->execute()) {
                    $success = 'Account created successfully! You can now login.';
                    
                    // Auto login
                    $user_id = $conn->lastInsertId();
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $full_name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_role'] = $role;
                    $_SESSION['logged_in_time'] = time();
                    
                    // Redirect after 2 seconds
                    header("refresh:2;url=" . SITE_URL . "/" . $role . "/dashboard.php");
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please try again later.';
        }
    }
}

$pageTitle = 'Register - Study Hub';
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
        max-width: 500px;
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
    
    .role-selector {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .role-option {
        position: relative;
    }
    
    .role-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .role-option label {
        display: block;
        padding: 1rem;
        border: 2px solid #E5E7EB;
        border-radius: var(--radius-md);
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .role-option label i {
        font-size: 2rem;
        color: var(--gray);
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .role-option label span {
        display: block;
        font-weight: 500;
        color: var(--dark-text);
    }
    
    .role-option input[type="radio"]:checked + label {
        border-color: var(--primary-blue);
        background: var(--background);
    }
    
    .role-option input[type="radio"]:checked + label i {
        color: var(--primary-blue);
    }
    
    .auth-divider {
        text-align: center;
        margin: 1.5rem 0;
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
</style>

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-card fade-in">
            <div class="auth-logo">
                <i class="fas fa-graduation-cap"></i>
                <h1>Create Account</h1>
                <p>Join Study Hub and start learning today</p>
            </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="registerForm">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input 
                    type="text" 
                    name="full_name" 
                    class="form-control" 
                    placeholder="John Doe"
                    value="<?php echo $full_name ?? ''; ?>"
                    required
                >
            </div>
            
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
                <label class="form-label">I want to join as:</label>
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" name="role" value="student" id="roleStudent" checked>
                        <label for="roleStudent">
                            <i class="fas fa-user-graduate"></i>
                            <span>Student</span>
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" name="role" value="teacher" id="roleTeacher">
                        <label for="roleTeacher">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Teacher</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="At least 6 characters"
                    required
                >
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input 
                    type="password" 
                    name="confirm_password" 
                    class="form-control" 
                    placeholder="Re-enter your password"
                    required
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>
        
        <div class="auth-divider">
            <span>Already have an account?</span>
        </div>
        
        <a href="login.php" class="btn btn-outline btn-block">
            <i class="fas fa-sign-in-alt"></i> Login
        </div>
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
