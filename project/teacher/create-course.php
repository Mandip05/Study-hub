<?php
/**
 * Teacher Create Course Page
 * Study Hub LMS - Create new course
 */

require_once '../config/config.php';
requireRole('teacher');

$pageTitle = 'Create Course - Teacher Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-plus-circle"></i> Create New Course</h1>
            <p>Create and publish a new course</p>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Course Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category *</label>
                        <select class="form-control" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Programming">Programming</option>
                            <option value="Business">Business</option>
                            <option value="Design">Design</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Science">Science</option>
                            <option value="Mathematics">Mathematics</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea class="form-control" name="description" rows="6" required></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2" style="gap: 1rem;">
                        <div class="form-group">
                            <label>Level</label>
                            <select class="form-control" name="level">
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Duration (hours)</label>
                            <input type="number" class="form-control" name="duration" min="1">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Course Thumbnail</label>
                        <input type="file" class="form-control" name="thumbnail" accept="image/*">
                    </div>
                    
                    <div class="flex-between" style="margin-top: 2rem;">
                        <a href="courses.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_create_course', 'Viewed create course page');
?>
