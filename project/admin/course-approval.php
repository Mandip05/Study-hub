<?php
/**
 * Admin Course Approval Page
 * Study Hub LMS - Approve or reject course submissions
 */

require_once '../config/config.php';
requireRole('admin');

$db = new Database();
$conn = $db->getConnection();

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $courseId = $_POST['course_id'];
    $action = $_POST['action'];
    $status = $action === 'approve' ? 'approved' : 'rejected';
    
    $updateQuery = "UPDATE courses SET approval_status = :status WHERE id = :id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->execute([':status' => $status, ':id' => $courseId]);
    
    logActivity(getCurrentUserId(), 'course_' . $action, "Course ID: $courseId");
    header('Location: course-approval.php');
    exit();
}

// Get pending courses
$coursesQuery = "SELECT c.*, u.full_name as teacher_name, u.email as teacher_email
                 FROM courses c
                 JOIN users u ON c.teacher_id = u.id
                 WHERE c.approval_status = 'pending'
                 ORDER BY c.created_at ASC";
$stmt = $conn->prepare($coursesQuery);
$stmt->execute();
$pendingCourses = $stmt->fetchAll();

$pageTitle = 'Course Approval - Admin Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-check-circle"></i> Course Approval</h1>
            <p>Review and approve course submissions</p>
        </div>
        
        <?php if (count($pendingCourses) > 0): ?>
            <div class="grid grid-cols-2" style="gap: 2rem;">
                <?php foreach ($pendingCourses as $course): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Teacher:</strong> <?php echo htmlspecialchars($course['teacher_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($course['teacher_email']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($course['category']); ?></p>
                        <p><strong>Description:</strong></p>
                        <p style="color: var(--gray);"><?php echo nl2br(htmlspecialchars(substr($course['description'], 0, 200))); ?>...</p>
                        <p><strong>Submitted:</strong> <?php echo timeAgo($course['created_at']); ?></p>
                        
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <form method="POST" style="flex: 1;">
                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-success" style="width: 100%;">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <form method="POST" style="flex: 1;">
                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-danger" style="width: 100%;">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 4rem;">
                <i class="fas fa-check-double" style="font-size: 5rem; color: var(--success); opacity: 0.3;"></i>
                <h3 style="margin-top: 1.5rem;">All Caught Up!</h3>
                <p style="color: var(--gray);">No pending course approvals</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_course_approval', 'Viewed course approval page');
?>
