<?php
/**
 * Teacher QR Attendance Page
 * Study Hub LMS - QR code attendance system
 */

require_once '../config/config.php';
requireRole('teacher');

$pageTitle = 'QR Attendance - Teacher Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-qrcode"></i> QR Code Attendance</h1>
            <p>Generate QR codes for attendance</p>
        </div>
        
        <div class="card" style="text-align: center; padding: 4rem;">
            <i class="fas fa-qrcode" style="font-size: 5rem; color: var(--primary-blue);"></i>
            <h3 style="margin: 1.5rem 0;">QR Attendance System</h3>
            <p style="color: var(--gray); margin: 1rem 0 2rem 0;">
                Generate QR codes for students to mark their attendance
            </p>
            <button class="btn btn-primary btn-lg">
                <i class="fas fa-qrcode"></i> Generate QR Code
            </button>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_qr_attendance', 'Viewed QR attendance page');
?>
