<?php
/**
 * Teacher Messages Page
 * Study Hub LMS - View and send messages
 */

require_once '../config/config.php';
requireRole('teacher');

$db = new Database();
$conn = $db->getConnection();
$teacherId = getCurrentUserId();

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
$stmt->execute([':user_id' => $teacherId]);
$messages = $stmt->fetchAll();

$pageTitle = 'Messages - Teacher Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-envelope"></i> Messages</h1>
            <p>Communicate with students and admin</p>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if (count($messages) > 0): ?>
                    <?php foreach ($messages as $message): ?>
                    <?php
                    $isReceived = $message['receiver_id'] == $teacherId;
                    $otherPerson = $isReceived ? $message['sender_name'] : $message['receiver_name'];
                    ?>
                    <div style="padding: 1rem; border-left: 3px solid <?php echo !$message['is_read'] && $isReceived ? 'var(--warning)' : 'var(--primary-blue)'; ?>; 
                         background: <?php echo !$message['is_read'] && $isReceived ? '#FEF3C7' : 'var(--background)'; ?>; 
                         margin-bottom: 1rem; border-radius: 0 var(--radius-sm) var(--radius-sm) 0;">
                        <div class="flex-between" style="margin-bottom: 0.5rem;">
                            <strong>
                                <?php echo $isReceived ? 'From: ' : 'To: '; ?>
                                <?php echo htmlspecialchars($otherPerson); ?>
                            </strong>
                            <span style="color: var(--gray); font-size: 0.85rem;">
                                <?php echo timeAgo($message['created_at']); ?>
                            </span>
                        </div>
                        <h4 style="margin: 0.5rem 0;"><?php echo htmlspecialchars($message['subject']); ?></h4>
                        <p style="color: var(--gray); margin: 0.5rem 0;">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-inbox" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem;">No Messages</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_messages', 'Viewed messages page');
?>
