<?php
/**
 * Test Email Sending
 * Run this file to test if email sending works
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/helpers/EmailHelper.php';

echo "<h1>Email Test</h1>";

// Get test email from query string or use default
$testEmail = $_GET['email'] ?? 'iabhilashpr@gmail.com';
$testOTP = rand(100000, 999999);

echo "<p>Testing email sending to: <strong>$testEmail</strong></p>";
echo "<p>Test OTP: <strong>$testOTP</strong></p>";

try {
    $emailHelper = new EmailHelper();
    
    echo "<h2>Email Configuration:</h2>";
    echo "<ul>";
    echo "<li><strong>SMTP Host:</strong> " . htmlspecialchars(env('SMTP_HOST', 'smtp.gmail.com')) . "</li>";
    echo "<li><strong>SMTP Port:</strong> " . htmlspecialchars(env('SMTP_PORT', '587')) . "</li>";
    $smtpUser = env('SMTP_USERNAME', 'Not set');
    echo "<li><strong>SMTP Username:</strong> " . htmlspecialchars($smtpUser) . ($smtpUser === 'Not set' ? ' ⚠️' : ' ✅') . "</li>";
    $fromEmail = env('SMTP_FROM_EMAIL', 'Not set');
    echo "<li><strong>From Email:</strong> " . htmlspecialchars($fromEmail) . ($fromEmail === 'Not set' ? ' ⚠️' : ' ✅') . "</li>";
    $smtpPass = env('SMTP_PASSWORD', 'Not set');
    echo "<li><strong>SMTP Password:</strong> " . (empty($smtpPass) || $smtpPass === 'Not set' ? 'Not set ⚠️' : 'Set ✅') . "</li>";
    echo "</ul>";
    
    echo "<h2>PHPMailer Status:</h2>";
    $phpmailerPath = __DIR__ . '/vendor/autoload.php';
    if (file_exists($phpmailerPath)) {
        echo "<p style='color: green;'>✅ Composer autoload found</p>";
        require_once $phpmailerPath;
    } else {
        echo "<p style='color: orange;'>⚠️ Composer autoload not found</p>";
    }
    
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "<p style='color: green;'>✅ PHPMailer class is available</p>";
    } else {
        echo "<p style='color: red;'>❌ PHPMailer class not found</p>";
        echo "<p><strong>To install PHPMailer:</strong></p>";
        echo "<pre>cd " . __DIR__ . "<br>composer require phpmailer/phpmailer</pre>";
    }
    
    echo "<h2>Sending Test Email...</h2>";
    
    $result = $emailHelper->sendOTP($testEmail, 'Test User', $testOTP);
    
    if ($result) {
        echo "<p style='color: green; font-size: 18px;'><strong>✅ Email sent successfully!</strong></p>";
        echo "<p>Please check your inbox at <strong>$testEmail</strong></p>";
        echo "<p>Also check spam/junk folder if not received.</p>";
    } else {
        echo "<p style='color: red; font-size: 18px;'><strong>❌ Email sending failed!</strong></p>";
        echo "<p>Check the error logs for details:</p>";
        echo "<pre>";
        echo "Error log location: " . ini_get('error_log') . "\n";
        echo "Or check: /Applications/XAMPP/xamppfiles/logs/php_error_log";
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<p><a href='?email=$testEmail'>Test Again</a> | <a href='register'>Go to Register</a></p>";
echo "<p><strong>Usage:</strong> test_email.php?email=your-email@example.com</p>";

