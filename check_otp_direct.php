<?php
/**
 * Direct OTP Check - Debug script
 * This will directly check the OTP from database
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';

echo "<!DOCTYPE html><html><head><title>Direct OTP Check</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
echo "h1{color:#333;}pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow:auto;}";
echo ".success{color:green;font-weight:bold;}.error{color:red;font-weight:bold;}.info{color:blue;}</style></head><body>";

echo "<h1>Direct OTP Check from Database</h1>";

$email = $_GET['email'] ?? '';

if (empty($email)) {
    echo "<div class='box'>";
    echo "<form method='GET' action=''>";
    echo "<p>Enter Email: <input type='email' name='email' required /></p>";
    echo "<p><input type='submit' value='Check OTP' /></p>";
    echo "</form>";
    echo "</div>";
} else {
    try {
        $pdo = get_db_connection();
        
        echo "<div class='box'>";
        echo "<h2>User Data from Database:</h2>";
        
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo "<p class='error'>❌ User not found</p>";
        } else {
            echo "<pre>";
            print_r($user);
            echo "</pre>";
            
            echo "<h3>OTP Details:</h3>";
            echo "<ul>";
            echo "<li><strong>Stored OTP:</strong> <code style='font-size:20px;padding:5px;background:#f0f0f0;'>" . htmlspecialchars($user['otp'] ?? 'NULL') . "</code></li>";
            echo "<li><strong>OTP Length:</strong> " . strlen($user['otp'] ?? '') . "</li>";
            echo "<li><strong>OTP Bytes (hex):</strong> " . bin2hex($user['otp'] ?? '') . "</li>";
            echo "<li><strong>OTP Expires At:</strong> " . ($user['otp_expires_at'] ?? 'NULL') . "</li>";
            echo "<li><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</li>";
            echo "<li><strong>Current Timestamp:</strong> " . time() . "</li>";
            
            if ($user['otp_expires_at']) {
                $expiresTimestamp = strtotime($user['otp_expires_at']);
                $currentTimestamp = time();
                $difference = $expiresTimestamp - $currentTimestamp;
                
                echo "<li><strong>Expires Timestamp:</strong> $expiresTimestamp</li>";
                echo "<li><strong>Time Difference:</strong> $difference seconds (" . round($difference / 60, 2) . " minutes)</li>";
                
                if ($difference > 0) {
                    echo "<li class='success'>✅ OTP is still valid (expires in " . round($difference / 60, 2) . " minutes)</li>";
                } else {
                    echo "<li class='error'>❌ OTP has expired (" . abs(round($difference / 60, 2)) . " minutes ago)</li>";
                }
            }
            echo "</ul>";
            
            echo "<h3>Test Verification Query:</h3>";
            
            // Test 1: Direct SQL query with current time
            echo "<h4>Test 1: Query with NOW() function</h4>";
            $testOTP = $user['otp'] ?? '';
            if ($testOTP) {
                $sql1 = "SELECT * FROM users WHERE email = :email AND otp = :otp AND otp_expires_at > NOW() LIMIT 1";
                $stmt1 = $pdo->prepare($sql1);
                $stmt1->execute([
                    ':email' => $email,
                    ':otp' => $testOTP
                ]);
                $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
                
                if ($result1) {
                    echo "<p class='success'>✅ Query with NOW() found match</p>";
                } else {
                    echo "<p class='error'>❌ Query with NOW() did NOT find match</p>";
                }
            }
            
            // Test 2: Query without expiration check
            echo "<h4>Test 2: Query without expiration check</h4>";
            $sql2 = "SELECT * FROM users WHERE email = :email AND otp = :otp LIMIT 1";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([
                ':email' => $email,
                ':otp' => $testOTP
            ]);
            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($result2) {
                echo "<p class='success'>✅ Query without expiration found match</p>";
                echo "<p class='info'>⚠️ This means OTP matches but might be expired</p>";
            } else {
                echo "<p class='error'>❌ Query without expiration did NOT find match</p>";
                echo "<p class='error'>This means OTP or email doesn't match!</p>";
            }
            
            // Test 3: Check what NOW() returns
            echo "<h4>Test 3: What does NOW() return?</h4>";
            $stmt3 = $pdo->query("SELECT NOW() as current_time, UNIX_TIMESTAMP(NOW()) as current_timestamp");
            $timeResult = $stmt3->fetch(PDO::FETCH_ASSOC);
            echo "<pre>";
            print_r($timeResult);
            echo "</pre>";
            
            // Test 4: Manual expiration check
            echo "<h4>Test 4: Manual expiration check</h4>";
            if ($user['otp_expires_at']) {
                $sql4 = "SELECT 
                    otp_expires_at,
                    NOW() as current_time,
                    UNIX_TIMESTAMP(otp_expires_at) as expires_timestamp,
                    UNIX_TIMESTAMP(NOW()) as current_timestamp,
                    (UNIX_TIMESTAMP(otp_expires_at) - UNIX_TIMESTAMP(NOW())) as time_diff_seconds
                    FROM users WHERE email = :email";
                $stmt4 = $pdo->prepare($sql4);
                $stmt4->execute([':email' => $email]);
                $expireResult = $stmt4->fetch(PDO::FETCH_ASSOC);
                echo "<pre>";
                print_r($expireResult);
                echo "</pre>";
                
                if ($expireResult && $expireResult['time_diff_seconds'] > 0) {
                    echo "<p class='success'>✅ OTP is NOT expired (according to database)</p>";
                } else {
                    echo "<p class='error'>❌ OTP IS expired (according to database)</p>";
                }
            }
            
            echo "</div>";
            
            // Show form to test with input OTP
            echo "<div class='box'>";
            echo "<h2>Test with Your Input:</h2>";
            echo "<form method='POST' action=''>";
            echo "<input type='hidden' name='email' value='" . htmlspecialchars($email) . "'>";
            echo "<p>Enter OTP from database: <input type='text' name='otp' maxlength='6' pattern='[0-9]{6}' style='font-size:20px;padding:10px;' /></p>";
            echo "<p><input type='submit' value='Test This OTP' /></p>";
            echo "</form>";
            echo "</div>";
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
                $inputOTP = trim($_POST['otp']);
                $storedOTP = trim($user['otp'] ?? '');
                
                echo "<div class='box'>";
                echo "<h2>OTP Comparison Results:</h2>";
                echo "<pre>";
                echo "Stored OTP: '$storedOTP' (length: " . strlen($storedOTP) . ")\n";
                echo "Input OTP:  '$inputOTP' (length: " . strlen($inputOTP) . ")\n";
                echo "\n";
                echo "Exact Match (===): " . ($storedOTP === $inputOTP ? 'YES ✅' : 'NO ❌') . "\n";
                echo "Loose Match (==):  " . ($storedOTP == $inputOTP ? 'YES ✅' : 'NO ❌') . "\n";
                echo "\n";
                echo "Stored bytes (hex): " . bin2hex($storedOTP) . "\n";
                echo "Input bytes (hex):  " . bin2hex($inputOTP) . "\n";
                echo "</pre>";
                
                // Try the verification query
                $verifySql = "SELECT * FROM users WHERE email = :email AND otp = :otp AND otp_expires_at > NOW() LIMIT 1";
                $verifyStmt = $pdo->prepare($verifySql);
                $verifyStmt->execute([
                    ':email' => $email,
                    ':otp' => $inputOTP
                ]);
                $verifyResult = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($verifyResult) {
                    echo "<p class='success'>✅ Verification Query: SUCCESS - This OTP would work!</p>";
                } else {
                    echo "<p class='error'>❌ Verification Query: FAILED - This OTP would NOT work</p>";
                    
                    // Check if it's an expiration issue
                    $verifySql2 = "SELECT * FROM users WHERE email = :email AND otp = :otp LIMIT 1";
                    $verifyStmt2 = $pdo->prepare($verifySql2);
                    $verifyStmt2->execute([
                        ':email' => $email,
                        ':otp' => $inputOTP
                    ]);
                    $verifyResult2 = $verifyStmt2->fetch(PDO::FETCH_ASSOC);
                    
                    if ($verifyResult2) {
                        echo "<p class='info'>⚠️ OTP matches but appears to be expired!</p>";
                    } else {
                        echo "<p class='error'>❌ OTP does not match at all!</p>";
                    }
                }
                echo "</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='box'>";
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    }
}

echo "<p><a href='?email=" . urlencode($email) . "'>Refresh</a> | <a href='verify-otp?email=" . urlencode($email) . "'>Go to Verify Page</a></p>";
echo "</body></html>";

