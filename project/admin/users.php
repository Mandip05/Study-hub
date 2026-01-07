<?php
/**
 * User Management System
 * Study Hub LMS - Admin Panel
 */

require_once '../config/config.php';
requireRole('admin');

$db = new Database();
$conn = $db->getConnection();

// Handle user actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // CSRF Token validation
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $message = 'Invalid security token';
        $messageType = 'error';
    } else {
        switch ($_POST['action']) {
            case 'create':
                $fullName = sanitize($_POST['full_name']);
                $email = sanitize($_POST['email']);
                $password = $_POST['password'];
                $role = sanitize($_POST['role']);
                $status = sanitize($_POST['status']);
                
                // Validate inputs
                if (empty($fullName) || empty($email) || empty($password) || empty($role)) {
                    $message = 'All fields are required';
                    $messageType = 'error';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message = 'Invalid email format';
                    $messageType = 'error';
                } else {
                    // Check if email already exists
                    $checkQuery = "SELECT id FROM users WHERE email = :email";
                    $checkStmt = $conn->prepare($checkQuery);
                    $checkStmt->execute([':email' => $email]);
                    
                    if ($checkStmt->rowCount() > 0) {
                        $message = 'Email already exists';
                        $messageType = 'error';
                    } else {
                        // Create user
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $insertQuery = "INSERT INTO users (full_name, email, password, role, status) 
                                      VALUES (:full_name, :email, :password, :role, :status)";
                        $insertStmt = $conn->prepare($insertQuery);
                        
                        if ($insertStmt->execute([
                            ':full_name' => $fullName,
                            ':email' => $email,
                            ':password' => $hashedPassword,
                            ':role' => $role,
                            ':status' => $status
                        ])) {
                            $message = 'User created successfully';
                            $messageType = 'success';
                            logActivity(getCurrentUserId(), 'create_user', "Created user: $email");
                            sendNotification($conn->lastInsertId(), 'Welcome to Study Hub', 'Your account has been created successfully', 'success');
                        } else {
                            $message = 'Failed to create user';
                            $messageType = 'error';
                        }
                    }
                }
                break;
                
            case 'update':
                $userId = intval($_POST['user_id']);
                $fullName = sanitize($_POST['full_name']);
                $email = sanitize($_POST['email']);
                $role = sanitize($_POST['role']);
                $status = sanitize($_POST['status']);
                $phone = sanitize($_POST['phone'] ?? '');
                $address = sanitize($_POST['address'] ?? '');
                
                $updateQuery = "UPDATE users 
                              SET full_name = :full_name, email = :email, role = :role, 
                                  status = :status, phone = :phone, address = :address 
                              WHERE id = :id";
                $updateStmt = $conn->prepare($updateQuery);
                
                if ($updateStmt->execute([
                    ':full_name' => $fullName,
                    ':email' => $email,
                    ':role' => $role,
                    ':status' => $status,
                    ':phone' => $phone,
                    ':address' => $address,
                    ':id' => $userId
                ])) {
                    $message = 'User updated successfully';
                    $messageType = 'success';
                    logActivity(getCurrentUserId(), 'update_user', "Updated user ID: $userId");
                } else {
                    $message = 'Failed to update user';
                    $messageType = 'error';
                }
                break;
                
            case 'delete':
                $userId = intval($_POST['user_id']);
                
                // Prevent deleting self
                if ($userId == getCurrentUserId()) {
                    $message = 'You cannot delete your own account';
                    $messageType = 'error';
                } else {
                    $deleteQuery = "DELETE FROM users WHERE id = :id";
                    $deleteStmt = $conn->prepare($deleteQuery);
                    
                    if ($deleteStmt->execute([':id' => $userId])) {
                        $message = 'User deleted successfully';
                        $messageType = 'success';
                        logActivity(getCurrentUserId(), 'delete_user', "Deleted user ID: $userId");
                    } else {
                        $message = 'Failed to delete user';
                        $messageType = 'error';
                    }
                }
                break;
                
            case 'toggle_status':
                $userId = intval($_POST['user_id']);
                $newStatus = $_POST['new_status'];
                
                $updateQuery = "UPDATE users SET status = :status WHERE id = :id";
                $updateStmt = $conn->prepare($updateQuery);
                
                if ($updateStmt->execute([':status' => $newStatus, ':id' => $userId])) {
                    $message = 'User status updated successfully';
                    $messageType = 'success';
                    logActivity(getCurrentUserId(), 'toggle_user_status', "Changed user ID $userId status to $newStatus");
                    sendNotification($userId, 'Account Status Changed', "Your account status has been changed to $newStatus", 'info');
                } else {
                    $message = 'Failed to update status';
                    $messageType = 'error';
                }
                break;
        }
    }
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Filters
$searchQuery = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// Build query
$whereConditions = [];
$params = [];

if (!empty($searchQuery)) {
    $whereConditions[] = "(full_name LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$searchQuery%";
}

if (!empty($roleFilter)) {
    $whereConditions[] = "role = :role";
    $params[':role'] = $roleFilter;
}

if (!empty($statusFilter)) {
    $whereConditions[] = "status = :status";
    $params[':status'] = $statusFilter;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countQuery = "SELECT COUNT(*) FROM users $whereClause";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute($params);
$totalUsers = $countStmt->fetchColumn();
$totalPages = ceil($totalUsers / $perPage);

// Get users
$usersQuery = "SELECT * FROM users $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$usersStmt = $conn->prepare($usersQuery);
foreach ($params as $key => $value) {
    $usersStmt->bindValue($key, $value);
}
$usersStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$usersStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$usersStmt->execute();
$users = $usersStmt->fetchAll();

$pageTitle = 'User Management - Study Hub Admin';
include '../includes/header.php';
?>

<style>
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--primary-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 600;
    }
    
    .filter-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    
    .filter-bar .form-control {
        min-width: 200px;
    }
    
    .table-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .modal-header h3 {
        margin: 0;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--gray);
    }
</style>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="flex-between" style="margin-bottom: 2rem;">
            <div>
                <h1><i class="fas fa-users"></i> User Management</h1>
                <p style="color: var(--gray); margin-top: 0.5rem;">Manage all users, roles and permissions</p>
            </div>
            <button onclick="openModal('createUserModal')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>
        
        <!-- Alert Messages -->
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'error'; ?>" style="margin-bottom: 1.5rem;">
            <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-4" style="gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card">
                <div class="flex-between">
                    <div>
                        <p style="color: var(--gray); margin-bottom: 0.5rem;">Total Users</p>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $totalUsers; ?></h3>
                    </div>
                    <i class="fas fa-users" style="font-size: 2rem; color: var(--primary-blue); opacity: 0.3;"></i>
                </div>
            </div>
            
            <?php
            $roleStats = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role")->fetchAll(PDO::FETCH_KEY_PAIR);
            ?>
            
            <div class="card">
                <div class="flex-between">
                    <div>
                        <p style="color: var(--gray); margin-bottom: 0.5rem;">Students</p>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $roleStats['student'] ?? 0; ?></h3>
                    </div>
                    <i class="fas fa-user-graduate" style="font-size: 2rem; color: var(--success); opacity: 0.3;"></i>
                </div>
            </div>
            
            <div class="card">
                <div class="flex-between">
                    <div>
                        <p style="color: var(--gray); margin-bottom: 0.5rem;">Teachers</p>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $roleStats['teacher'] ?? 0; ?></h3>
                    </div>
                    <i class="fas fa-chalkboard-teacher" style="font-size: 2rem; color: var(--warning); opacity: 0.3;"></i>
                </div>
            </div>
            
            <div class="card">
                <div class="flex-between">
                    <div>
                        <p style="color: var(--gray); margin-bottom: 0.5rem;">Admins</p>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $roleStats['admin'] ?? 0; ?></h3>
                    </div>
                    <i class="fas fa-user-shield" style="font-size: 2rem; color: var(--danger); opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <form method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                
                <select name="role" class="form-control">
                    <option value="">All Roles</option>
                    <option value="student" <?php echo $roleFilter === 'student' ? 'selected' : ''; ?>>Student</option>
                    <option value="teacher" <?php echo $roleFilter === 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                    <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
                
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="suspended" <?php echo $statusFilter === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                </select>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                
                <?php if ($searchQuery || $roleFilter || $statusFilter): ?>
                <a href="users.php" class="btn btn-outline">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Users Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="flex-start" style="gap: 1rem; align-items: center;">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                        </div>
                                        <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $user['role'] === 'student' ? 'primary' : 
                                             ($user['role'] === 'teacher' ? 'success' : 'warning'); 
                                    ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $user['status'] === 'active' ? 'success' : 
                                             ($user['status'] === 'inactive' ? 'secondary' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($user['created_at']); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <button onclick='editUser(<?php echo json_encode($user); ?>)' class="btn btn-sm btn-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <?php if ($user['id'] != getCurrentUserId()): ?>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="new_status" value="<?php echo $user['status'] === 'active' ? 'suspended' : 'active'; ?>">
                                            <button type="submit" class="btn btn-sm btn-<?php echo $user['status'] === 'active' ? 'warning' : 'success'; ?>" title="<?php echo $user['status'] === 'active' ? 'Suspend' : 'Activate'; ?>">
                                                <i class="fas fa-<?php echo $user['status'] === 'active' ? 'ban' : 'check'; ?>"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 3rem;">
                                    <i class="fas fa-users" style="font-size: 3rem; color: var(--gray); opacity: 0.3;"></i>
                                    <p style="color: var(--gray); margin-top: 1rem;">No users found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div style="padding: 1.5rem; border-top: 1px solid #E5E7EB;">
                <div class="flex-between">
                    <div>
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $perPage, $totalUsers); ?> of <?php echo $totalUsers; ?> users
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchQuery); ?>&role=<?php echo $roleFilter; ?>&status=<?php echo $statusFilter; ?>" class="btn btn-sm btn-outline">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchQuery); ?>&role=<?php echo $roleFilter; ?>&status=<?php echo $statusFilter; ?>" class="btn btn-sm btn-outline">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<!-- Create User Modal -->
<div id="createUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Create New User</h3>
            <button onclick="closeModal('createUserModal')" class="close-modal">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            
            <div class="form-group">
                <label class="form-label">Role *</label>
                <select name="role" class="form-control" required>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" onclick="closeModal('createUserModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit User</h3>
            <button onclick="closeModal('editUserModal')" class="close-modal">&times;</button>
        </div>
        <form method="POST" id="editUserForm">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="user_id" id="edit_user_id">
            
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" id="edit_phone" class="form-control">
            </div>
            
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Role *</label>
                <select name="role" id="edit_role" class="form-control" required>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" id="edit_status" class="form-control" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" onclick="closeModal('editUserModal')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_phone').value = user.phone || '';
    document.getElementById('edit_address').value = user.address || '';
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_status').value = user.status;
    openModal('editUserModal');
}

// Close modal on outside click
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}
</script>

<?php
logActivity(getCurrentUserId(), 'view_users', 'Viewed user management page');
?>
