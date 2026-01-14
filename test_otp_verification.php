<?php
/**
 * Test OTP Verification
 * Debug OTP verification issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/models/UserModel.php';

echo "<!DOCTYPE html><html><head><title>OTP Verification Debug</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
echo "h1{color:#333;}pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow:auto;}";
echo ".success{color:green;}.error{color:red;}.info{color:blue;}</style></head><body>";

echo "<h1>OTP Verification Debug</h1>";

$email = $_GET['email'] ?? $_POST['email'] ?? '';
$otp = $_GET['otp'] ?? $_POST['otp'] ?? '';

echo "<div class='box'>";
echo "<h2>Input Parameters:</h2>";
echo "<form method='POST' action=''>";
echo "<table cellpadding='10'>";
echo "<tr><td>Email:</td><td><input type='email' name='email' value='" . htmlspecialchars($email) . "' required /></td></tr>";
echo "<tr><td>OTP:</td><td><input type='text' name='otp' value='" . htmlspecialchars($otp) . "' required maxlength='6' /></td></tr>";
echo "<tr><td colspan='2'><input type='submit' value='Test Verification' style='padding:10px 20px;'/></td></tr>";
echo "</table>";
echo "</form>";
echo "</div>";

if (!empty($email) && !empty($otp)) {
    echo "<div class='box'>";
    echo "<h2>Verification Process:</h2>";
    
    try {
        $userModel = new UserModel();
        
        // Check user exists
        echo "<h3>1. Checking if user exists...</h3>";
        $user = $userModel->findByEmail($email);
        
        if (!$user) {
            echo "<p class='error'>❌ User not found with email: $email</p>";
        } else {
            echo "<p class='success'>✅ User found</p>";
            echo "<pre>";
            echo "ID: " . ($user['id'] ?? 'N/A') . "\n";
            echo "Email: " . ($user['email'] ?? 'N/A') . "\n";
            echo "Status: " . ($user['status'] ?? 'N/A') . "\n";
            echo "Email Verified: " . ($user['email_verified'] ?? 'N/A') . "\n";
            echo "</pre>";
            
            // Check OTP in database
            echo "<h3>2. Checking OTP in database...</h3>";
            echo "<pre>";
            echo "Stored OTP: " . ($user['otp'] ?? 'NULL') . "\n";
            echo "Input OTP: " . htmlspecialchars($otp) . "\n";
            echo "OTP Expires At: " . ($user['otp_expires_at'] ?? 'NULL') . "\n";
            echo "</pre>";
            
            // Check OTP match
            echo "<h3>3. Checking OTP match...</h3>";
            $storedOTP = $user['otp'] ?? '';
            $inputOTP = trim($otp);
            
            echo "<pre>";
            echo "Stored OTP (raw): '" . var_export($storedOTP, true) . "'\n";
            echo "Stored OTP (length): " . strlen($storedOTP) . "\n";
            echo "Input OTP (raw): '" . var_export($inputOTP, true) . "'\n";
            echo "Input OTP (length): " . strlen($inputOTP) . "\n";
            echo "Match (==): " . ($storedOTP == $inputOTP ? 'Yes' : 'No') . "\n";
            echo "Match (===): " . ($storedOTP === $inputOTP ? 'Yes' : 'No') . "\n";
            echo "Match (trimmed ==): " . (trim($storedOTP) == $inputOTP ? 'Yes' : 'No') . "\n";
            echo "</pre>";
            
            // Check expiration
            echo "<h3>4. Checking expiration...</h3>";
            $expiresAt = $user['otp_expires_at'] ?? null;
            if ($expiresAt) {
                $expiresTimestamp = strtotime($expiresAt);
                $nowTimestamp = time();
                $currentDateTime = date('Y-m-d H:i:s');
                
                echo "<pre>";
                echo "Current Time: $currentDateTime\n";
                echo "Expires At: $expiresAt\n";
                echo "Current Timestamp: $nowTimestamp\n";
                echo "Expires Timestamp: $expiresTimestamp\n";
                echo "Time Remaining: " . ($expiresTimestamp - $nowTimestamp) . " seconds\n";
                
                if ($nowTimestamp < $expiresTimestamp) {
                    echo "Status: <span class='success'>✅ OTP is still valid</span>\n";
                } else {
                    echo "Status: <span class='error'>❌ OTP has expired</span>\n";
                }
                echo "</pre>";
            } else {
                echo "<p class='error'>❌ No expiration time set</p>";
            }
            
            // Test verification query
            echo "<h3>5. Testing verification query...</h3>";
            try {
                require_once __DIR__ . '/app/config/database.php';
                $pdo = get_db_connection();
                
                $sql = "SELECT * FROM users WHERE email = :email AND otp = :otp AND otp_expires_at > NOW() LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':email' => $email,
                    ':otp' => $inputOTP
                ]);
                
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result) {
                    echo "<p class='success'>✅ Verification query found matching record</p>";
                    echo "<pre>";
                    print_r($result);
                    echo "</pre>";
                } else {
                    echo "<p class='error'>❌ Verification query did not find matching record</p>";
                    
                    // Try without expiration check
                    echo "<h4>5a. Testing without expiration check...</h4>";
                    $sql2 = "SELECT * FROM users WHERE email = :email AND otp = :otp LIMIT 1";
                    $stmt2 = $pdo->prepare($sql2);
                    $stmt2->execute([
                        ':email' => $email,
                        ':otp' => $inputOTP
                    ]);
                    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result2) {
                        echo "<p class='info'>⚠️ Found match without expiration check - OTP may be expired</p>";
                    } else {
                        echo "<p class='error'>❌ No match found even without expiration check</p>";
                        
                        // Try with stored OTP from database
                        echo "<h4>5b. Testing with stored OTP from database...</h4>";
                        $sql3 = "SELECT * FROM users WHERE email = :email AND otp = :otp LIMIT 1";
                        $stmt3 = $pdo->prepare($sql3);
                        $stmt3->execute([
                            ':email' => $email,
                            ':otp' => $storedOTP
                        ]);
                        $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
                        
                        if ($result3) {
                            echo "<p class='info'>⚠️ Found match with stored OTP - input OTP may have whitespace or encoding issue</p>";
                            echo "<pre>";
                            echo "Stored OTP bytes: " . bin2hex($storedOTP) . "\n";
                            echo "Input OTP bytes: " . bin2hex($inputOTP) . "\n";
                            echo "</pre>";
                        }
                    }
                }
            } catch (Exception $e) {
                echo "<p class='error'>❌ Query Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            
            // Try actual verification
            echo "<h3>6. Testing verifyOTP method...</h3>";
            $verificationResult = $userModel->verifyOTP($email, $inputOTP);
            
            if ($verificationResult) {
                echo "<p class='success'>✅ verifyOTP returned TRUE - Verification successful!</p>";
            } else {
                echo "<p class='error'>❌ verifyOTP returned FALSE - Verification failed</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    
    echo "</div>";
}

echo "<p><a href='verify-otp?email=" . urlencode($email) . "'>Go to Verify OTP Page</a> | <a href='register'>Go to Register</a></p>";
echo "</body></html>";

