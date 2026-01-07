<?php
/**
 * Student Notifications Page
 * Study Hub LMS - View all notifications
 */

require_once '../config/config.php';
requireRole('student');

$db = new Database();
$conn = $db->getConnection();
$studentId = getCurrentUserId();

// Mark all as read if requested
if (isset($_GET['mark_all_read'])) {
    $updateQuery = "UPDATE notifications SET is_read = TRUE WHERE user_id = :user_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->execute([':user_id' => $studentId]);
    header('Location: notifications.php');
    exit();
}

// Get all notifications
$notificationsQuery = "SELECT * FROM notifications 
                       WHERE user_id = :student_id 
                       ORDER BY created_at DESC";
$stmt = $conn->prepare($notificationsQuery);
$stmt->execute([':student_id' => $studentId]);
$notifications = $stmt->fetchAll();

$pageTitle = 'Notifications - Study Hub';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="flex-between">
                <div>
                    <h1><i class="fas fa-bell"></i> Notifications</h1>
                    <p>Stay updated with your learning activities</p>
                </div>
                <a href="?mark_all_read=1" class="btn btn-outline">
                    <i class="fas fa-check-double"></i> Mark All as Read
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if (count($notifications) > 0): ?>
                    <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>" 
                         style="padding: 1rem; border-left: 3px solid <?php echo !$notification['is_read'] ? 'var(--warning)' : 'var(--primary-blue)'; ?>; 
                         background: <?php echo !$notification['is_read'] ? '#FEF3C7' : 'var(--background)'; ?>; 
                         margin-bottom: 0.75rem; border-radius: 0 var(--radius-sm) var(--radius-sm) 0; transition: all 0.2s ease;">
                        <div class="flex-between">
                            <div style="flex: 1;">
                                <div class="flex-between" style="margin-bottom: 0.5rem;">
                                    <h4 style="margin: 0; font-size: 1rem; color: var(--dark-text);">
                                        <?php
                                        $typeIcons = [
                                            'info' => 'info-circle',
                                            'success' => 'check-circle',
                                            'warning' => 'exclamation-triangle',
                                            'error' => 'times-circle'
                                        ];
                                        $icon = $typeIcons[$notification['type']] ?? 'bell';
                                        ?>
                                        <i class="fas fa-<?php echo $icon; ?>" style="color: var(--primary-blue);"></i>
                                        <?php echo htmlspecialchars($notification['title']); ?>
                                    </h4>
                                    <span style="color: var(--gray); font-size: 0.85rem; white-space: nowrap; margin-left: 1rem;">
                                        <?php echo timeAgo($notification['created_at']); ?>
                                    </span>
                                </div>
                                <p style="color: var(--gray); font-size: 0.9rem; margin: 0.5rem 0 0 0;">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem 1rem;">
                        <i class="fas fa-bell-slash" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem; color: var(--dark-text);">No Notifications</h3>
                        <p style="color: var(--gray);">You're all caught up!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<style>
    .notification-item:hover {
        background: #D1E9F6 !important;
    }
</style>

<?php
logActivity(getCurrentUserId(), 'view_notifications', 'Viewed notifications page');
?>
