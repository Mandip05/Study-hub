<?php
/**
 * Student Settings Page
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

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For now, just show success message
    // You can add actual settings functionality later
    $success = 'Settings updated successfully!';
}

// Get user data
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->execute([':id' => $studentId]);
$user = $stmt->fetch();

$pageTitle = 'Settings - Study Hub';
include '../includes/header.php';
?>

<style>
    .settings-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .settings-header {
        margin-bottom: 2rem;
    }
    
    .settings-sections {
        display: grid;
        gap: 2rem;
    }
    
    .settings-section {
        background: white;
        border-radius: var(--radius-lg);
        padding: 2rem;
        box-shadow: var(--shadow-md);
    }
    
    .settings-section h2 {
        margin-bottom: 1.5rem;
        color: var(--dark-text);
        border-bottom: 2px solid var(--primary-blue);
        padding-bottom: 0.5rem;
    }
    
    .setting-item {
        padding: 1rem;
        border-bottom: 1px solid #E5E7EB;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .setting-item:last-child {
        border-bottom: none;
    }
    
    .setting-info h4 {
        margin-bottom: 0.25rem;
        color: var(--dark-text);
    }
    
    .setting-info p {
        color: var(--gray);
        font-size: 0.9rem;
        margin: 0;
    }
</style>

<?php include '../includes/navbar.php'; ?>

<div class="settings-container">
    <div class="settings-header fade-in">
        <h1><i class="fas fa-cog"></i> Settings</h1>
        <p style="color: var(--gray); margin-top: 0.5rem;">Manage your account preferences and settings</p>
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
    
    <div class="settings-sections">
        <!-- Notification Settings -->
        <div class="settings-section fade-in">
            <h2><i class="fas fa-bell"></i> Notification Preferences</h2>
            <form method="POST" action="">
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Email Notifications</h4>
                        <p>Receive email notifications for new assignments and announcements</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="email_notifications" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Assignment Reminders</h4>
                        <p>Get reminders for upcoming assignment deadlines</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="assignment_reminders" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Grade Notifications</h4>
                        <p>Receive notifications when assignments are graded</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="grade_notifications" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div style="padding: 1rem; padding-bottom: 0;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Preferences
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Privacy Settings -->
        <div class="settings-section fade-in">
            <h2><i class="fas fa-shield-alt"></i> Privacy & Security</h2>
            <div class="setting-item">
                <div class="setting-info">
                    <h4>Profile Visibility</h4>
                    <p>Control who can see your profile information</p>
                </div>
                <select class="form-control" style="max-width: 200px;">
                    <option>Everyone</option>
                    <option>Teachers Only</option>
                    <option>Private</option>
                </select>
            </div>
            
            <div class="setting-item">
                <div class="setting-info">
                    <h4>Show Online Status</h4>
                    <p>Let others see when you're online</p>
                </div>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        
        <!-- Display Settings -->
        <div class="settings-section fade-in">
            <h2><i class="fas fa-palette"></i> Display Preferences</h2>
            <div class="setting-item">
                <div class="setting-info">
                    <h4>Theme</h4>
                    <p>Choose your preferred color theme</p>
                </div>
                <select class="form-control" style="max-width: 200px;">
                    <option selected>Light</option>
                    <option>Dark</option>
                    <option>Auto</option>
                </select>
            </div>
            
            <div class="setting-item">
                <div class="setting-info">
                    <h4>Language</h4>
                    <p>Select your preferred language</p>
                </div>
                <select class="form-control" style="max-width: 200px;">
                    <option selected>English</option>
                    <option>Spanish</option>
                    <option>French</option>
                </select>
            </div>
        </div>
        
        <!-- Danger Zone -->
        <div class="settings-section fade-in" style="border: 2px solid var(--danger);">
            <h2 style="color: var(--danger); border-color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h2>
            <div class="setting-item">
                <div class="setting-info">
                    <h4>Delete Account</h4>
                    <p>Permanently delete your account and all associated data</p>
                </div>
                <button class="btn btn-danger" onclick="alert('Please contact support to delete your account.')">
                    <i class="fas fa-trash"></i> Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: var(--primary-blue);
}

input:checked + .slider:before {
    transform: translateX(26px);
}
</style>

<?php include '../includes/footer.php'; ?>
