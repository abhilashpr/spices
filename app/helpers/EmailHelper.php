<?php
/**
 * Email Helper Class
 * Handles email sending via SMTP
 */

require_once __DIR__ . '/../config/env.php';

class EmailHelper
{
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $fromEmail;
    private $fromName;

    public function __construct()
    {
        $this->smtpHost = env('SMTP_HOST', 'smtp.gmail.com');
        $this->smtpPort = env('SMTP_PORT', '587');
        $this->smtpUsername = env('SMTP_USERNAME', 'axclue@gmail.com');
        $this->smtpPassword = env('SMTP_PASSWORD', 'aeokohsjwvkgbhon');
        $this->fromEmail = env('SMTP_FROM_EMAIL', 'axclue@gmail.com');
        $this->fromName = env('SMTP_FROM_NAME', 'Wynvalley');
    }

    /**
     * Send OTP email
     */
    public function sendOTP(string $toEmail, string $toName, string $otp): bool
    {
        $subject = 'Verify Your Email - OTP Code';
        $htmlBody = $this->getOTPTemplate($toName, $otp);
        
        return $this->sendEmail($toEmail, $toName, $subject, $htmlBody);
    }

    /**
     * Send email via SMTP
     */
    private function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        try {
            // Check if PHPMailer is available
            $phpmailerPath = __DIR__ . '/../../vendor/autoload.php';
            if (file_exists($phpmailerPath)) {
                require_once $phpmailerPath;
            }
            
            // Try to load PHPMailer classes directly if composer autoload doesn't work
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                $phpmailerSrcPath = __DIR__ . '/../../vendor/phpmailer/phpmailer/src';
                if (file_exists($phpmailerSrcPath . '/PHPMailer.php')) {
                    require_once $phpmailerSrcPath . '/PHPMailer.php';
                    require_once $phpmailerSrcPath . '/SMTP.php';
                    require_once $phpmailerSrcPath . '/Exception.php';
                }
            }
            
            // Use PHPMailer if available, otherwise use mail() function
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                error_log("Using PHPMailer to send email to: $toEmail");
                return $this->sendViaPHPMailer($toEmail, $toName, $subject, $htmlBody);
            } else {
                error_log("PHPMailer not found - falling back to mail() function");
                error_log("WARNING: Gmail SMTP requires PHPMailer! Install it with: composer require phpmailer/phpmailer");
                error_log("WARNING: mail() function will NOT work with Gmail SMTP! Emails will not be delivered!");
                
                // Check if we have SMTP credentials configured
                if (!empty($this->smtpUsername) && !empty($this->smtpPassword)) {
                    error_log("ERROR: SMTP credentials configured but PHPMailer not installed. Email will NOT be sent!");
                    return false; // Don't use mail() if SMTP is configured
                }
                
                return $this->sendViaMail($toEmail, $toName, $subject, $htmlBody);
            }
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        } catch (Error $e) {
            error_log("Email sending error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Send email using PHP mail() function with SMTP headers
     * Note: This won't work with Gmail SMTP - requires PHPMailer
     */
    private function sendViaMail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        // PHP mail() function doesn't support SMTP authentication
        // This will only work with local mail server, not Gmail
        error_log("Attempting to send email via mail() function - this may not work with Gmail SMTP");
        error_log("Recipient: $toEmail, From: {$this->fromEmail}");
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];

        $result = @mail($toEmail, $subject, $htmlBody, implode("\r\n", $headers));
        
        if (!$result) {
            error_log("mail() function returned false - email may not be sent");
            error_log("Last error: " . (error_get_last()['message'] ?? 'Unknown error'));
        }
        
        return $result;
    }

    /**
     * Send email using PHPMailer (if installed)
     */
    private function sendViaPHPMailer(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Enable verbose debug output (level 2 = client and server)
            // $mail->SMTPDebug = 2; // Uncomment for debugging
            // $mail->Debugoutput = function($str, $level) {
            //     error_log("PHPMailer Debug: $str");
            // };

            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int)$this->smtpPort;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $result = $mail->send();
            
            if ($result) {
                error_log("Email sent successfully to: $toEmail via PHPMailer");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            error_log("Exception: " . $e->getMessage());
            error_log("SMTP Settings: Host={$this->smtpHost}, Port={$this->smtpPort}, User={$this->smtpUsername}");
            return false;
        }
    }

    /**
     * Get OTP email HTML template
     */
    private function getOTPTemplate(string $name, string $otp): string
    {
        $appName = env('APP_NAME', 'Wynvalley');
        $appUrl = env('APP_URL', 'http://localhost/online-sp');
        
        return "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Email Verification</title>
</head>
<body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f7fa;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f5f7fa; padding: 40px 20px;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.1);'>
                    <!-- Header -->
                    <tr>
                        <td style='background: linear-gradient(135deg, #32c68d, #28a870); padding: 40px 30px; text-align: center;'>
                            <h1 style='margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;'>$appName</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style='padding: 40px 30px;'>
                            <h2 style='margin: 0 0 20px 0; color: #09342a; font-size: 24px;'>Verify Your Email Address</h2>
                            <p style='margin: 0 0 20px 0; color: #666666; font-size: 16px; line-height: 1.6;'>
                                Hello <strong>$name</strong>,
                            </p>
                            <p style='margin: 0 0 30px 0; color: #666666; font-size: 16px; line-height: 1.6;'>
                                Thank you for registering with $appName! Please use the following OTP code to verify your email address:
                            </p>
                            
                            <!-- OTP Box -->
                            <table width='100%' cellpadding='0' cellspacing='0'>
                                <tr>
                                    <td align='center' style='padding: 20px 0;'>
                                        <div style='background: linear-gradient(135deg, #32c68d, #28a870); color: #ffffff; font-size: 36px; font-weight: bold; letter-spacing: 8px; padding: 20px 40px; border-radius: 12px; display: inline-block; box-shadow: 0 4px 16px rgba(50, 198, 141, 0.3);'>
                                            $otp
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style='margin: 30px 0 20px 0; color: #666666; font-size: 14px; line-height: 1.6;'>
                                This OTP will expire in <strong>10 minutes</strong>. If you didn't request this code, please ignore this email.
                            </p>
                            
                            <p style='margin: 0; color: #999999; font-size: 12px; line-height: 1.6;'>
                                For security reasons, never share this OTP with anyone.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;'>
                            <p style='margin: 0 0 10px 0; color: #666666; font-size: 14px;'>
                                © " . date('Y') . " $appName. All rights reserved.
                            </p>
                            <p style='margin: 0; color: #999999; font-size: 12px;'>
                                This is an automated email, please do not reply.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>";
    }
}

