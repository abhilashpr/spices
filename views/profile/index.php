<?php
/**
 * Profile View
 */
require_once __DIR__ . '/../../app/helpers/helpers.php';
$user = $user ?? [];
?>
<section class="profile-page">
  <div class="profile-container">
    <div class="profile-header">
      <h1>My Profile</h1>
      <p>Manage your account information</p>
    </div>

    <div class="profile-content">
      <div class="profile-card">
        <h2>Personal Information</h2>
        <div class="profile-info">
          <div class="info-item">
            <label>Name:</label>
            <span><?= e($user['firstname'] ?? '') . ' ' . e($user['lastname'] ?? '') ?></span>
          </div>
          <div class="info-item">
            <label>Email:</label>
            <span><?= e($user['email'] ?? '') ?></span>
          </div>
          <div class="info-item">
            <label>Status:</label>
            <span class="badge badge-<?= ($user['status'] ?? '') === 'active' ? 'success' : 'warning' ?>">
              <?= ucfirst($user['status'] ?? 'inactive') ?>
            </span>
          </div>
          <div class="info-item">
            <label>Member Since:</label>
            <span><?= date('F d, Y', strtotime($user['created_at'] ?? 'now')) ?></span>
          </div>
        </div>
      </div>

      <div class="profile-actions">
        <a href="<?= url('profile/edit') ?>" class="btn btn-primary">Edit Profile</a>
        <a href="<?= url('logout') ?>" class="btn btn-outline">Logout</a>
      </div>
    </div>
  </div>
</section>

<style>
.profile-page {
  min-height: 80vh;
  padding: 40px 20px;
  background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 50%, #f1f8f4 100%);
}

.profile-container {
  max-width: 800px;
  margin: 0 auto;
}

.profile-header {
  text-align: center;
  margin-bottom: 40px;
}

.profile-header h1 {
  font-family: 'Playfair Display', serif;
  font-size: 2.5rem;
  color: #09342a;
  margin: 0 0 10px 0;
}

.profile-header p {
  color: rgba(9, 52, 42, 0.6);
  font-size: 1rem;
}

.profile-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.profile-card {
  background: white;
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

.profile-card h2 {
  font-size: 1.5rem;
  color: #09342a;
  margin: 0 0 20px 0;
  padding-bottom: 15px;
  border-bottom: 2px solid rgba(50, 198, 141, 0.2);
}

.profile-info {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.info-item {
  display: flex;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

.info-item:last-child {
  border-bottom: none;
}

.info-item label {
  font-weight: 600;
  color: #09342a;
  min-width: 150px;
}

.info-item span {
  color: rgba(9, 52, 42, 0.8);
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
}

.badge-success {
  background: rgba(50, 198, 141, 0.1);
  color: #32c68d;
}

.badge-warning {
  background: rgba(255, 193, 7, 0.1);
  color: #ffc107;
}

.profile-actions {
  display: flex;
  gap: 15px;
  justify-content: center;
  margin-top: 20px;
}

.btn {
  padding: 12px 24px;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.btn-primary {
  background: linear-gradient(135deg, #32c68d, #28a870);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(50, 198, 141, 0.3);
}

.btn-outline {
  background: transparent;
  color: #32c68d;
  border-color: #32c68d;
}

.btn-outline:hover {
  background: #32c68d;
  color: white;
}
</style>

