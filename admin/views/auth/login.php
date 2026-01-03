<?php
$title = 'Admin Login';
ob_start();
?>
<div class="admin-login-page">
    <div class="admin-login-card">
        <div class="admin-login-header">
            <h1>Admin Login</h1>
            <p>Access the admin dashboard</p>
        </div>
        <?php if (isset($error) && $error): ?>
            <div class="admin-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="admin-form-group">
                <label for="username">Username</label>
                <input type="email" id="username" name="username" placeholder="admin@gmail.com" required autofocus>
            </div>
            <div class="admin-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="admin-login-btn">Login</button>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth.php';
?>

