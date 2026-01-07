<?php
/**
 * Student Assignments Page
 * Study Hub LMS - View and submit assignments
 */

require_once '../config/config.php';
requireRole('student');

$db = new Database();
$conn = $db->getConnection();
$studentId = getCurrentUserId();

// Get all assignments from enrolled courses
$assignmentsQuery = "SELECT a.*, c.title as course_title, c.id as course_id,
                     u.full_name as teacher_name,
                     s.id as submission_id, s.file_path, 
                     s.submitted_at, s.status as submission_status, s.marks_obtained, s.feedback,
                     CASE 
                         WHEN a.deadline < NOW() AND s.id IS NULL THEN 'overdue'
                         WHEN s.id IS NOT NULL AND s.status = 'graded' THEN 'graded'
                         WHEN s.id IS NOT NULL THEN 'submitted'
                         ELSE 'pending'
                     END as assignment_status
                     FROM assignments a
                     JOIN courses c ON a.course_id = c.id
                     JOIN users u ON c.teacher_id = u.id
                     JOIN enrollments e ON c.id = e.course_id
                     LEFT JOIN submissions s ON a.id = s.assignment_id AND s.student_id = :student_id
                     WHERE e.student_id = :student_id AND e.status = 'active'
                     ORDER BY a.deadline ASC";
$stmt = $conn->prepare($assignmentsQuery);
$stmt->execute([':student_id' => $studentId]);
$assignments = $stmt->fetchAll();

$pageTitle = 'Assignments - Study Hub';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-tasks"></i> My Assignments</h1>
            <p>View and submit your assignments</p>
        </div>
        
        <?php if (count($assignments) > 0): ?>
            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Assignment</th>
                            <th>Course</th>
                            <th>Teacher</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($assignment['title']); ?></strong>
                                <?php if ($assignment['total_marks']): ?>
                                    <br><small style="color: var(--gray);">Max Marks: <?php echo $assignment['total_marks']; ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($assignment['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($assignment['teacher_name']); ?></td>
                            <td>
                                <i class="fas fa-clock"></i> <?php echo formatDateTime($assignment['deadline']); ?>
                            </td>
                            <td>
                                <?php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'submitted' => 'info',
                                    'graded' => 'success',
                                    'overdue' => 'danger'
                                ];
                                $status = $assignment['assignment_status'];
                                ?>
                                <span class="badge badge-<?php echo $statusColors[$status]; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($assignment['score'] !== null): ?>
                                    <strong style="color: var(--success);"><?php echo $assignment['score']; ?></strong>
                                    <?php if ($assignment['total_marks']): ?>
                                        / <?php echo $assignment['total_marks']; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: var(--gray);">Not graded</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($assignment['assignment_status'] === 'pending' || $assignment['assignment_status'] === 'overdue'): ?>
                                    <a href="submit-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-upload"></i> Submit
                                    </a>
                                <?php elseif ($assignment['assignment_status'] === 'submitted'): ?>
                                    <a href="view-submission.php?id=<?php echo $assignment['submission_id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                <?php else: ?>
                                    <a href="view-submission.php?id=<?php echo $assignment['submission_id']; ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-check-circle"></i> View Result
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 4rem 2rem;">
                <i class="fas fa-clipboard-list" style="font-size: 5rem; color: var(--gray); opacity: 0.3;"></i>
                <h3 style="margin-top: 1.5rem; color: var(--dark-text);">No Assignments Yet</h3>
                <p style="color: var(--gray); margin: 1rem 0;">
                    Assignments from your enrolled courses will appear here
                </p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_assignments', 'Viewed assignments page');
?>
