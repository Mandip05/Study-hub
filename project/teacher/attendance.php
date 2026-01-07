<?php
/**
 * Teacher Attendance Page
 * Study Hub LMS - Mark attendance
 */

require_once '../config/config.php';
requireRole('teacher');

$db = new Database();
$conn = $db->getConnection();
$teacherId = getCurrentUserId();

// Get teacher's courses
$coursesQuery = "SELECT * FROM courses WHERE teacher_id = :teacher_id AND status = 'published'";
$stmt = $conn->prepare($coursesQuery);
$stmt->execute([':teacher_id' => $teacherId]);
$courses = $stmt->fetchAll();

$pageTitle = 'Attendance - Teacher Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-check"></i> Mark Attendance</h1>
            <p>Record student attendance</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Select Course</h3>
            </div>
            <div class="card-body">
                <?php if (count($courses) > 0): ?>
                    <div class="grid grid-cols-3" style="gap: 1.5rem;">
                        <?php foreach ($courses as $course): ?>
                        <a href="mark-attendance.php?course_id=<?php echo $course['id']; ?>" class="card" style="text-decoration: none; transition: all 0.3s;">
                            <div style="padding: 2rem; text-align: center;">
                                <i class="fas fa-book" style="font-size: 3rem; color: var(--primary-blue);"></i>
                                <h4 style="margin: 1rem 0 0.5rem 0;"><?php echo htmlspecialchars($course['title']); ?></h4>
                                <p style="color: var(--gray); font-size: 0.9rem;"><?php echo htmlspecialchars($course['category']); ?></p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-book" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem;">No Active Courses</h3>
                        <p style="color: var(--gray);">Create and publish courses to mark attendance</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_attendance', 'Viewed attendance page');
?>
