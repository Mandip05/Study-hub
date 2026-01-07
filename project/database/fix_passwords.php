<?php
/**
 * Fix Demo User Passwords
 * Run this file once to update demo account passwords
 */

require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Generate hash for 'admin123'
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    echo "Updating passwords...\n";
    
    // Update all demo user passwords
    $query = "UPDATE users SET password = :password WHERE email IN ('admin@studyhub.com', 'teacher@studyhub.com', 'student@studyhub.com')";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->execute();
    
    echo "✓ Passwords updated successfully!\n";
    echo "All demo accounts now use password: admin123\n\n";
    
    // Verify the update
    echo "Demo Accounts:\n";
    $verifyQuery = "SELECT full_name, email, role FROM users WHERE email IN ('admin@studyhub.com', 'teacher@studyhub.com', 'student@studyhub.com')";
    $verifyStmt = $conn->prepare($verifyQuery);
    $verifyStmt->execute();
    $users = $verifyStmt->fetchAll();
    
    foreach ($users as $user) {
        echo "- {$user['full_name']} ({$user['email']}) - Role: {$user['role']}\n";
    }
    
    echo "\n✓ You can now login with any of these accounts using password: admin123\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
