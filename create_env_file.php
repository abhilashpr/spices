<?php
/**
 * Create .env File Script
 * Run this file to create the .env file with email configuration
 */

$envContent = <<<'ENV'
# Gmail SMTP Configuration
# Replace these values with your actual Gmail credentials

# Gmail SMTP Settings
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=axclue@gmail.com
SMTP_PASSWORD=aeokohsjwvkgbhon
SMTP_FROM_EMAIL=axclue@gmail.com
SMTP_FROM_NAME=Wynvalley

# Application Settings
APP_NAME=Wynvalley
APP_URL=http://localhost/online-sp

# Session & Security
SESSION_LIFETIME=7200
TOKEN_LIFETIME=86400
ENV;

$envFile = __DIR__ . '/.env';

if (file_exists($envFile)) {
    echo "<h1>⚠️ .env file already exists!</h1>";
    echo "<p>Current .env file content:</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents($envFile)) . "</pre>";
    echo "<p>If you want to overwrite it, delete the existing file first.</p>";
} else {
    if (file_put_contents($envFile, $envContent)) {
        echo "<h1>✅ .env file created successfully!</h1>";
        echo "<p>The .env file has been created with the following configuration:</p>";
        echo "<pre>" . htmlspecialchars($envContent) . "</pre>";
        echo "<p><strong>File location:</strong> " . htmlspecialchars($envFile) . "</p>";
        echo "<p style='color: green;'><strong>✅ You can now test email sending!</strong></p>";
    } else {
        echo "<h1>❌ Failed to create .env file</h1>";
        echo "<p>Please check file permissions. The directory must be writable.</p>";
        echo "<p><strong>Manual creation:</strong> Create a file named <code>.env</code> in the root directory with the following content:</p>";
        echo "<pre>" . htmlspecialchars($envContent) . "</pre>";
    }
}

echo "<hr>";
echo "<p><a href='test_email.php'>Test Email</a> | <a href='register'>Go to Register</a></p>";

