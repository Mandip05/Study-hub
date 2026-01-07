<?php
/**
 * Student Grades Page
 * Study Hub LMS - View grades and performance
 */

require_once '../config/config.php';
requireRole('student');

$db = new Database();
$conn = $db->getConnection();
$studentId = getCurrentUserId();

// Get grades by course
$gradesQuery = "SELECT g.*, c.title as course_title, c.id as course_id, u.full_name as teacher_name
                FROM grades g
                JOIN courses c ON g.course_id = c.id
                JOIN users u ON c.teacher_id = u.id
                WHERE g.student_id = :student_id
                ORDER BY g.calculated_at DESC";
$stmt = $conn->prepare($gradesQuery);
$stmt->execute([':student_id' => $studentId]);
$grades = $stmt->fetchAll();

// Calculate overall GPA
$totalGrade = 0;
$gradeCount = 0;
foreach ($grades as $grade) {
    if ($grade['total_grade']) {
        $totalGrade += $grade['total_grade'];
        $gradeCount++;
    }
}
$overallGrade = $gradeCount > 0 ? round($totalGrade / $gradeCount, 2) : 0;

$pageTitle = 'Grades - Study Hub';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-chart-bar"></i> My Grades</h1>
            <p>View your academic performance</p>
        </div>
        
        <!-- Overall Performance -->
        <div class="grid grid-cols-3" style="gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card" style="background: linear-gradient(135deg, #0A8BCB, #1E9ED8); color: white;">
                <div style="text-align: center; padding: 1rem;">
                    <p style="color: rgba(255,255,255,0.8); margin: 0; font-size: 0.9rem;">Overall Grade</p>
                    <h2 style="color: white; font-size: 3rem; margin: 0.5rem 0;"><?php echo $overallGrade; ?>%</h2>
                    <p style="color: rgba(255,255,255,0.7); margin: 0;">Based on <?php echo $gradeCount; ?> courses</p>
                </div>
            </div>
            
            <div class="card">
                <div style="text-align: center; padding: 1rem;">
                    <i class="fas fa-book" style="font-size: 2rem; color: var(--primary-blue);"></i>
                    <h3 style="margin: 0.5rem 0; font-size: 2rem;"><?php echo count($grades); ?></h3>
                    <p style="color: var(--gray); margin: 0;">Courses Graded</p>
                </div>
            </div>
            
            <div class="card">
                <div style="text-align: center; padding: 1rem;">
                    <i class="fas fa-trophy" style="font-size: 2rem; color: #F59E0B;"></i>
                    <h3 style="margin: 0.5rem 0; font-size: 2rem;">
                        <?php
                        $aGrades = 0;
                        foreach ($grades as $grade) {
                            if ($grade['total_grade'] >= 90) $aGrades++;
                        }
                        echo $aGrades;
                        ?>
                    </h3>
                    <p style="color: var(--gray); margin: 0;">A Grades</p>
                </div>
            </div>
        </div>
        
        <!-- Grades Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Course Grades</h3>
            </div>
            <div class="card-body">
                <?php if (count($grades) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Teacher</th>
                                <th>Assignments</th>
                                <th>Midterm</th>
                                <th>Final</th>
                                <th>Total Grade</th>
                                <th>Letter Grade</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grades as $grade): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($grade['course_title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($grade['teacher_name']); ?></td>
                                <td><?php echo $grade['assignment_grade'] ?? '-'; ?>%</td>
                                <td><?php echo $grade['midterm_grade'] ?? '-'; ?>%</td>
                                <td><?php echo $grade['final_grade'] ?? '-'; ?>%</td>
                                <td>
                                    <strong style="font-size: 1.1rem; color: var(--primary-blue);">
                                        <?php echo $grade['total_grade'] ?? '-'; ?>%
                                    </strong>
                                </td>
                                <td>
                                    <?php
                                    $total = $grade['total_grade'];
                                    $letter = 'F';
                                    $color = 'danger';
                                    if ($total >= 90) { $letter = 'A'; $color = 'success'; }
                                    elseif ($total >= 80) { $letter = 'B'; $color = 'info'; }
                                    elseif ($total >= 70) { $letter = 'C'; $color = 'warning'; }
                                    elseif ($total >= 60) { $letter = 'D'; $color = 'warning'; }
                                    ?>
                                    <span class="badge badge-<?php echo $color; ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                        <?php echo $letter; ?>
                                    </span>
                                </td>
                                <td style="color: var(--gray); font-size: 0.9rem;">
                                    <?php echo formatDate($grade['created_at']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem 1rem;">
                        <i class="fas fa-chart-bar" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem; color: var(--dark-text);">No Grades Yet</h3>
                        <p style="color: var(--gray);">Your grades will appear here once teachers grade your work</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_grades', 'Viewed grades page');
?>
