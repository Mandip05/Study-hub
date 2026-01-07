<?php
/**
 * Student Certificates Page
 * Study Hub LMS - View and download certificates
 */

require_once '../config/config.php';
requireRole('student');

$db = new Database();
$conn = $db->getConnection();
$studentId = getCurrentUserId();

// Get certificates
$certificatesQuery = "SELECT cert.*, c.title as course_title, c.description as course_description,
                      u.full_name as teacher_name
                      FROM certificates cert
                      JOIN courses c ON cert.course_id = c.id
                      JOIN users u ON c.teacher_id = u.id
                      WHERE cert.student_id = :student_id
                      ORDER BY cert.issued_at DESC";
$stmt = $conn->prepare($certificatesQuery);
$stmt->execute([':student_id' => $studentId]);
$certificates = $stmt->fetchAll();

$pageTitle = 'Certificates - Study Hub';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-certificate"></i> My Certificates</h1>
            <p>View and download your earned certificates</p>
        </div>
        
        <?php if (count($certificates) > 0): ?>
            <div class="grid grid-cols-2" style="gap: 2rem;">
                <?php foreach ($certificates as $cert): ?>
                <div class="card" style="overflow: hidden;">
                    <div style="background: linear-gradient(135deg, #F59E0B, #D97706); padding: 2rem; text-align: center; color: white;">
                        <i class="fas fa-award" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                        <h3 style="color: white; margin: 0;">Certificate of Completion</h3>
                    </div>
                    <div style="padding: 1.5rem;">
                        <h3 style="margin: 0 0 1rem 0;"><?php echo htmlspecialchars($cert['course_title']); ?></h3>
                        <div style="color: var(--gray); margin-bottom: 1rem;">
                            <p style="margin: 0.5rem 0;">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <strong>Instructor:</strong> <?php echo htmlspecialchars($cert['teacher_name']); ?>
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <i class="fas fa-calendar"></i>
                                <strong>Issued:</strong> <?php echo formatDate($cert['issue_date']); ?>
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <i class="fas fa-barcode"></i>
                                <strong>Certificate Code:</strong> <?php echo $cert['certificate_code']; ?>
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <i class="fas fa-check-circle"></i>
                                <strong>Status:</strong> 
                                <span class="badge badge-<?php echo $cert['verification_status'] === 'verified' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($cert['verification_status']); ?>
                                </span>
                            </p>
                        </div>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <?php if ($cert['pdf_path']): ?>
                            <a href="<?php echo SITE_URL . '/' . $cert['pdf_path']; ?>" download class="btn btn-primary" style="flex: 1;">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                            <?php endif; ?>
                            <a href="verify-certificate.php?code=<?php echo $cert['certificate_code']; ?>" class="btn btn-outline" style="flex: 1;">
                                <i class="fas fa-shield-alt"></i> Verify
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 4rem 2rem;">
                <i class="fas fa-certificate" style="font-size: 5rem; color: var(--gray); opacity: 0.3;"></i>
                <h3 style="margin-top: 1.5rem; color: var(--dark-text);">No Certificates Earned Yet</h3>
                <p style="color: var(--gray); margin: 1rem 0 2rem 0;">
                    Complete your courses to earn certificates!<br>
                    Certificates are automatically generated when you complete 100% of a course.
                </p>
                <a href="courses.php" class="btn btn-primary">
                    <i class="fas fa-book"></i> View My Courses
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_certificates', 'Viewed certificates page');
?>
