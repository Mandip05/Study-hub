<?php
/**
 * Admin Library Management Page
 * Study Hub LMS - Manage library resources
 */

require_once '../config/config.php';
requireRole('admin');

$db = new Database();
$conn = $db->getConnection();

// Get all library resources
$resourcesQuery = "SELECT lr.*, u.full_name as uploaded_by_name
                   FROM library_resources lr
                   JOIN users u ON lr.uploaded_by = u.id
                   ORDER BY lr.created_at DESC";
$stmt = $conn->prepare($resourcesQuery);
$stmt->execute();
$resources = $stmt->fetchAll();

$pageTitle = 'Library Management - Admin Panel';
include '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book-reader"></i> Library Management</h1>
            <p>Manage digital library resources</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="flex-between">
                    <h3 class="card-title">All Resources</h3>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Resource
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($resources) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Uploaded By</th>
                                <th>Status</th>
                                <th>Downloads</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resources as $resource): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($resource['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($resource['category']); ?></td>
                                <td><?php echo htmlspecialchars($resource['author']); ?></td>
                                <td><?php echo htmlspecialchars($resource['uploaded_by_name']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $resource['approval_status'] === 'approved' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($resource['approval_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $resource['download_count']; ?></td>
                                <td><?php echo formatDate($resource['created_at']); ?></td>
                                <td>
                                    <a href="<?php echo SITE_URL . '/' . $resource['file_path']; ?>" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-book" style="font-size: 4rem; color: var(--gray); opacity: 0.3;"></i>
                        <h3 style="margin-top: 1rem;">No resources yet</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</div>

<?php
logActivity(getCurrentUserId(), 'view_library', 'Viewed library management page');
?>
