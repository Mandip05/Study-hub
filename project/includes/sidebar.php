<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <i class="fas fa-graduation-cap"></i>
        Study Hub
    </div>
    
    <ul class="sidebar-menu">
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        $role = getCurrentUserRole();
        
        // Define menu items based on role
        $menuItems = [];
        
        if($role === 'student') {
            $menuItems = [
                ['icon' => 'tachometer-alt', 'label' => 'Dashboard', 'url' => 'dashboard.php'],
                ['icon' => 'book', 'label' => 'My Courses', 'url' => 'courses.php'],
                ['icon' => 'tasks', 'label' => 'Assignments', 'url' => 'assignments.php'],
                ['icon' => 'calendar-check', 'label' => 'Attendance', 'url' => 'attendance.php'],
                ['icon' => 'chart-bar', 'label' => 'Grades', 'url' => 'grades.php'],
                ['icon' => 'book-reader', 'label' => 'Library', 'url' => '../library.php'],
                ['icon' => 'certificate', 'label' => 'Certificates', 'url' => 'certificates.php'],
                ['icon' => 'envelope', 'label' => 'Messages', 'url' => 'messages.php'],
            ];
        } elseif($role === 'teacher') {
            $menuItems = [
                ['icon' => 'tachometer-alt', 'label' => 'Dashboard', 'url' => 'dashboard.php'],
                ['icon' => 'chalkboard-teacher', 'label' => 'My Courses', 'url' => 'courses.php'],
                ['icon' => 'plus-circle', 'label' => 'Create Course', 'url' => 'create-course.php'],
                ['icon' => 'tasks', 'label' => 'Assignments', 'url' => 'assignments.php'],
                ['icon' => 'user-check', 'label' => 'Attendance', 'url' => 'attendance.php'],
                ['icon' => 'qrcode', 'label' => 'QR Attendance', 'url' => 'qr-attendance.php'],
                ['icon' => 'graduation-cap', 'label' => 'Gradebook', 'url' => 'gradebook.php'],
                ['icon' => 'users', 'label' => 'Students', 'url' => 'students.php'],
                ['icon' => 'envelope', 'label' => 'Messages', 'url' => 'messages.php'],
            ];
        } elseif($role === 'admin') {
            $menuItems = [
                ['icon' => 'tachometer-alt', 'label' => 'Dashboard', 'url' => 'dashboard.php'],
                ['icon' => 'users', 'label' => 'Users', 'url' => 'users.php'],
                ['icon' => 'book', 'label' => 'Courses', 'url' => 'courses.php'],
                ['icon' => 'check-circle', 'label' => 'Course Approval', 'url' => 'course-approval.php'],
                ['icon' => 'book-reader', 'label' => 'Library', 'url' => 'library.php'],
                ['icon' => 'chart-line', 'label' => 'Reports', 'url' => 'reports.php'],
                ['icon' => 'bell', 'label' => 'Notifications', 'url' => 'notifications.php'],
                ['icon' => 'cog', 'label' => 'Settings', 'url' => 'settings.php'],
            ];
        }
        
        foreach($menuItems as $item):
            $isActive = $currentPage === $item['url'] ? 'active' : '';
        ?>
            <li>
                <a href="<?php echo $item['url']; ?>" class="<?php echo $isActive; ?>">
                    <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                    <span><?php echo $item['label']; ?></span>
                </a>
            </li>
        <?php endforeach; ?>
        
        <li style="margin-top: 2rem;">
            <a href="<?php echo SITE_URL; ?>/auth/logout.php" style="color: #EF4444;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>
