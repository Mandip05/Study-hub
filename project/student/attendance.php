<?php
/**
 * Student Attendance Page
 * Study Hub LMS - View attendance records
 */

require_once '../config/config.php';
requireRole('student');

$db = new Database();
$conn = $db->getConnection();
$studentId = getCurrentUserId();

// Get attendance statistics
$statsQuery = "SELECT 
    COUNT(CASE WHEN status = 'present' THEN 1 END) as present_count,
    COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent_count,
    COUNT(CASE WHEN status = 'late' THEN 1 END) as late_count,
    COUNT(*) as total_records
    FROM attendance 
    WHERE student_id = :student_id";
$stmt = $conn->prepare($statsQuery);
$stmt->execute([':student_id' => $studentId]);
$stats = $stmt->fetch();
$attendancePercentage = $stats['total_records'] > 0 
    ? round(($stats['present_count'] / $stats['total_records']) * 100, 1) 
    : 0;

// Get attendance records
$attendanceQuery = "SELECT a.*, c.title as course_title, c.id as course_id
                    FROM attendance a
                    JOIN courses c ON a.course_id = c.id
                    WHERE a.student_id = :student_id
                    ORDER BY a.attendance_date DESC, a.marked_at DESC
                    LIMIT 50";
$stmt = $conn->prepare($attendanceQuery);
$stmt->execute([':student_id' => $studentId]);
$attendanceRecords = $stmt->fetchAll();

$pageTitle = 'Attendance - Study Hub';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-calendar-check"></i> My Attendance</h1>
            <p>Track your class attendance records</p>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-4" style="gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #10B981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Present</p>
                        <h3 style="margin: 0.25rem 0 0 0; font-size: 2rem; color: var(--success);"><?php echo $stats['present_count']; ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #EF4444, #DC2626); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Absent</p>
                        <h3 style="margin: 0.25rem 0 0 0; font-size: 2rem; color: var(--danger);"><?php echo $stats['absent_count']; ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #F59E0B, #D97706); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Late</p>
                        <h3 style="margin: 0.25rem 0 0 0; font-size: 2rem; color: var(--warning);"><?php echo $stats['late_count']; ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 60px; height: 60px; border-radius: 12px; background: linear-gradient(135deg, #0A8BCB, #1E9ED8); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div>
                        <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Percentage</p>
                        <h3 style="margin: 0.25rem 0 0 0; font-size: 2rem; color: var(--primary-blue);"><?php echo $attendancePercentage; ?>%</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Attendance Records -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Attendance Records</h3>
            </div>
            <div class="card-body">
                <?php if (count($attendanceRecords) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Recorded At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceRecords as $record): ?>
                            <tr>
                                <td><i class="fas fa-calendar"></i> <?php echo formatDate($record['date']); ?></td>
                                <td><?php echo htmlspecialchars($record['course_title']); ?></td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'present' => 'success',
                                        'absent' => 'danger',
                                        'late' => 'warning'
                                    ];
                                    $statusIcons = [
                                        'present' => 'check-circle',
                                        'absent' => 'times-circle',
                                        'late' => 'clock'
                                    ];
                                    ?>
                                    <span class="badge badge-<?php echo $statusColors[$record['status']]; ?>">
                                        <i class="fas fa-<?php echo $statusIcons[$record['status']]; ?>"></i>
                                        <?php echo ucfirst($record['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $record['remarks'] ? htmlspecialchars($record['remarks']) : '-'; ?></td>
                                <td style="color: var(--gray); font-size: 0.9rem;">
                                    <?php echo timeAgo($record['created_at']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem 1rem;">
                        <i class="fas fa-calendar-times" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem; color: var(--dark-text);">No Attendance Records</h3>
                        <p style="color: var(--gray);">Your attendance records will appear here</p>
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
