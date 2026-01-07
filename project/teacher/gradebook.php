<?php
/**
 * Teacher Gradebook Page
 * Study Hub LMS - Manage student grades
 */

require_once '../config/config.php';
requireRole('teacher');

$db = new Database();
$conn = $db->getConnection();
$teacherId = getCurrentUserId();

// Get teacher's courses
$coursesQuery = "SELECT * FROM courses WHERE teacher_id = :teacher_id";
$stmt = $conn->prepare($coursesQuery);
$stmt->execute([':teacher_id' => $teacherId]);
$courses = $stmt->fetchAll();

$pageTitle = 'Gradebook - Teacher Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-graduation-cap"></i> Gradebook</h1>
            <p>Manage student grades</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Select Course</h3>
            </div>
            <div class="card-body">
                <?php if (count($courses) > 0): ?>
                    <div class="grid grid-cols-3" style="gap: 1.5rem;">
                        <?php foreach ($courses as $course): ?>
                        <a href="course-grades.php?course_id=<?php echo $course['id']; ?>" class="card" style="text-decoration: none;">
                            <div style="padding: 2rem; text-align: center;">
                                <i class="fas fa-book" style="font-size: 3rem; color: var(--success);"></i>
                                <h4 style="margin: 1rem 0;"><?php echo htmlspecialchars($course['title']); ?></h4>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-book" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem;">No Courses</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_gradebook', 'Viewed gradebook page');
?>
