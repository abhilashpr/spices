<?php
$title = 'Dashboard';
ob_start();
?>
<div class="admin-header">
    <h1>Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($username ?? 'Admin') ?></p>
</div>
<div class="admin-panel">
    <p>Select a section from the sidebar to get started.</p>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php';
?>

