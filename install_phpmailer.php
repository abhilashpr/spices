<?php
/**
 * Install PHPMailer Script
 * This will help you install PHPMailer if composer is available
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHPMailer Installation Helper</h1>";

// Check if composer is available
$composerCommand = 'composer';
$hasComposer = false;

// Try to find composer
$possiblePaths = [
    '/usr/local/bin/composer',
    '/usr/bin/composer',
    'composer',
];

foreach ($possiblePaths as $path) {
    $output = [];
    $return_var = 0;
    exec("which $path 2>&1", $output, $return_var);
    if ($return_var === 0) {
        $composerCommand = trim($output[0]);
        $hasComposer = true;
        break;
    }
}

if (!$hasComposer) {
    echo "<p style='color: red;'><strong>❌ Composer not found!</strong></p>";
    echo "<h2>Manual Installation:</h2>";
    echo "<ol>";
    echo "<li>Download Composer from: <a href='https://getcomposer.org/download/' target='_blank'>https://getcomposer.org/download/</a></li>";
    echo "<li>Install Composer</li>";
    echo "<li>Run in terminal: <code>cd " . __DIR__ . " && composer require phpmailer/phpmailer</code></li>";
    echo "</ol>";
} else {
    echo "<p style='color: green;'>✅ Composer found at: <strong>$composerCommand</strong></p>";
    
    $projectDir = __DIR__;
    $installCommand = "cd $projectDir && $composerCommand require phpmailer/phpmailer 2>&1";
    
    echo "<h2>Installing PHPMailer...</h2>";
    echo "<pre>";
    passthru($installCommand, $return_var);
    echo "</pre>";
    
    if ($return_var === 0) {
        echo "<p style='color: green; font-size: 18px;'><strong>✅ PHPMailer installed successfully!</strong></p>";
        echo "<p><a href='test_email.php'>Test Email Now</a></p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Installation failed!</strong></p>";
        echo "<p>Try running this command manually in terminal:</p>";
        echo "<pre>cd $projectDir<br>$composerCommand require phpmailer/phpmailer</pre>";
    }
}

echo "<hr>";
echo "<p><a href='test_email.php'>Test Email</a> | <a href='register'>Go to Register</a></p>";

