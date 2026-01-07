<?php
/**
 * Teacher Assignments Page
 * Study Hub LMS - Manage assignments
 */

require_once '../config/config.php';
requireRole('teacher');

$db = new Database();
$conn = $db->getConnection();
$teacherId = getCurrentUserId();

// Get teacher's assignments
$assignmentsQuery = "SELECT a.*, c.title as course_title,
                     (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id) as submission_count,
                     (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id AND status = 'submitted') as pending_count
                     FROM assignments a
                     JOIN courses c ON a.course_id = c.id
                     WHERE c.teacher_id = :teacher_id
                     ORDER BY a.due_date DESC";
$stmt = $conn->prepare($assignmentsQuery);
$stmt->execute([':teacher_id' => $teacherId]);
$assignments = $stmt->fetchAll();

$pageTitle = 'Assignments - Teacher Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="flex-between">
                <div>
                    <h1><i class="fas fa-tasks"></i> Assignments</h1>
                    <p>Manage course assignments</p>
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Assignment
                </button>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if (count($assignments) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Due Date</th>
                                <th>Total Marks</th>
                                <th>Submissions</th>
                                <th>Pending</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($assignment['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($assignment['course_title']); ?></td>
                                <td><?php echo formatDateTime($assignment['due_date']); ?></td>
                                <td><?php echo $assignment['total_marks'] ?? 'N/A'; ?></td>
                                <td><?php echo $assignment['submission_count']; ?></td>
                                <td>
                                    <?php if ($assignment['pending_count'] > 0): ?>
                                        <span class="badge badge-warning"><?php echo $assignment['pending_count']; ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-success">0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="view-submissions.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-clipboard-list" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem;">No assignments yet</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_assignments', 'Viewed assignments page');
?>
