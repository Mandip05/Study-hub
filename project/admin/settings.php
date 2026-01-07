<?php
/**
 * Admin Settings Page
 * Study Hub LMS - System settings
 */

require_once '../config/config.php';
requireRole('admin');

$db = new Database();
$conn = $db->getConnection();

$pageTitle = 'Settings - Admin Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-cog"></i> System Settings</h1>
            <p>Configure system parameters</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">General Settings</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Site Name</label>
                        <input type="text" class="form-control" value="Study Hub" name="site_name">
                    </div>
                    
                    <div class="form-group">
                        <label>Site Email</label>
                        <input type="email" class="form-control" value="admin@studyhub.com" name="site_email">
                    </div>
                    
                    <div class="form-group">
                        <label>Timezone</label>
                        <select class="form-control" name="timezone">
                            <option value="Asia/Kathmandu" selected>Asia/Kathmandu</option>
                            <option value="UTC">UTC</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Language</label>
                        <select class="form-control" name="language">
                            <option value="en" selected>English</option>
                            <option value="ne">Nepali</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_settings', 'Viewed settings page');
?>
