<?php
/**
 * Auth Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';
require_once __DIR__ . '/../config/env.php';

class AuthController extends Controller
{
    private $userModel;
    private $emailHelper;

    public function __construct()
    {
        parent::__construct();
        try {
            $this->userModel = new UserModel();
        } catch (Exception $e) {
            error_log("AuthController UserModel error: " . $e->getMessage());
            throw $e; // Re-throw to show error
        }
        
        try {
            $this->emailHelper = new EmailHelper();
        } catch (Exception $e) {
            error_log("AuthController EmailHelper error: " . $e->getMessage());
            // EmailHelper can fail without breaking the page
            $this->emailHelper = null;
        }
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            $user = $this->userModel->findByEmail($email);

            if ($user && $user['status'] === 'active' && $user['email_verified'] == 1) {
                if ($user['login_type'] === 'web' && password_verify($password, $user['password'])) {
                    $this->loginUser($user, $remember);
                    $this->redirect(url(''));
                    return;
                } elseif ($user['login_type'] === 'gmail') {
                    // Gmail login handled separately
                    $this->setFlash('Please use Google login', 'error');
                } else {
                    $this->setFlash('Invalid email or password', 'error');
                }
            } else {
                if ($user && $user['status'] === 'pending') {
                    $this->setFlash('Please verify your email first. Check your inbox for OTP.', 'error');
                } else {
                    $this->setFlash('Invalid email or password', 'error');
                }
            }
        }

        $this->render('auth/login', [
            'layout' => 'main',
            'pageTitle' => 'Login | Wynvalley'
        ]);
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get POST data
            $firstname = trim($_POST['firstname'] ?? '');
            $lastname = trim($_POST['lastname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Log registration attempt
            error_log("Registration attempt - Email: $email, Firstname: $firstname, Lastname: $lastname");

            // Validation
            if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
                $error = 'All fields are required';
                error_log("Registration validation failed: $error");
                $this->setFlash($error, 'error');
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email address';
                error_log("Registration validation failed: $error");
                $this->setFlash($error, 'error');
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters';
                error_log("Registration validation failed: $error");
                $this->setFlash($error, 'error');
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match';
                error_log("Registration validation failed: $error");
                $this->setFlash($error, 'error');
            } else {
                // Check if user already exists
                error_log("Checking if user exists: $email");
                $existingUser = $this->userModel->findByEmail($email);
                
                if ($existingUser) {
                    error_log("User already exists - Email verified: " . ($existingUser['email_verified'] ?? 0));
                    if ($existingUser['email_verified'] == 1) {
                        $error = 'Email already registered. Please login.';
                        error_log("Registration failed: $error");
                        $this->setFlash($error, 'error');
                    } else {
                        // Resend OTP
                        error_log("Resending OTP to existing unverified user");
                        if ($this->sendOTP($existingUser['id'], $email, $firstname . ' ' . $lastname)) {
                            $success = 'OTP has been resent to your email. Please check your inbox.';
                            error_log("Registration success: $success");
                            $this->setFlash($success, 'info');
                            $this->redirect(url('verify-otp?email=' . urlencode($email)));
                            return;
                        } else {
                            $error = 'Failed to resend OTP. Please try again.';
                            error_log("Registration failed: $error");
                            $this->setFlash($error, 'error');
                        }
                    }
                } else {
                    // Create new user
                    error_log("Creating new user: $email");
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $otp = $this->generateOTP();
                    $otpExpiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                    
                    error_log("Generated OTP: $otp (expires at: $otpExpiresAt)");

                    $userId = $this->userModel->create([
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'email' => $email,
                        'password' => $hashedPassword,
                        'status' => 'pending',
                        'login_type' => 'web',
                        'otp' => $otp,
                        'otp_expires_at' => $otpExpiresAt
                    ]);

                    if ($userId) {
                        error_log("User created successfully with ID: $userId");
                        // Send OTP email
                        try {
                            error_log("Attempting to send OTP email to: $email");
                            error_log("EmailHelper available: " . ($this->emailHelper ? 'Yes' : 'No'));
                            
                            if ($this->emailHelper) {
                                $emailResult = $this->emailHelper->sendOTP($email, $firstname . ' ' . $lastname, $otp);
                                error_log("Email send result: " . ($emailResult ? 'Success' : 'Failed'));
                                
                                if ($emailResult) {
                                    $success = 'Registration successful! Please check your email for OTP verification.';
                                    error_log("Registration success: $success");
                                    $this->setFlash($success, 'info');
                                    $this->redirect(url('verify-otp?email=' . urlencode($email)));
                                    return;
                                } else {
                                    $error = 'Registration successful but failed to send OTP. Please contact support or check your email settings. OTP: ' . $otp;
                                    error_log("Registration partial success - User created but email failed: $error");
                                    $this->setFlash($error, 'error');
                                }
                            } else {
                                $error = 'Registration successful but email service not available. Please contact support. OTP: ' . $otp;
                                error_log("Registration partial success - EmailHelper not available: $error");
                                $this->setFlash($error, 'error');
                            }
                        } catch (Exception $e) {
                            error_log("Exception sending OTP email: " . $e->getMessage());
                            error_log("Exception trace: " . $e->getTraceAsString());
                            $error = 'Registration successful but failed to send OTP. Please contact support. OTP: ' . $otp;
                            $this->setFlash($error, 'error');
                        } catch (Error $e) {
                            error_log("Fatal error sending OTP email: " . $e->getMessage());
                            error_log("Error trace: " . $e->getTraceAsString());
                            $error = 'Registration successful but failed to send OTP. Please contact support. OTP: ' . $otp;
                            $this->setFlash($error, 'error');
                        }
                    } else {
                        $error = 'Registration failed. Please try again.';
                        error_log("User creation failed: $error");
                        $this->setFlash($error, 'error');
                    }
                }
            }
        }

        $this->render('auth/register', [
            'layout' => 'main',
            'pageTitle' => 'Register | Wynvalley'
        ]);
    }

    public function verifyOTP(): void
    {
        $email = $_GET['email'] ?? $_POST['email'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otp = trim($_POST['otp'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            // Remove any non-numeric characters from OTP
            $otp = preg_replace('/[^0-9]/', '', $otp);

            error_log("OTP Verification request - Email: $email, OTP: $otp (length: " . strlen($otp) . ")");

            if (empty($otp) || empty($email)) {
                $error = 'OTP and email are required';
                error_log("OTP Verification failed: $error");
                $this->setFlash($error, 'error');
            } elseif (strlen($otp) !== 6) {
                $error = 'OTP must be 6 digits (got ' . strlen($otp) . ' digits)';
                error_log("OTP Verification failed: $error - OTP: '$otp'");
                $this->setFlash($error, 'error');
            } else {
                // Log before verification
                error_log("Attempting OTP verification for email: $email");
                
                if ($this->userModel->verifyOTP($email, $otp)) {
                    $user = $this->userModel->findByEmail($email);
                    if ($user) {
                        // Auto login
                        error_log("OTP Verification successful - Logging in user: " . $user['id']);
                        $this->loginUser($user, false);
                        $this->setFlash('Email verified successfully! Welcome to Wynvalley.', 'info');
                        $this->redirect(url(''));
                        return;
                    } else {
                        error_log("OTP Verification successful but user not found after verification");
                        $this->setFlash('Verification successful but user not found. Please try logging in.', 'error');
                    }
                } else {
                    // Get user to check OTP details for debugging
                    $user = $this->userModel->findByEmail($email);
                    if ($user) {
                        $storedOTP = $user['otp'] ?? '';
                        $expiresAt = $user['otp_expires_at'] ?? '';
                        error_log("OTP Verification failed - Stored OTP: '$storedOTP', Input OTP: '$otp', Expires: $expiresAt");
                        $this->setFlash('Invalid or expired OTP. Please check your email or request a new OTP.', 'error');
                    } else {
                        error_log("OTP Verification failed - User not found: $email");
                        $this->setFlash('User not found. Please register again.', 'error');
                    }
                }
            }
        }

        if (empty($email)) {
            $this->redirect(url('register'));
            return;
        }

        $this->render('auth/verify-otp', [
            'layout' => 'main',
            'pageTitle' => 'Verify Email | Wynvalley',
            'email' => $email
        ]);
    }

    public function resendOTP(): void
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Email is required']);
                exit;
            }
            
            $user = $this->userModel->findByEmail($email);
            if ($user && $user['status'] === 'pending') {
                $otp = $this->generateOTP();
                $otpExpiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                
                if ($this->userModel->updateOTP($user['id'], $otp, $otpExpiresAt)) {
                    if ($this->emailHelper && $this->emailHelper->sendOTP($email, $user['firstname'] . ' ' . $user['lastname'], $otp)) {
                        echo json_encode(['success' => true, 'message' => 'OTP has been resent to your email.']);
                        exit;
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
                        exit;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to resend OTP. Please try again.']);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found or already verified.']);
                exit;
            }
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    public function logout(): void
    {
        // Clear all session data
        $_SESSION = [];
        
        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        $this->setFlash('You have been logged out successfully', 'info');
        $this->redirect(url(''));
    }

    /**
     * Generate 6-digit OTP
     */
    private function generateOTP(): string
    {
        return str_pad((string)rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to user
     */
    private function sendOTP(int $userId, string $email, string $name): bool
    {
        $otp = $this->generateOTP();
        $otpExpiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        if ($this->userModel->updateOTP($userId, $otp, $otpExpiresAt)) {
            if ($this->emailHelper) {
                return $this->emailHelper->sendOTP($email, $name, $otp);
            }
        }
    
        
        return false;
    }

    /**
     * Login user and set session
     */
    private function loginUser(array $user, bool $remember = false): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
        $_SESSION['user_logged_in'] = true;

        // Migrate session cart to database cart if session cart exists
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            require_once __DIR__ . '/../models/CartModel.php';
            $cartModel = new CartModel();
            $sessionCart = $_SESSION['cart']; // Store session cart before clearing
            
            // Migrate session cart items to database cart
            // This will add new items or update quantities if items already exist
            $cartModel->migrateSessionCart($user['id'], $sessionCart);
            
            // Clear session cart after successful migration
            $_SESSION['cart'] = [];
            unset($_SESSION['cart']);
        }

        // Generate auth token if remember me
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $tokenExpiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
            $this->userModel->setAuthToken($user['id'], $token, $tokenExpiresAt);
            setcookie('auth_token', $token, time() + (30 * 24 * 60 * 60), '/');
        }
    }
}
