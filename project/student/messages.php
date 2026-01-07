<?php
/**
 * Student Messages Page
 * Study Hub LMS - View and send messages
 */

require_once '../config/config.php';
requireRole('student');

$db = new Database();
$conn = $db->getConnection();
$studentId = getCurrentUserId();

// Get messages
$messagesQuery = "SELECT m.*, 
                  sender.full_name as sender_name, sender.role as sender_role,
                  receiver.full_name as receiver_name
                  FROM messages m
                  JOIN users sender ON m.sender_id = sender.id
                  JOIN users receiver ON m.receiver_id = receiver.id
                  WHERE m.sender_id = :user_id OR m.receiver_id = :user_id
                  ORDER BY m.created_at DESC
                  LIMIT 50";
$stmt = $conn->prepare($messagesQuery);
$stmt->execute([':user_id' => $studentId]);
$messages = $stmt->fetchAll();

// Get unread count
$unreadQuery = "SELECT COUNT(*) FROM messages WHERE receiver_id = :user_id AND is_read = FALSE";
$stmt = $conn->prepare($unreadQuery);
$stmt->execute([':user_id' => $studentId]);
$unreadCount = $stmt->fetchColumn();

$pageTitle = 'Messages - Study Hub';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="flex-between">
                <div>
                    <h1><i class="fas fa-envelope"></i> Messages</h1>
                    <p>Communicate with teachers and admin</p>
                </div>
                <div>
                    <?php if ($unreadCount > 0): ?>
                        <span class="badge badge-danger" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            <?php echo $unreadCount; ?> Unread
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if (count($messages) > 0): ?>
                    <?php foreach ($messages as $message): ?>
                    <?php
                    $isReceived = $message['receiver_id'] == $studentId;
                    $otherPerson = $isReceived ? $message['sender_name'] : $message['receiver_name'];
                    $otherRole = $message['sender_role'];
                    ?>
                    <div class="<?php echo !$isReceived ? 'message-item sent' : 'message-item'; ?>" 
                         style="padding: 1rem; border-left: 3px solid <?php echo !$message['is_read'] && $isReceived ? 'var(--warning)' : 'var(--primary-blue)'; ?>; 
                         background: <?php echo !$message['is_read'] && $isReceived ? '#FEF3C7' : 'var(--background)'; ?>; 
                         margin-bottom: 1rem; border-radius: 0 var(--radius-sm) var(--radius-sm) 0;">
                        <div class="flex-between" style="margin-bottom: 0.5rem;">
                            <div>
                                <strong style="color: var(--dark-text);">
                                    <?php echo $isReceived ? 'From: ' : 'To: '; ?>
                                    <?php echo htmlspecialchars($otherPerson); ?>
                                </strong>
                                <span class="badge badge-info" style="margin-left: 0.5rem; font-size: 0.75rem;">
                                    <?php echo ucfirst($otherRole); ?>
                                </span>
                            </div>
                            <span style="color: var(--gray); font-size: 0.85rem;">
                                <?php echo timeAgo($message['created_at']); ?>
                            </span>
                        </div>
                        <h4 style="margin: 0.5rem 0; font-size: 1rem;">
                            <?php echo htmlspecialchars($message['subject']); ?>
                        </h4>
                        <p style="color: var(--gray); margin: 0.5rem 0;">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </p>
                        <?php if (!$message['is_read'] && $isReceived): ?>
                            <span class="badge badge-warning" style="margin-top: 0.5rem;">
                                <i class="fas fa-envelope"></i> Unread
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem 1rem;">
                        <i class="fas fa-inbox" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem; color: var(--dark-text);">No Messages</h3>
                        <p style="color: var(--gray);">Your messages will appear here</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<style>
    .message-item {
        transition: all 0.2s ease;
    }
    .message-item:hover {
        background: #D1E9F6 !important;
    }
</style>

<?php
logActivity(getCurrentUserId(), 'view_messages', 'Viewed messages page');
?>
