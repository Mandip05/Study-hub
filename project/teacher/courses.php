<?php
/**
 * Teacher Courses Page
 * Study Hub LMS - Manage teacher's courses
 */

require_once '../config/config.php';
requireRole('teacher');

$db = new Database();
$conn = $db->getConnection();
$teacherId = getCurrentUserId();

// Get teacher's courses
$coursesQuery = "SELECT c.*,
                 (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrollment_count,
                 (SELECT COUNT(*) FROM modules WHERE course_id = c.id) as module_count,
                 (SELECT COUNT(*) FROM assignments WHERE course_id = c.id) as assignment_count
                 FROM courses c
                 WHERE c.teacher_id = :teacher_id
                 ORDER BY c.created_at DESC";
$stmt = $conn->prepare($coursesQuery);
$stmt->execute([':teacher_id' => $teacherId]);
$courses = $stmt->fetchAll();

$pageTitle = 'My Courses - Teacher Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="flex-between">
                <div>
                    <h1><i class="fas fa-chalkboard-teacher"></i> My Courses</h1>
                    <p>Manage your courses</p>
                </div>
                <a href="create-course.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Course
                </a>
            </div>
        </div>
        
        <?php if (count($courses) > 0): ?>
            <div class="grid grid-cols-3" style="gap: 2rem;">
                <?php foreach ($courses as $course): ?>
                <div class="card">
                    <div style="height: 150px; background: linear-gradient(135deg, #0A8BCB, #1E9ED8); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p style="color: var(--gray); font-size: 0.9rem; margin: 0.5rem 0;">
                            <?php echo htmlspecialchars($course['category']); ?>
                        </p>
                        <div style="display: flex; gap: 1rem; margin: 1rem 0; font-size: 0.85rem; color: var(--gray);">
                            <span><i class="fas fa-users"></i> <?php echo $course['enrollment_count']; ?> Students</span>
                            <span><i class="fas fa-layer-group"></i> <?php echo $course['module_count']; ?> Modules</span>
                        </div>
                        <div style="margin: 1rem 0;">
                            <span class="badge badge-<?php echo $course['status'] === 'published' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($course['status']); ?>
                            </span>
                            <span class="badge badge-<?php 
                                echo $course['approval_status'] === 'approved' ? 'success' : 
                                    ($course['approval_status'] === 'rejected' ? 'danger' : 'warning'); 
                            ?>">
                                <?php echo ucfirst($course['approval_status']); ?>
                            </span>
                        </div>
                        <a href="edit-course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            <i class="fas fa-edit"></i> Manage Course
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 4rem;">
                <i class="fas fa-book-open" style="font-size: 5rem; color: var(--gray); opacity: 0.3;"></i>
                <h3 style="margin-top: 1.5rem;">No Courses Yet</h3>
                <p style="color: var(--gray); margin: 1rem 0 2rem 0;">Create your first course to start teaching</p>
                <a href="create-course.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Course
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_courses', 'Viewed my courses page');
?>
