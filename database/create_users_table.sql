-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(100) NOT NULL,
  `lastname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
  `login_type` ENUM('web', 'gmail') DEFAULT 'web',
  `gmail_id` VARCHAR(255) DEFAULT NULL,
  `otp` VARCHAR(6) DEFAULT NULL,
  `otp_expires_at` DATETIME DEFAULT NULL,
  `email_verified` TINYINT(1) DEFAULT 0,
  `auth_token` VARCHAR(255) DEFAULT NULL,
  `token_expires_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `gmail_id` (`gmail_id`),
  KEY `auth_token` (`auth_token`),
  KEY `email_verified` (`email_verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

