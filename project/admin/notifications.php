<?php
/**
 * Admin Notifications Page
 * Study Hub LMS - Send system notifications
 */

require_once '../config/config.php';
requireRole('admin');

$db = new Database();
$conn = $db->getConnection();

$pageTitle = 'Notifications - Admin Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-bell"></i> Send Notifications</h1>
            <p>Send notifications to users</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Broadcast Notification</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Recipient Group</label>
                        <select class="form-control" name="recipient_group">
                            <option value="all">All Users</option>
                            <option value="students">All Students</option>
                            <option value="teachers">All Teachers</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <textarea class="form-control" name="message" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" name="type">
                            <option value="info">Info</option>
                            <option value="success">Success</option>
                            <option value="warning">Warning</option>
                            <option value="error">Error</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Notification
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_notifications', 'Viewed notifications page');
?>
