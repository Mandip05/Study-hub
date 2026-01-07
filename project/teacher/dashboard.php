<?php
/**
 * Teacher Dashboard
 * Study Hub LMS
 */

require_once '../config/config.php';
requireRole('teacher');

$db = new Database();
$conn = $db->getConnection();
$teacher_id = getCurrentUserId();

// Fetch teacher's courses
$coursesQuery = "SELECT c.*, 
                 (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as enrolled_count,
                 (SELECT COUNT(*) FROM assignments WHERE course_id = c.id) as assignments_count
                 FROM courses c 
                 WHERE c.teacher_id = :teacher_id
                 ORDER BY c.created_at DESC";
$coursesStmt = $conn->prepare($coursesQuery);
$coursesStmt->bindParam(':teacher_id', $teacher_id);
$coursesStmt->execute();
$courses = $coursesStmt->fetchAll();

// Fetch pending submissions
$submissionsQuery = "SELECT s.*, a.title as assignment_title, c.title as course_title, u.full_name as student_name
                     FROM submissions s
                     JOIN assignments a ON s.assignment_id = a.id
                     JOIN courses c ON a.course_id = c.id
                     JOIN users u ON s.student_id = u.id
                     WHERE c.teacher_id = :teacher_id AND s.status = 'submitted'
                     ORDER BY s.submitted_at DESC
                     LIMIT 10";
$submissionsStmt = $conn->prepare($submissionsQuery);
$submissionsStmt->bindParam(':teacher_id', $teacher_id);
$submissionsStmt->execute();
$pendingSubmissions = $submissionsStmt->fetchAll();

// Stats
$totalStudents = 0;
foreach ($courses as $course) {
    $totalStudents += $course['enrolled_count'];
}

$totalAssignments = 0;
foreach ($courses as $course) {
    $totalAssignments += $course['assignments_count'];
}

$pageTitle = 'Teacher Dashboard - Study Hub';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="flex-between" style="margin-bottom: 2rem;">
            <div>
                <h1>Welcome back, <?php echo $_SESSION['user_name']; ?>! 👋</h1>
                <p style="color: var(--gray); margin-top: 0.5rem;">Manage your courses and track student progress</p>
            </div>
            <a href="create-course.php" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> Create New Course
            </a>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-4" style="margin-bottom: 2rem;">
            <div class="card" style="background: linear-gradient(135deg, #0A8BCB, #1E9ED8); color: white;">
                <div class="flex-between">
                    <div>
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem;">My Courses</p>
                        <h2 style="color: white; font-size: 2.5rem;"><?php echo count($courses); ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #10B981, #059669); color: white;">
                <div class="flex-between">
                    <div>
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem;">Total Students</p>
                        <h2 style="color: white; font-size: 2.5rem;"><?php echo $totalStudents; ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #F59E0B, #D97706); color: white;">
                <div class="flex-between">
                    <div>
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem;">Assignments</p>
                        <h2 style="color: white; font-size: 2.5rem;"><?php echo $totalAssignments; ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #EF4444, #DC2626); color: white;">
                <div class="flex-between">
                    <div>
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem;">Pending Reviews</p>
                        <h2 style="color: white; font-size: 2.5rem;"><?php echo count($pendingSubmissions); ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2" style="gap: 2rem;">
            <!-- My Courses -->
            <div class="card">
                <div class="card-header">
                    <div class="flex-between">
                        <h3 class="card-title"><i class="fas fa-book"></i> My Courses</h3>
                        <a href="courses.php" style="color: var(--primary-blue); font-size: 0.9rem;">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($courses) > 0): ?>
                        <?php foreach (array_slice($courses, 0, 5) as $course): ?>
                            <div style="padding: 1rem; border-bottom: 1px solid #E5E7EB;">
                                <div class="flex-between" style="margin-bottom: 0.5rem;">
                                    <h4 style="margin: 0; font-size: 1rem;"><?php echo $course['title']; ?></h4>
                                    <span class="badge badge-<?php 
                                        echo $course['status'] === 'published' ? 'success' : 
                                             ($course['status'] === 'draft' ? 'warning' : 'info'); 
                                    ?>">
                                        <?php echo ucfirst($course['status']); ?>
                                    </span>
                                </div>
                                <div class="flex-between" style="font-size: 0.875rem; color: var(--gray);">
                                    <span><i class="fas fa-users"></i> <?php echo $course['enrolled_count']; ?> students</span>
                                    <span><i class="fas fa-tasks"></i> <?php echo $course['assignments_count']; ?> assignments</span>
                                    <a href="course-edit.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">
                                        Manage
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem 1rem;">
                            <i class="fas fa-chalkboard" style="font-size: 3rem; color: var(--gray); opacity: 0.3;"></i>
                            <p style="color: var(--gray); margin-top: 1rem;">No courses created yet</p>
                            <a href="create-course.php" class="btn btn-primary btn-sm" style="margin-top: 1rem;">
                                Create Your First Course
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Pending Submissions -->
            <div class="card">
                <div class="card-header">
                    <div class="flex-between">
                        <h3 class="card-title"><i class="fas fa-clock"></i> Pending Reviews</h3>
                        <a href="assignments.php" style="color: var(--primary-blue); font-size: 0.9rem;">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($pendingSubmissions) > 0): ?>
                        <?php foreach (array_slice($pendingSubmissions, 0, 5) as $submission): ?>
                            <div style="padding: 1rem; border-bottom: 1px solid #E5E7EB;">
                                <div class="flex-between" style="margin-bottom: 0.5rem;">
                                    <h4 style="margin: 0; font-size: 1rem;"><?php echo $submission['assignment_title']; ?></h4>
                                    <span class="badge badge-warning">Pending</span>
                                </div>
                                <p style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-user"></i> <?php echo $submission['student_name']; ?>
                                </p>
                                <div class="flex-between" style="font-size: 0.875rem;">
                                    <span style="color: var(--gray);">
                                        <i class="fas fa-clock"></i> <?php echo timeAgo($submission['submitted_at']); ?>
                                    </span>
                                    <a href="grade-assignment.php?id=<?php echo $submission['id']; ?>" class="btn btn-primary btn-sm">
                                        Review
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem 1rem;">
                            <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--success); opacity: 0.5;"></i>
                            <p style="color: var(--gray); margin-top: 1rem;">All caught up! No pending submissions</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-4" style="gap: 1rem;">
                    <a href="create-course.php" class="card" style="text-align: center; padding: 2rem 1rem; text-decoration: none;">
                        <i class="fas fa-plus-circle" style="font-size: 2.5rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 1rem;">Create Course</h4>
                    </a>
                    <a href="attendance.php" class="card" style="text-align: center; padding: 2rem 1rem; text-decoration: none;">
                        <i class="fas fa-user-check" style="font-size: 2.5rem; color: var(--success); margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 1rem;">Take Attendance</h4>
                    </a>
                    <a href="qr-attendance.php" class="card" style="text-align: center; padding: 2rem 1rem; text-decoration: none;">
                        <i class="fas fa-qrcode" style="font-size: 2.5rem; color: var(--warning); margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 1rem;">Generate QR</h4>
                    </a>
                    <a href="gradebook.php" class="card" style="text-align: center; padding: 2rem 1rem; text-decoration: none;">
                        <i class="fas fa-chart-line" style="font-size: 2.5rem; color: var(--info); margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 1rem;">View Gradebook</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
