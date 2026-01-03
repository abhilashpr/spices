<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
$currentPage = $_GET['page'] ?? 'dashboard';
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <nav>
        <ul class="admin-sidebar-nav">
            <li>
                <a href="<?= admin_url('index.php') ?>" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= admin_url('index.php?page=sliders') ?>" class="<?= $currentPage === 'sliders' ? 'active' : '' ?>">
                    <span>🖼️</span>
                    <span>Sliders</span>
                </a>
            </li>
            <li>
                <a href="<?= admin_url('index.php?page=categories') ?>" class="<?= $currentPage === 'categories' ? 'active' : '' ?>">
                    <span>📁</span>
                    <span>Categories</span>
                </a>
            </li>
            <li>
                <a href="<?= admin_url('index.php?page=subcategories') ?>" class="<?= $currentPage === 'subcategories' ? 'active' : '' ?>">
                    <span>📂</span>
                    <span>Subcategories</span>
                </a>
            </li>
            <li>
                <a href="<?= admin_url('index.php?page=units') ?>" class="<?= $currentPage === 'units' ? 'active' : '' ?>">
                    <span>📏</span>
                    <span>Units</span>
                </a>
            </li>
            <li>
                <a href="<?= admin_url('index.php?page=products') ?>" class="<?= $currentPage === 'products' ? 'active' : '' ?>">
                    <span>📦</span>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a href="<?= admin_url('logout.php') ?>">
                    <span>🚪</span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

