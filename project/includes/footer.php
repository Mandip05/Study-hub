    <!-- Footer -->
    <footer style="background: var(--dark-text); color: white; padding: 3rem 0; margin-top: 4rem;">
        <div class="container">
            <div class="grid grid-cols-4" style="gap: 2rem;">
                <div>
                    <h3 style="color: white; margin-bottom: 1rem;">Study Hub</h3>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">
                        A modern learning management system designed for students, teachers, and educational institutions.
                    </p>
                </div>
                
                <div>
                    <h4 style="color: white; margin-bottom: 1rem;">Quick Links</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 0.5rem;">
                            <a href="<?php echo SITE_URL; ?>/index.php" style="color: rgba(255,255,255,0.7);">Home</a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="<?php echo SITE_URL; ?>/courses.php" style="color: rgba(255,255,255,0.7);">Courses</a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="<?php echo SITE_URL; ?>/library.php" style="color: rgba(255,255,255,0.7);">Library</a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="<?php echo SITE_URL; ?>/about.php" style="color: rgba(255,255,255,0.7);">About</a>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h4 style="color: white; margin-bottom: 1rem;">Categories</h4>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 0.5rem;">
                            <a href="#" style="color: rgba(255,255,255,0.7);">CSIT</a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="#" style="color: rgba(255,255,255,0.7);">BCA</a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="#" style="color: rgba(255,255,255,0.7);">Engineering</a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="#" style="color: rgba(255,255,255,0.7);">Entrance Prep</a>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h4 style="color: white; margin-bottom: 1rem;">Connect With Us</h4>
                    <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                    </div>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">
                        <i class="fas fa-envelope"></i> info@studyhub.com<br>
                        <i class="fas fa-phone"></i> +977 01-4567890
                    </p>
                </div>
            </div>
            
            <div style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 2rem; padding-top: 1.5rem; text-align: center;">
                <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">
                    &copy; <?php echo date('Y'); ?> Study Hub. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <!-- Additional Scripts -->
    <?php if(isset($additionalJS)) echo $additionalJS; ?>
</body>
</html>
