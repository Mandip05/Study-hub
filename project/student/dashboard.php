<?php
/**
 * Student Dashboard
 * Study Hub LMS - Comprehensive Student Dashboard
 */

require_once '../config/config.php';
requireRole('student');

$db = new Database();
$conn = $db->getConnection();
$studentId = getCurrentUserId();

// Get student statistics
$stats = [];

// Enrolled courses
$enrolledQuery = "SELECT COUNT(*) as total FROM enrollments WHERE student_id = :student_id AND status = 'active'";
$stmt = $conn->prepare($enrolledQuery);
$stmt->execute([':student_id' => $studentId]);
$stats['enrolled_courses'] = $stmt->fetchColumn();

// Completed courses
$completedQuery = "SELECT COUNT(*) as total FROM enrollments WHERE student_id = :student_id AND status = 'completed'";
$stmt = $conn->prepare($completedQuery);
$stmt->execute([':student_id' => $studentId]);
$stats['completed_courses'] = $stmt->fetchColumn();

// Overall progress percentage
$progressQuery = "SELECT AVG(progress) as avg_progress FROM enrollments WHERE student_id = :student_id";
$stmt = $conn->prepare($progressQuery);
$stmt->execute([':student_id' => $studentId]);
$stats['overall_progress'] = round($stmt->fetchColumn() ?? 0, 1);

// Attendance percentage
$attendanceQuery = "SELECT 
    COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count,
    COUNT(*) as total_attendance
    FROM attendance 
    WHERE student_id = :student_id";
$stmt = $conn->prepare($attendanceQuery);
$stmt->execute([':student_id' => $studentId]);
$attendanceResult = $stmt->fetch();
$stats['attendance_percentage'] = $attendanceResult['total_attendance'] > 0 
    ? round(($attendanceResult['present_count'] / $attendanceResult['total_attendance']) * 100, 1) 
    : 0;

// Pending assignments
$pendingAssignmentsQuery = "SELECT COUNT(*) as total 
                            FROM assignments a
                            JOIN enrollments e ON a.course_id = e.course_id
                            LEFT JOIN submissions s ON a.id = s.assignment_id AND s.student_id = :student_id
                            WHERE e.student_id = :student_id
                            AND e.status = 'active'
                            AND s.id IS NULL
                            AND a.deadline > NOW()";
$stmt = $conn->prepare($pendingAssignmentsQuery);
$stmt->execute([':student_id' => $studentId]);
$stats['pending_assignments'] = $stmt->fetchColumn();

// Certificates earned
$certificatesQuery = "SELECT COUNT(*) as total FROM certificates WHERE student_id = :student_id";
$stmt = $conn->prepare($certificatesQuery);
$stmt->execute([':student_id' => $studentId]);
$stats['certificates'] = $stmt->fetchColumn();

// Unread notifications
$unreadNotificationsQuery = "SELECT COUNT(*) FROM notifications WHERE user_id = :student_id AND is_read = FALSE";
$stmt = $conn->prepare($unreadNotificationsQuery);
$stmt->execute([':student_id' => $studentId]);
$stats['unread_notifications'] = $stmt->fetchColumn();

// Get enrolled courses with details
$coursesQuery = "SELECT c.*, e.progress, e.enrolled_at, u.full_name as teacher_name,
                 (SELECT COUNT(*) FROM modules WHERE course_id = c.id) as module_count,
                 (SELECT COUNT(*) FROM assignments WHERE course_id = c.id) as assignment_count
                 FROM courses c
                 JOIN enrollments e ON c.id = e.course_id
                 JOIN users u ON c.teacher_id = u.id
                 WHERE e.student_id = :student_id
                 AND e.status = 'active'
                 ORDER BY e.enrolled_at DESC
                 LIMIT 6";
$stmt = $conn->prepare($coursesQuery);
$stmt->execute([':student_id' => $studentId]);
$enrolledCourses = $stmt->fetchAll();

// Upcoming assignments
$upcomingAssignmentsQuery = "SELECT a.*, c.title as course_title, u.full_name as teacher_name,
                              s.id as submission_id, s.status as submission_status
                              FROM assignments a
                              JOIN courses c ON a.course_id = c.id
                              JOIN users u ON c.teacher_id = u.id
                              JOIN enrollments e ON c.id = e.course_id
                              LEFT JOIN submissions s ON a.id = s.assignment_id AND s.student_id = :student_id
                              WHERE e.student_id = :student_id
                              AND e.status = 'active'
                              AND a.deadline > NOW()
                              ORDER BY a.deadline ASC
                              LIMIT 5";
$stmt = $conn->prepare($upcomingAssignmentsQuery);
$stmt->execute([':student_id' => $studentId]);
$upcomingAssignments = $stmt->fetchAll();

// Recent notifications
$notificationsQuery = "SELECT * FROM notifications 
                       WHERE user_id = :student_id 
                       ORDER BY created_at DESC 
                       LIMIT 5";
$stmt = $conn->prepare($notificationsQuery);
$stmt->execute([':student_id' => $studentId]);
$recentNotifications = $stmt->fetchAll();

// Recent certificates
$certificatesListQuery = "SELECT cert.*, c.title as course_title 
                          FROM certificates cert
                          JOIN courses c ON cert.course_id = c.id
                          WHERE cert.student_id = :student_id
                          ORDER BY cert.issued_at DESC
                          LIMIT 3";
$stmt = $conn->prepare($certificatesListQuery);
$stmt->execute([':student_id' => $studentId]);
$recentCertificates = $stmt->fetchAll();

$pageTitle = 'Student Dashboard - Study Hub';
include '../includes/header.php';
?>

<style>
    .stat-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-md);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30%, -30%);
    }
    
    .course-card {
        background: white;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
    }
    
    .course-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-5px);
    }
    
    .course-thumbnail {
        height: 150px;
        background: linear-gradient(135deg, #0A8BCB, #1E9ED8);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }
    
    .progress-ring {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: conic-gradient(var(--primary-blue) calc(var(--progress) * 1%), #E5E7EB 0);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .progress-ring::before {
        content: attr(data-progress) '%';
        position: absolute;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: var(--dark-text);
    }
    
    .notification-item {
        padding: 1rem;
        border-left: 3px solid var(--primary-blue);
        background: var(--background);
        margin-bottom: 0.5rem;
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
        transition: all 0.2s ease;
    }
    
    .notification-item:hover {
        background: #D1E9F6;
    }
    
    .notification-item.unread {
        border-left-color: var(--warning);
        background: #FEF3C7;
    }
</style>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Welcome Header -->
        <div class="flex-between" style="margin-bottom: 2rem;">
            <div>
                <h1><i class="fas fa-home"></i> Welcome Back, <?php echo $_SESSION['user_name']; ?>!</h1>
                <p style="color: var(--gray); margin-top: 0.5rem;">
                    <?php echo date('l, F j, Y'); ?> • Ready to continue learning?
                </p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="courses.php?action=browse" class="btn btn-primary">
                    <i class="fas fa-search"></i> Browse Courses
                </a>
                <a href="notifications.php" class="btn btn-outline">
                    <i class="fas fa-bell"></i> 
                    <?php if ($stats['unread_notifications'] > 0): ?>
                        <span class="badge badge-danger"><?php echo $stats['unread_notifications']; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-4" style="margin-bottom: 2rem; gap: 1.5rem;">
            <div class="stat-card" style="background: linear-gradient(135deg, #0A8BCB, #1E9ED8); color: white;">
                <div class="flex-between">
                    <div style="position: relative; z-index: 1;">
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem; font-size: 0.9rem;">Enrolled Courses</p>
                        <h2 style="color: white; font-size: 2.5rem; margin: 0;"><?php echo $stats['enrolled_courses']; ?></h2>
                        <p style="color: rgba(255,255,255,0.7); font-size: 0.85rem; margin-top: 0.5rem;">
                            <?php echo $stats['completed_courses']; ?> Completed
                        </p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3; position: relative; z-index: 1;">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #10B981, #059669); color: white;">
                <div class="flex-between">
                    <div style="position: relative; z-index: 1;">
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem; font-size: 0.9rem;">Overall Progress</p>
                        <h2 style="color: white; font-size: 2.5rem; margin: 0;"><?php echo $stats['overall_progress']; ?>%</h2>
                        <div style="margin-top: 0.75rem; height: 6px; background: rgba(255,255,255,0.3); border-radius: 10px; overflow: hidden;">
                            <div style="height: 100%; width: <?php echo $stats['overall_progress']; ?>%; background: white;"></div>
                        </div>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3; position: relative; z-index: 1;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #F59E0B, #D97706); color: white;">
                <div class="flex-between">
                    <div style="position: relative; z-index: 1;">
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem; font-size: 0.9rem;">Attendance</p>
                        <h2 style="color: white; font-size: 2.5rem; margin: 0;"><?php echo $stats['attendance_percentage']; ?>%</h2>
                        <div style="margin-top: 0.75rem; height: 6px; background: rgba(255,255,255,0.3); border-radius: 10px; overflow: hidden;">
                            <div style="height: 100%; width: <?php echo $stats['attendance_percentage']; ?>%; background: white;"></div>
                        </div>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3; position: relative; z-index: 1;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: white;">
                <div class="flex-between">
                    <div style="position: relative; z-index: 1;">
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem; font-size: 0.9rem;">Pending Tasks</p>
                        <h2 style="color: white; font-size: 2.5rem; margin: 0;"><?php echo $stats['pending_assignments']; ?></h2>
                        <p style="color: rgba(255,255,255,0.7); font-size: 0.85rem; margin-top: 0.5rem;">
                            Assignments Due
                        </p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3; position: relative; z-index: 1;">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Secondary Stats -->
        <div class="grid grid-cols-2" style="margin-bottom: 2rem; gap: 1.5rem;">
            <div class="card">
                <div class="flex-between">
                    <div>
                        <p style="color: var(--gray); margin-bottom: 0.5rem;"><i class="fas fa-certificate"></i> Certificates Earned</p>
                        <h3 style="margin: 0; font-size: 2rem; color: var(--primary-blue);"><?php echo $stats['certificates']; ?></h3>
                    </div>
                    <a href="certificates.php" class="btn btn-sm btn-primary">View All</a>
                </div>
            </div>
            
            <div class="card">
                <div class="flex-between">
                    <div>
                        <p style="color: var(--gray); margin-bottom: 0.5rem;"><i class="fas fa-bell"></i> Notifications</p>
                        <h3 style="margin: 0; font-size: 2rem; color: var(--warning);"><?php echo $stats['unread_notifications']; ?></h3>
                    </div>
                    <a href="notifications.php" class="btn btn-sm btn-warning">View All</a>
                </div>
            </div>
        </div>
        
        <!-- My Courses -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <div class="flex-between">
                    <h3 class="card-title"><i class="fas fa-book"></i> My Courses</h3>
                    <a href="courses.php" style="color: var(--primary-blue); font-size: 0.9rem;">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($enrolledCourses) > 0): ?>
                    <div class="grid grid-cols-3" style="gap: 1.5rem;">
                        <?php foreach ($enrolledCourses as $course): ?>
                        <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="course-card">
                            <div class="course-thumbnail">
                                <i class="fas fa-book-reader"></i>
                            </div>
                            <div style="padding: 1rem;">
                                <h4 style="margin: 0 0 0.5rem 0; font-size: 1rem; color: var(--dark-text);">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </h4>
                                <p style="color: var(--gray); font-size: 0.875rem; margin: 0.25rem 0;">
                                    <i class="fas fa-chalkboard-teacher"></i> <?php echo htmlspecialchars($course['teacher_name']); ?>
                                </p>
                                <div style="margin-top: 1rem;">
                                    <div class="flex-between" style="margin-bottom: 0.5rem;">
                                        <span style="font-size: 0.875rem; color: var(--gray);">Progress</span>
                                        <span style="font-size: 0.875rem; font-weight: 600; color: var(--primary-blue);">
                                            <?php echo round($course['progress'], 1); ?>%
                                        </span>
                                    </div>
                                    <div style="height: 6px; background: #E5E7EB; border-radius: 10px; overflow: hidden;">
                                        <div style="height: 100%; width: <?php echo $course['progress']; ?>%; background: var(--primary-blue); transition: width 0.3s ease;"></div>
                                    </div>
                                </div>
                                <div class="flex-between" style="margin-top: 1rem; font-size: 0.75rem; color: var(--gray);">
                                    <span><i class="fas fa-layer-group"></i> <?php echo $course['module_count']; ?> Modules</span>
                                    <span><i class="fas fa-tasks"></i> <?php echo $course['assignment_count']; ?> Assignments</span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem 1rem;">
                        <i class="fas fa-book-open" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem; color: var(--dark-text);">No Courses Enrolled Yet</h3>
                        <p style="color: var(--gray); margin: 0.5rem 0 1.5rem 0;">Start your learning journey by browsing available courses</p>
                        <a href="courses.php?action=browse" class="btn btn-primary">
                            <i class="fas fa-search"></i> Browse Courses
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="grid grid-cols-2" style="gap: 2rem; margin-bottom: 2rem;">
            <!-- Upcoming Assignments -->
            <div class="card">
                <div class="card-header">
                    <div class="flex-between">
                        <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Upcoming Assignments</h3>
                        <a href="assignments.php" style="color: var(--primary-blue); font-size: 0.9rem;">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($upcomingAssignments) > 0): ?>
                        <?php foreach ($upcomingAssignments as $assignment): ?>
                        <div style="padding: 1rem; border-bottom: 1px solid #E5E7EB;">
                            <div class="flex-between" style="margin-bottom: 0.5rem;">
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; font-size: 1rem;">
                                        <a href="assignments.php?id=<?php echo $assignment['id']; ?>" style="color: var(--dark-text);">
                                            <?php echo htmlspecialchars($assignment['title']); ?>
                                        </a>
                                    </h4>
                                    <p style="color: var(--gray); font-size: 0.875rem; margin: 0.25rem 0;">
                                        <?php echo htmlspecialchars($assignment['course_title']); ?>
                                    </p>
                                    <p style="color: var(--gray); font-size: 0.75rem; margin: 0.25rem 0;">
                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($assignment['teacher_name']); ?>
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    <?php if ($assignment['submission_id']): ?>
                                        <span class="badge badge-success">Submitted</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pending</span>
                                    <?php endif; ?>
                                    <p style="color: var(--gray); font-size: 0.75rem; margin-top: 0.5rem;">
                                        <i class="fas fa-clock"></i> Due: <?php echo formatDate($assignment['deadline']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 2rem 1rem;">
                            <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--success); opacity: 0.5;"></i>
                            <p style="color: var(--gray); margin-top: 1rem;">No upcoming assignments</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Notifications -->
            <div class="card">
                <div class="card-header">
                    <div class="flex-between">
                        <h3 class="card-title"><i class="fas fa-bell"></i> Recent Notifications</h3>
                        <a href="notifications.php" style="color: var(--primary-blue); font-size: 0.9rem;">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($recentNotifications) > 0): ?>
                        <?php foreach ($recentNotifications as $notification): ?>
                        <div class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>">
                            <div class="flex-between">
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; font-size: 0.95rem; color: var(--dark-text);">
                                        <?php echo htmlspecialchars($notification['title']); ?>
                                    </h4>
                                    <p style="color: var(--gray); font-size: 0.875rem; margin: 0.25rem 0;">
                                        <?php echo htmlspecialchars($notification['message']); ?>
                                    </p>
                                </div>
                                <span style="color: var(--gray); font-size: 0.75rem; white-space: nowrap; margin-left: 1rem;">
                                    <?php echo timeAgo($notification['created_at']); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 2rem 1rem;">
                            <i class="fas fa-bell-slash" style="font-size: 3rem; color: var(--gray); opacity: 0.3;"></i>
                            <p style="color: var(--gray); margin-top: 1rem;">No notifications yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Certificates -->
        <?php if (count($recentCertificates) > 0): ?>
        <div class="card">
            <div class="card-header">
                <div class="flex-between">
                    <h3 class="card-title"><i class="fas fa-award"></i> My Certificates</h3>
                    <a href="certificates.php" style="color: var(--primary-blue); font-size: 0.9rem;">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-3" style="gap: 1rem;">
                    <?php foreach ($recentCertificates as $cert): ?>
                    <div class="card" style="text-align: center; padding: 1.5rem;">
                        <i class="fas fa-certificate" style="font-size: 3rem; color: #F59E0B; margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 0.95rem; margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($cert['course_title']); ?></h4>
                        <p style="color: var(--gray); font-size: 0.85rem; margin: 0.25rem 0;">
                            Code: <?php echo $cert['certificate_code']; ?>
                        </p>
                        <p style="color: var(--gray); font-size: 0.75rem; margin: 0.5rem 0;">
                            Issued: <?php echo formatDate($cert['issue_date']); ?>
                        </p>
                        <a href="certificates.php?download=<?php echo $cert['id']; ?>" class="btn btn-sm btn-primary" style="margin-top: 0.5rem;">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_student_dashboard', 'Viewed student dashboard');
?>
