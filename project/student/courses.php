<?php
/**
 * Student Courses Page
 * Study Hub LMS - View and manage enrolled courses
 */

require_once '../config/config.php';
requireRole('student');

$db = new Database();
$conn = $db->getConnection();
$studentId = getCurrentUserId();

// Get enrolled courses
$enrolledQuery = "SELECT c.*, e.progress, e.enrolled_at, e.status as enrollment_status,
                  u.full_name as teacher_name,
                  (SELECT COUNT(*) FROM modules WHERE course_id = c.id) as module_count,
                  (SELECT COUNT(*) FROM lessons WHERE module_id IN (SELECT id FROM modules WHERE course_id = c.id)) as lesson_count,
                  (SELECT COUNT(*) FROM assignments WHERE course_id = c.id) as assignment_count
                  FROM enrollments e
                  JOIN courses c ON e.course_id = c.id
                  JOIN users u ON c.teacher_id = u.id
                  WHERE e.student_id = :student_id
                  ORDER BY e.enrolled_at DESC";
$stmt = $conn->prepare($enrolledQuery);
$stmt->execute([':student_id' => $studentId]);
$enrolledCourses = $stmt->fetchAll();

$pageTitle = 'My Courses - Study Hub';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book"></i> My Courses</h1>
            <p>View and manage your enrolled courses</p>
        </div>
        
        <?php if (count($enrolledCourses) > 0): ?>
            <div class="grid grid-cols-3" style="gap: 2rem;">
                <?php foreach ($enrolledCourses as $course): ?>
                <div class="card" style="overflow: hidden;">
                    <div style="height: 180px; background: linear-gradient(135deg, #0A8BCB, #1E9ED8); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p style="color: var(--gray); font-size: 0.9rem; margin: 0.5rem 0;">
                            <i class="fas fa-chalkboard-teacher"></i> <?php echo htmlspecialchars($course['teacher_name']); ?>
                        </p>
                        <p style="color: var(--gray); font-size: 0.85rem; margin: 0.5rem 0;">
                            <i class="fas fa-layer-group"></i> <?php echo $course['module_count']; ?> Modules • 
                            <i class="fas fa-video"></i> <?php echo $course['lesson_count']; ?> Lessons • 
                            <i class="fas fa-tasks"></i> <?php echo $course['assignment_count']; ?> Assignments
                        </p>
                        
                        <div style="margin: 1rem 0;">
                            <div class="flex-between" style="margin-bottom: 0.5rem;">
                                <span style="font-size: 0.9rem; color: var(--gray);">Progress</span>
                                <span style="font-size: 0.9rem; font-weight: 600; color: var(--primary-blue);">
                                    <?php echo round($course['progress'], 1); ?>%
                                </span>
                            </div>
                            <div style="height: 8px; background: #E5E7EB; border-radius: 10px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo $course['progress']; ?>%; background: var(--primary-blue); transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                        
                        <div class="flex-between" style="margin-top: 1rem;">
                            <span class="badge badge-<?php echo $course['enrollment_status'] === 'active' ? 'success' : 'info'; ?>">
                                <?php echo ucfirst($course['enrollment_status']); ?>
                            </span>
                            <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">
                                <?php echo $course['progress'] > 0 ? 'Continue' : 'Start'; ?> Learning
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 4rem 2rem;">
                <i class="fas fa-book-open" style="font-size: 5rem; color: var(--gray); opacity: 0.3;"></i>
                <h3 style="margin-top: 1.5rem; color: var(--dark-text);">No Courses Enrolled Yet</h3>
                <p style="color: var(--gray); margin: 1rem 0 2rem 0;">
                    Browse available courses and start your learning journey today!
                </p>
                <a href="../courses.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Browse Courses
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_courses', 'Viewed my courses page');
?>
