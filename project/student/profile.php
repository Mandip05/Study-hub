<?php
/**
 * Student Profile Page
 * Study Hub LMS
 */

require_once '../config/config.php';

// Check authentication and role
checkAuth();
checkRole('student');

$studentId = $_SESSION['user_id'];
$success = '';
$error = '';

$db = new Database();
$conn = $db->getConnection();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    
    try {
        $updateQuery = "UPDATE users SET full_name = :full_name, phone = :phone, address = :address WHERE id = :id";
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute([
            ':full_name' => $fullName,
            ':phone' => $phone,
            ':address' => $address,
            ':id' => $studentId
        ]);
        
        $_SESSION['user_name'] = $fullName;
        $success = 'Profile updated successfully!';
    } catch (PDOException $e) {
        $error = 'Failed to update profile. Please try again.';
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match!';
    } else {
        try {
            $query = "SELECT password FROM users WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->execute([':id' => $studentId]);
            $user = $stmt->fetch();
            
            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE users SET password = :password WHERE id = :id";
                $stmt = $conn->prepare($updateQuery);
                $stmt->execute([':password' => $hashedPassword, ':id' => $studentId]);
                
                $success = 'Password changed successfully!';
            } else {
                $error = 'Current password is incorrect!';
            }
        } catch (PDOException $e) {
            $error = 'Failed to change password. Please try again.';
        }
    }
}

// Get user data
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->execute([':id' => $studentId]);
$user = $stmt->fetch();

$pageTitle = 'My Profile - Study Hub';
include '../includes/header.php';
?>

<style>
    .profile-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .profile-header {
        background: white;
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
        text-align: center;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 600;
        margin: 0 auto 1rem;
    }
    
    .profile-sections {
        display: grid;
        gap: 2rem;
    }
    
    .profile-section {
        background: white;
        border-radius: var(--radius-lg);
        padding: 2rem;
        box-shadow: var(--shadow-md);
    }
    
    .profile-section h2 {
        margin-bottom: 1.5rem;
        color: var(--dark-text);
        border-bottom: 2px solid var(--primary-blue);
        padding-bottom: 0.5rem;
    }
</style>

<?php include '../includes/navbar.php'; ?>

<div class="profile-container">
    <div class="profile-header fade-in">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
        </div>
        <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
        <p style="color: var(--gray); margin-top: 0.5rem;">
            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?>
        </p>
        <span class="badge badge-primary" style="margin-top: 1rem;">Student</span>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success fade-in">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error fade-in">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-sections">
        <!-- Personal Information -->
        <div class="profile-section fade-in">
            <h2><i class="fas fa-user"></i> Personal Information</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <small style="color: var(--gray);">Email cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Enter your phone number">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Enter your address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" name="update_profile" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>
        
        <!-- Change Password -->
        <div class="profile-section fade-in">
            <h2><i class="fas fa-lock"></i> Change Password</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" minlength="6" required>
                    <small style="color: var(--gray);">Minimum 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                </div>
                
                <button type="submit" name="change_password" class="btn btn-primary">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </form>
        </div>
        
        <!-- Account Information -->
        <div class="profile-section fade-in">
            <h2><i class="fas fa-info-circle"></i> Account Information</h2>
            <div class="info-grid" style="display: grid; gap: 1rem;">
                <div>
                    <strong>Account Status:</strong>
                    <span class="badge badge-success" style="margin-left: 0.5rem;"><?php echo ucfirst($user['status']); ?></span>
                </div>
                <div>
                    <strong>Member Since:</strong>
                    <span style="color: var(--gray); margin-left: 0.5rem;"><?php echo formatDate($user['created_at']); ?></span>
                </div>
                <div>
                    <strong>Last Login:</strong>
                    <span style="color: var(--gray); margin-left: 0.5rem;">
                        <?php echo $user['last_login'] ? formatDateTime($user['last_login']) : 'Never'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
