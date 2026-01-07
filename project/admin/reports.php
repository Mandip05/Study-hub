<?php
/**
 * Admin Reports Page
 * Study Hub LMS - Generate system reports
 */

require_once '../config/config.php';
requireRole('admin');

$db = new Database();
$conn = $db->getConnection();

$pageTitle = 'Reports - Admin Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> Reports</h1>
            <p>Generate and view system reports</p>
        </div>
        
        <div class="grid grid-cols-3" style="gap: 2rem;">
            <div class="card">
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-users" style="font-size: 3rem; color: var(--primary-blue);"></i>
                    <h3 style="margin: 1rem 0;">User Report</h3>
                    <p style="color: var(--gray); font-size: 0.9rem;">Generate detailed user statistics</p>
                    <button class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-download"></i> Generate
                    </button>
                </div>
            </div>
            
            <div class="card">
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-book" style="font-size: 3rem; color: var(--success);"></i>
                    <h3 style="margin: 1rem 0;">Course Report</h3>
                    <p style="color: var(--gray); font-size: 0.9rem;">Enrollment and completion data</p>
                    <button class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-download"></i> Generate
                    </button>
                </div>
            </div>
            
            <div class="card">
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-calendar-check" style="font-size: 3rem; color: var(--warning);"></i>
                    <h3 style="margin: 1rem 0;">Attendance Report</h3>
                    <p style="color: var(--gray); font-size: 0.9rem;">System-wide attendance statistics</p>
                    <button class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-download"></i> Generate
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_reports', 'Viewed reports page');
?>
