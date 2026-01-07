<?php
/**
 * Admin Courses Page
 * Study Hub LMS - Manage all courses
 */

require_once '../config/config.php';
requireRole('admin');

$db = new Database();
$conn = $db->getConnection();

// Get all courses with teacher info
$coursesQuery = "SELECT c.*, u.full_name as teacher_name,
                 (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrollment_count
                 FROM courses c
                 JOIN users u ON c.teacher_id = u.id
                 ORDER BY c.created_at DESC";
$stmt = $conn->prepare($coursesQuery);
$stmt->execute();
$courses = $stmt->fetchAll();

$pageTitle = 'Manage Courses - Admin Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book"></i> Manage Courses</h1>
            <p>View and manage all courses in the system</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Courses</h3>
            </div>
            <div class="card-body">
                <?php if (count($courses) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Teacher</th>
                                <th>Category</th>
                                <th>Enrollments</th>
                                <th>Status</th>
                                <th>Approval</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($course['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($course['teacher_name']); ?></td>
                                <td><?php echo htmlspecialchars($course['category']); ?></td>
                                <td><?php echo $course['enrollment_count']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $course['status'] === 'published' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($course['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $course['approval_status'] === 'approved' ? 'success' : 
                                            ($course['approval_status'] === 'rejected' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php echo ucfirst($course['approval_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($course['created_at']); ?></td>
                                <td>
                                    <a href="view-course.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-book" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem;">No courses yet</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_courses', 'Viewed courses management page');
?>
