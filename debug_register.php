<?php
/**
 * Debug Registration Script
 * Shows all POST data and registration process
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Debug Registration</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
echo "h1{color:#333;}pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow:auto;}";
echo ".success{color:green;}.error{color:red;}.info{color:blue;}</style></head><body>";

echo "<h1>Registration Debug</h1>";

echo "<div class='box'>";
echo "<h2>POST Data:</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";
echo "</div>";

echo "<div class='box'>";
echo "<h2>Request Method:</h2>";
echo "<p>" . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "</div>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/app/config/config.php';
    require_once __DIR__ . '/app/bootstrap.php';
    require_once __DIR__ . '/app/helpers/helpers.php';
    require_once __DIR__ . '/app/models/UserModel.php';
    require_once __DIR__ . '/app/helpers/EmailHelper.php';
    
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    echo "<div class='box'>";
    echo "<h2>Processed Data:</h2>";
    echo "<ul>";
    echo "<li><strong>Firstname:</strong> " . htmlspecialchars($firstname) . "</li>";
    echo "<li><strong>Lastname:</strong> " . htmlspecialchars($lastname) . "</li>";
    echo "<li><strong>Email:</strong> " . htmlspecialchars($email) . "</li>";
    echo "<li><strong>Password:</strong> " . (empty($password) ? '(empty)' : '(set, length: ' . strlen($password) . ')') . "</li>";
    echo "<li><strong>Confirm Password:</strong> " . (empty($confirmPassword) ? '(empty)' : '(set, length: ' . strlen($confirmPassword) . ')') . "</li>";
    echo "</ul>";
    echo "</div>";
    
    // Validation
    echo "<div class='box'>";
    echo "<h2>Validation:</h2>";
    
    $errors = [];
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        $errors[] = "All fields are required";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    if (!empty($password) && $password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($errors)) {
        echo "<p class='success'>✅ Validation passed</p>";
        
        // Check database
        echo "<h3>Database Check:</h3>";
        try {
            $userModel = new UserModel();
            echo "<p class='info'>✅ UserModel created successfully</p>";
            
            $existingUser = $userModel->findByEmail($email);
            if ($existingUser) {
                echo "<p class='info'>⚠️ User already exists</p>";
                echo "<pre>";
                print_r($existingUser);
                echo "</pre>";
            } else {
                echo "<p class='success'>✅ Email not registered yet</p>";
                
                // Test email sending
                echo "<h3>Email Sending Test:</h3>";
                try {
                    $emailHelper = new EmailHelper();
                    echo "<p class='info'>✅ EmailHelper created successfully</p>";
                    
                    $otp = str_pad((string)rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                    echo "<p><strong>Test OTP:</strong> $otp</p>";
                    
                    echo "<p>Attempting to send email...</p>";
                    $result = $emailHelper->sendOTP($email, $firstname . ' ' . $lastname, $otp);
                    
                    if ($result) {
                        echo "<p class='success'>✅ Email sent successfully!</p>";
                    } else {
                        echo "<p class='error'>❌ Email sending failed!</p>";
                        echo "<p>Check PHP error logs for details.</p>";
                    }
                } catch (Exception $e) {
                    echo "<p class='error'>❌ EmailHelper Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                } catch (Error $e) {
                    echo "<p class='error'>❌ EmailHelper Fatal Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                }
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
    } else {
        echo "<p class='error'>❌ Validation failed:</p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
    
    echo "</div>";
}

echo "<div class='box'>";
echo "<h2>Test Registration Form:</h2>";
echo "<form method='POST' action=''>";
echo "<table cellpadding='10'>";
echo "<tr><td>Firstname:</td><td><input type='text' name='firstname' required /></td></tr>";
echo "<tr><td>Lastname:</td><td><input type='text' name='lastname' required /></td></tr>";
echo "<tr><td>Email:</td><td><input type='email' name='email' required /></td></tr>";
echo "<tr><td>Password:</td><td><input type='password' name='password' required /></td></tr>";
echo "<tr><td>Confirm Password:</td><td><input type='password' name='confirm_password' required /></td></tr>";
echo "<tr><td colspan='2'><input type='submit' value='Test Registration' style='padding:10px 20px;'/></td></tr>";
echo "</table>";
echo "</form>";
echo "</div>";

echo "<div class='box'>";
echo "<h2>Error Logs (Last 50 lines):</h2>";
echo "<pre>";
$errorLog = ini_get('error_log');
if (file_exists($errorLog)) {
    $lines = file($errorLog);
    $lastLines = array_slice($lines, -50);
    echo htmlspecialchars(implode('', $lastLines));
} else {
    echo "Error log file not found: $errorLog";
}
echo "</pre>";
echo "</div>";

echo "<p><a href='register'>Go to Register Page</a> | <a href='test_email.php'>Test Email</a></p>";
echo "</body></html>";

