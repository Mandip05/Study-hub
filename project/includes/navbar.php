<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="<?php echo SITE_URL; ?>/index.php" class="navbar-logo">
            <i class="fas fa-graduation-cap"></i>
            Study Hub
        </a>
        
        <div class="navbar-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search courses, library..." id="globalSearch">
        </div>
        
        <ul class="navbar-menu">
            <li><a href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>/courses.php">Courses</a></li>
            <li><a href="<?php echo SITE_URL; ?>/library.php">Library</a></li>
            <li><a href="<?php echo SITE_URL; ?>/about.php">About</a></li>
        </ul>
        
        <div class="navbar-actions">
            <?php if(isLoggedIn()): ?>
                <div class="notification-icon" onclick="toggleNotifications()" style="cursor: pointer; position: relative;">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <!-- Notifications Dropdown -->
                <div id="notificationMenu" style="display: none; position: absolute; top: 100%; right: 240px; background: white; box-shadow: var(--shadow-lg); border-radius: var(--radius-md); padding: 1rem; min-width: 320px; max-height: 400px; overflow-y: auto; z-index: 1000;">
                    <h4 style="margin-bottom: 1rem; color: var(--dark-text);">Notifications</h4>
                    <div class="notification-item" style="padding: 0.75rem; border-bottom: 1px solid #E5E7EB; cursor: pointer;">
                        <div style="display: flex; gap: 0.75rem;">
                            <i class="fas fa-info-circle" style="color: var(--primary-blue); margin-top: 0.25rem;"></i>
                            <div>
                                <p style="margin: 0; font-weight: 500;">New Assignment Posted</p>
                                <p style="margin: 0; font-size: 0.85rem; color: var(--gray);">Web Development - Due next week</p>
                                <span style="font-size: 0.75rem; color: var(--gray);">2 hours ago</span>
                            </div>
                        </div>
                    </div>
                    <div class="notification-item" style="padding: 0.75rem; border-bottom: 1px solid #E5E7EB; cursor: pointer;">
                        <div style="display: flex; gap: 0.75rem;">
                            <i class="fas fa-check-circle" style="color: var(--success); margin-top: 0.25rem;"></i>
                            <div>
                                <p style="margin: 0; font-weight: 500;">Assignment Graded</p>
                                <p style="margin: 0; font-size: 0.85rem; color: var(--gray);">Your submission received 95/100</p>
                                <span style="font-size: 0.75rem; color: var(--gray);">5 hours ago</span>
                            </div>
                        </div>
                    </div>
                    <div class="notification-item" style="padding: 0.75rem; cursor: pointer;">
                        <div style="display: flex; gap: 0.75rem;">
                            <i class="fas fa-bullhorn" style="color: var(--warning); margin-top: 0.25rem;"></i>
                            <div>
                                <p style="margin: 0; font-weight: 500;">New Announcement</p>
                                <p style="margin: 0; font-size: 0.85rem; color: var(--gray);">Class schedule updated</p>
                                <span style="font-size: 0.75rem; color: var(--gray);">1 day ago</span>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/<?php echo getCurrentUserRole(); ?>/notifications.php" style="display: block; text-align: center; padding: 0.75rem; margin-top: 0.5rem; color: var(--primary-blue); font-weight: 500;">
                        View All Notifications
                    </a>
                </div>
                
                <div class="user-profile" onclick="toggleUserMenu()">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <span><?php echo $_SESSION['user_name'] ?? 'User'; ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                
                <!-- User Dropdown Menu -->
                <div id="userMenu" style="display: none; position: absolute; top: 100%; right: 0; background: white; box-shadow: var(--shadow-lg); border-radius: var(--radius-md); padding: 0.5rem; min-width: 180px; z-index: 1000;">
                    <a href="<?php echo SITE_URL; ?>/<?php echo getCurrentUserRole(); ?>/dashboard.php" style="display: block; padding: 0.75rem 1rem; border-radius: var(--radius-sm);">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="<?php echo SITE_URL; ?>/<?php echo getCurrentUserRole(); ?>/profile.php" style="display: block; padding: 0.75rem 1rem; border-radius: var(--radius-sm);">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <a href="<?php echo SITE_URL; ?>/<?php echo getCurrentUserRole(); ?>/settings.php" style="display: block; padding: 0.75rem 1rem; border-radius: var(--radius-sm);">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <hr style="margin: 0.5rem 0; border: none; border-top: 1px solid #E5E7EB;">
                    <a href="<?php echo SITE_URL; ?>/auth/logout.php" style="display: block; padding: 0.75rem 1rem; color: var(--danger); border-radius: var(--radius-sm);">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-outline btn-sm">Login</a>
                <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-primary btn-sm">Sign Up</a>
            <?php endif; ?>
        </div>
        
        <div class="navbar-toggle" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>
