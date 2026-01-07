<?php
/**
 * Admin Dashboard
 * Study Hub LMS
 */

require_once '../config/config.php';
requireRole('admin');

$db = new Database();
$conn = $db->getConnection();

// Get statistics
$stats = [];

// Total users by role
$usersQuery = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$usersStmt = $conn->prepare($usersQuery);
$usersStmt->execute();
while ($row = $usersStmt->fetch()) {
    $stats[$row['role'] . '_count'] = $row['count'];
}

// Total courses
$coursesQuery = "SELECT COUNT(*) as total FROM courses";
$stats['total_courses'] = $conn->query($coursesQuery)->fetchColumn();

// Pending course approvals
$pendingCoursesQuery = "SELECT COUNT(*) as total FROM courses WHERE approval_status = 'pending'";
$stats['pending_courses'] = $conn->query($pendingCoursesQuery)->fetchColumn();

// Total enrollments
$enrollmentsQuery = "SELECT COUNT(*) as total FROM enrollments";
$stats['total_enrollments'] = $conn->query($enrollmentsQuery)->fetchColumn();

// Recent activity
$recentUsersQuery = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$recentUsersStmt = $conn->prepare($recentUsersQuery);
$recentUsersStmt->execute();
$recentUsers = $recentUsersStmt->fetchAll();

$pendingCoursesQuery = "SELECT c.*, u.full_name as teacher_name 
                        FROM courses c 
                        JOIN users u ON c.teacher_id = u.id
                        WHERE c.approval_status = 'pending' 
                        ORDER BY c.created_at DESC 
                        LIMIT 5";
$pendingCoursesStmt = $conn->prepare($pendingCoursesQuery);
$pendingCoursesStmt->execute();
$pendingCourses = $pendingCoursesStmt->fetchAll();

$pageTitle = 'Admin Dashboard - Study Hub';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="flex-between" style="margin-bottom: 2rem;">
            <div>
                <h1>Admin Dashboard</h1>
                <p style="color: var(--gray); margin-top: 0.5rem;">Manage and monitor your LMS platform</p>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-4" style="margin-bottom: 2rem; gap: 1.5rem;">
            <div class="card" style="background: linear-gradient(135deg, #0A8BCB, #1E9ED8); color: white;">
                <div class="flex-between">
                    <div>
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem;">Total Students</p>
                        <h2 style="color: white; font-size: 2.5rem;"><?php echo $stats['student_count'] ?? 0; ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #10B981, #059669); color: white;">
                <div class="flex-between">
                    <div>
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem;">Total Teachers</p>
                        <h2 style="color: white; font-size: 2.5rem;"><?php echo $stats['teacher_count'] ?? 0; ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #F59E0B, #D97706); color: white;">
                <div class="flex-between">
                    <div>
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem;">Total Courses</p>
                        <h2 style="color: white; font-size: 2.5rem;"><?php echo $stats['total_courses']; ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: white;">
                <div class="flex-between">
                    <div>
                        <p style="color: rgba(255,255,255,0.8); margin-bottom: 0.5rem;">Enrollments</p>
                        <h2 style="color: white; font-size: 2.5rem;"><?php echo $stats['total_enrollments']; ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alerts -->
        <?php if ($stats['pending_courses'] > 0): ?>
        <div class="alert alert-warning" style="margin-bottom: 2rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>You have <strong><?php echo $stats['pending_courses']; ?></strong> course(s) pending approval.</span>
            <a href="course-approval.php" class="btn btn-sm btn-primary" style="margin-left: auto;">Review Now</a>
        </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-2" style="gap: 2rem;">
            <!-- Recent Users -->
            <div class="card">
                <div class="card-header">
                    <div class="flex-between">
                        <h3 class="card-title"><i class="fas fa-user-plus"></i> Recent Users</h3>
                        <a href="users.php" style="color: var(--primary-blue); font-size: 0.9rem;">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($recentUsers) > 0): ?>
                        <?php foreach ($recentUsers as $user): ?>
                            <div style="padding: 1rem; border-bottom: 1px solid #E5E7EB;">
                                <div class="flex-between" style="margin-bottom: 0.5rem;">
                                    <div>
                                        <h4 style="margin: 0; font-size: 1rem;"><?php echo $user['full_name']; ?></h4>
                                        <p style="color: var(--gray); font-size: 0.875rem; margin: 0.25rem 0;">
                                            <?php echo $user['email']; ?>
                                        </p>
                                    </div>
                                    <div style="text-align: right;">
                                        <span class="badge badge-<?php 
                                            echo $user['role'] === 'student' ? 'primary' : 
                                                 ($user['role'] === 'teacher' ? 'success' : 'warning'); 
                                        ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                        <p style="color: var(--gray); font-size: 0.75rem; margin-top: 0.25rem;">
                                            <?php echo timeAgo($user['created_at']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem 1rem;">
                            <i class="fas fa-users" style="font-size: 3rem; color: var(--gray); opacity: 0.3;"></i>
                            <p style="color: var(--gray); margin-top: 1rem;">No recent users</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Pending Course Approvals -->
            <div class="card">
                <div class="card-header">
                    <div class="flex-between">
                        <h3 class="card-title"><i class="fas fa-clock"></i> Pending Approvals</h3>
                        <a href="course-approval.php" style="color: var(--primary-blue); font-size: 0.9rem;">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($pendingCourses) > 0): ?>
                        <?php foreach ($pendingCourses as $course): ?>
                            <div style="padding: 1rem; border-bottom: 1px solid #E5E7EB;">
                                <div class="flex-between" style="margin-bottom: 0.5rem;">
                                    <div>
                                        <h4 style="margin: 0; font-size: 1rem;"><?php echo $course['title']; ?></h4>
                                        <p style="color: var(--gray); font-size: 0.875rem; margin: 0.25rem 0;">
                                            <i class="fas fa-user"></i> <?php echo $course['teacher_name']; ?>
                                        </p>
                                    </div>
                                    <span class="badge badge-warning">Pending</span>
                                </div>
                                <div class="flex-between" style="margin-top: 0.75rem;">
                                    <span style="color: var(--gray); font-size: 0.875rem;">
                                        <i class="fas fa-clock"></i> <?php echo timeAgo($course['created_at']); ?>
                                    </span>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button onclick="approveCourse(<?php echo $course['id']; ?>)" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button onclick="rejectCourse(<?php echo $course['id']; ?>)" class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem 1rem;">
                            <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--success); opacity: 0.5;"></i>
                            <p style="color: var(--gray); margin-top: 1rem;">No pending approvals</p>
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
                    <a href="users.php" class="card" style="text-align: center; padding: 2rem 1rem; text-decoration: none;">
                        <i class="fas fa-users-cog" style="font-size: 2.5rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 1rem;">Manage Users</h4>
                    </a>
                    <a href="courses.php" class="card" style="text-align: center; padding: 2rem 1rem; text-decoration: none;">
                        <i class="fas fa-book" style="font-size: 2.5rem; color: var(--success); margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 1rem;">Manage Courses</h4>
                    </a>
                    <a href="reports.php" class="card" style="text-align: center; padding: 2rem 1rem; text-decoration: none;">
                        <i class="fas fa-chart-line" style="font-size: 2.5rem; color: var(--warning); margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 1rem;">View Reports</h4>
                    </a>
                    <a href="settings.php" class="card" style="text-align: center; padding: 2rem 1rem; text-decoration: none;">
                        <i class="fas fa-cog" style="font-size: 2.5rem; color: var(--info); margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 1rem;">Settings</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>
