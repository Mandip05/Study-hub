<?php
/**
 * Teacher Students Page
 * Study Hub LMS - View enrolled students
 */

require_once '../config/config.php';
requireRole('teacher');

$db = new Database();
$conn = $db->getConnection();
$teacherId = getCurrentUserId();

// Get all students enrolled in teacher's courses
$studentsQuery = "SELECT DISTINCT u.*, c.title as course_title, e.enrolled_at
                  FROM users u
                  JOIN enrollments e ON u.id = e.student_id
                  JOIN courses c ON e.course_id = c.id
                  WHERE c.teacher_id = :teacher_id AND u.role = 'student'
                  ORDER BY u.full_name ASC";
$stmt = $conn->prepare($studentsQuery);
$stmt->execute([':teacher_id' => $teacherId]);
$students = $stmt->fetchAll();

$pageTitle = 'Students - Teacher Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> My Students</h1>
            <p>View students enrolled in your courses</p>
        </div>
        
        <div class="card">
            <div class="card-body">
                <?php if (count($students) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Course</th>
                                <th>Enrolled</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($student['full_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($student['course_title']); ?></td>
                                <td><?php echo formatDate($student['enrolled_at']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $student['status'] === 'active' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($student['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-users" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem;">No Students Yet</h3>
                        <p style="color: var(--gray);">Students will appear here when they enroll in your courses</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_students', 'Viewed students page');
?>
