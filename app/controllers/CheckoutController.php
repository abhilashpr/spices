<?php
/**
 * Checkout Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/AddressModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/helpers.php';

class CheckoutController extends Controller
{
    private $cartModel;
    private $productModel;
    private $addressModel;
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->addressModel = new AddressModel();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        // Handle action requests
        $action = $_GET['action'] ?? '';
        if ($action === 'guest-checkout') {
            $this->guestCheckout();
            return;
        }
        if ($action === 'verify-guest-otp') {
            $this->verifyGuestOTP();
            return;
        }

        // Get cart items
        $cart = [];
        $user = null;
        $addresses = [];
        $defaultAddress = null;

        if (is_logged_in()) {
            // Get cart from database for logged-in user
            $user = get_logged_in_user();
            $userId = $user['id'] ?? null;
            
            if ($userId) {
                $cart = $this->cartModel->getUserCart($userId);
                
                // Get user details
                $userDetails = $this->userModel->findById($userId);
                if ($userDetails) {
                    $user = array_merge($user, [
                        'firstname' => $userDetails['firstname'] ?? '',
                        'lastname' => $userDetails['lastname'] ?? '',
                        'email' => $userDetails['email'] ?? '',
                        'phone' => $userDetails['phone'] ?? ''
                    ]);
                }
                
                // Get user addresses
                $addresses = $this->addressModel->getUserAddresses($userId);
                $defaultAddress = $this->addressModel->getDefaultAddress($userId);
            }
        } else {
            // Get cart from session for guest users
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

            // Enhance session cart with product details
            foreach ($_SESSION['cart'] as $key => $item) {
                $product = $this->productModel->getById((int)$item['product_id']);
                if ($product) {
                    $sku = $this->productModel->getProductSKUs((int)$item['product_id']);
                    $selectedSku = null;
                    foreach ($sku as $s) {
                        if ($s['id'] == $item['sku_id']) {
                            $selectedSku = $s;
                            break;
                        }
                    }
                    if ($selectedSku) {
                        $cart[] = [
                            'cart_item_id' => $key,
                            'product_id' => $product['id'],
                            'product_name' => $product['name'],
                            'product_slug' => $product['slug'],
                            'main_image' => $product['main_image'] ?? null,
                            'sku_id' => $selectedSku['id'],
                            'sku_value' => $selectedSku['value'],
                            'unit_symbol' => $selectedSku['unit_symbol'] ?? '',
                            'price' => $selectedSku['price'],
                            'offer_price' => $selectedSku['offer_price'] ?? null,
                            'quantity' => $item['quantity'],
                            'unit' => $item['unit'] ?? null
                        ];
                    }
                }
            }
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $price = $item['offer_price'] ?? $item['price'] ?? 0;
            $subtotal += $price * ($item['quantity'] ?? 1);
        }
        $shipping = 0; // Free shipping
        $tax = round($subtotal * 0.18, 2); // 18% GST
        $total = $subtotal + $shipping + $tax;

        $this->render('checkout/index', [
            'layout' => 'main',
            'pageTitle' => 'Checkout | Wynvalley',
            'cart' => $cart,
            'user' => $user,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'isLoggedIn' => is_logged_in()
        ]);
    }

    /**
     * Handle guest checkout - send OTP
     */
    public function guestCheckout(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $email = trim($input['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Valid email is required']);
            exit;
        }

        // Check if user exists
        $existingUser = $this->userModel->findByEmail($email);
        
        if ($existingUser && $existingUser['email_verified'] == 1) {
            echo json_encode([
                'success' => false, 
                'message' => 'Email already registered. Please login to continue.',
                'login_required' => true
            ]);
            exit;
        }

        // Generate and send OTP
        require_once __DIR__ . '/../helpers/EmailHelper.php';
        $emailHelper = new EmailHelper();
        
        $otp = $this->generateOTP();
        $otpExpiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        if ($existingUser) {
            // Resend OTP
            $this->userModel->updateOTP($existingUser['id'], $otp, $otpExpiresAt);
            $emailHelper->sendOTP($email, $existingUser['firstname'] . ' ' . $existingUser['lastname'], $otp);
        } else {
            // Create new user with OTP
            $userId = $this->userModel->create([
                'firstname' => 'Guest',
                'lastname' => 'User',
                'email' => $email,
                'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), // Random password
                'status' => 'pending',
                'login_type' => 'web',
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt
            ]);

            if ($userId) {
                $emailHelper->sendOTP($email, 'Guest User', $otp);
            }
        }

        // Store email in session for OTP verification
        $_SESSION['guest_checkout_email'] = $email;

        echo json_encode([
            'success' => true,
            'message' => 'OTP sent to your email. Please verify to continue.',
            'email' => $email
        ]);
    }

    /**
     * Verify OTP for guest checkout
     */
    public function verifyGuestOTP(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $email = trim($input['email'] ?? '');
        $otp = preg_replace('/[^0-9]/', '', $input['otp'] ?? '');

        if (empty($email) || empty($otp) || strlen($otp) !== 6) {
            echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
            exit;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        if ($this->userModel->verifyOTP($email, $otp)) {
            // Login user and migrate cart
            $this->loginUser($user, false);

            // Migrate session cart to database
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $sessionCart = $_SESSION['cart'];
                $this->cartModel->migrateSessionCart($user['id'], $sessionCart);
                $_SESSION['cart'] = [];
                unset($_SESSION['cart']);
            }

            unset($_SESSION['guest_checkout_email']);

            echo json_encode([
                'success' => true,
                'message' => 'OTP verified successfully!',
                'redirect' => url('checkout')
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP']);
        }
    }

    /**
     * Add new address for logged-in user
     */
    public function addAddress(): void
    {
        // Set JSON header first, before any output
        header('Content-Type: application/json');
        
        // Prevent any output buffering issues
        if (ob_get_level()) {
            ob_clean();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!is_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Please login to save address']);
            exit;
        }

        $user = get_logged_in_user();
        $userId = $user['id'] ?? null;

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $required = ['contact_name', 'contact_email', 'contact_phone', 'address_line1', 'city', 'state', 'country', 'post_code'];
        foreach ($required as $field) {
            if (empty($input[$field] ?? '')) {
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                exit;
            }
        }

        // Validate email format
        if (!filter_var($input['contact_email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
            exit;
        }

        $addressData = [
            'user_id' => $userId,
            'contact_name' => trim($input['contact_name'] ?? ''),
            'contact_email' => trim($input['contact_email'] ?? ''),
            'contact_phone' => trim($input['contact_phone'] ?? ''),
            'address_line1' => trim($input['address_line1'] ?? ''),
            'address_line2' => trim($input['address_line2'] ?? ''),
            'city' => trim($input['city'] ?? ''),
            'state' => trim($input['state'] ?? ''),
            'country' => trim($input['country'] ?? 'India'),
            'post_code' => trim($input['post_code'] ?? ''),
            'landmark' => trim($input['landmark'] ?? ''),
            'note' => trim($input['address_note'] ?? trim($input['note'] ?? '')),
            'is_default' => isset($input['set_as_default']) && $input['set_as_default'] ? 1 : 0
        ];

        try {
            $addressId = $this->addressModel->create($addressData);

            if ($addressId > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Address saved successfully!',
                    'address_id' => $addressId
                ]);
            } else {
                error_log("CheckoutController::addAddress - AddressModel::create returned 0");
                echo json_encode(['success' => false, 'message' => 'Failed to save address. Please check that all required fields are filled and try again.']);
            }
        } catch (PDOException $e) {
            error_log("CheckoutController::addAddress PDO error: " . $e->getMessage());
            error_log("CheckoutController::addAddress SQL error info: " . print_r($e->errorInfo ?? [], true));
            
            // Check for specific database errors
            $errorMessage = 'Failed to save address. ';
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                $errorMessage .= 'Database columns missing. Please run: database/add_contact_fields_to_addresses.sql';
            } else {
                $errorMessage .= 'Error: ' . $e->getMessage();
            }
            
            echo json_encode(['success' => false, 'message' => $errorMessage]);
        } catch (Exception $e) {
            error_log("CheckoutController::addAddress error: " . $e->getMessage());
            error_log("CheckoutController::addAddress stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update existing address
     */
    public function updateAddress(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!is_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Please login to update address']);
            exit;
        }

        $user = get_logged_in_user();
        $userId = $user['id'] ?? null;

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $addressId = (int)($input['address_id'] ?? 0);

        if ($addressId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid address ID']);
            exit;
        }

        // Validate required fields
        $required = ['contact_name', 'contact_email', 'contact_phone', 'address_line1', 'city', 'state', 'country', 'post_code'];
        foreach ($required as $field) {
            if (empty($input[$field] ?? '')) {
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                exit;
            }
        }

        // Validate email format
        if (!filter_var($input['contact_email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
            exit;
        }

        $addressData = [
            'contact_name' => trim($input['contact_name'] ?? ''),
            'contact_email' => trim($input['contact_email'] ?? ''),
            'contact_phone' => trim($input['contact_phone'] ?? ''),
            'address_line1' => trim($input['address_line1'] ?? ''),
            'address_line2' => trim($input['address_line2'] ?? ''),
            'city' => trim($input['city'] ?? ''),
            'state' => trim($input['state'] ?? ''),
            'country' => trim($input['country'] ?? 'India'),
            'post_code' => trim($input['post_code'] ?? ''),
            'landmark' => trim($input['landmark'] ?? ''),
            'note' => trim($input['address_note'] ?? trim($input['note'] ?? '')),
            'is_default' => isset($input['set_as_default']) && $input['set_as_default'] ? 1 : 0
        ];

        $success = $this->addressModel->update($addressId, $userId, $addressData);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Address updated successfully!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update address. Please try again.']);
        }
    }

    /**
     * Delete address
     */
    public function deleteAddress(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!is_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Please login to delete address']);
            exit;
        }

        $user = get_logged_in_user();
        $userId = $user['id'] ?? null;

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $addressId = (int)($input['address_id'] ?? 0);

        if ($addressId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid address ID']);
            exit;
        }

        $success = $this->addressModel->delete($addressId, $userId);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Address deleted successfully!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete address. Please try again.']);
        }
    }

    /**
     * Set address as default
     */
    public function setDefaultAddress(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!is_logged_in()) {
            echo json_encode(['success' => false, 'message' => 'Please login to set default address']);
            exit;
        }

        $user = get_logged_in_user();
        $userId = $user['id'] ?? null;

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $addressId = (int)($input['address_id'] ?? 0);

        if ($addressId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid address ID']);
            exit;
        }

        $success = $this->addressModel->setDefault($addressId, $userId);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Default address updated successfully!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to set default address. Please try again.']);
        }
    }

    /**
     * Save guest address to session
     */
    public function saveGuestAddress(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $required = ['contact_name', 'contact_email', 'contact_phone', 'address_line1', 'city', 'state', 'country', 'post_code'];
        foreach ($required as $field) {
            if (empty($input[$field] ?? '')) {
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                exit;
            }
        }

        // Validate email format
        if (!filter_var($input['contact_email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
            exit;
        }

        // Store address in session for guest users
        $_SESSION['guest_shipping_address'] = [
            'contact_name' => trim($input['contact_name'] ?? ''),
            'contact_email' => trim($input['contact_email'] ?? ''),
            'contact_phone' => trim($input['contact_phone'] ?? ''),
            'address_line1' => trim($input['address_line1'] ?? ''),
            'address_line2' => trim($input['address_line2'] ?? ''),
            'city' => trim($input['city'] ?? ''),
            'state' => trim($input['state'] ?? ''),
            'country' => trim($input['country'] ?? 'India'),
            'post_code' => trim($input['post_code'] ?? ''),
            'landmark' => trim($input['landmark'] ?? ''),
            'note' => trim($input['address_note'] ?? trim($input['note'] ?? ''))
        ];

        echo json_encode([
            'success' => true,
            'message' => 'Address saved successfully!'
        ]);
    }

    /**
     * Generate 6-digit OTP
     */
    private function generateOTP(): string
    {
        return str_pad((string)rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Login user and set session (similar to AuthController)
     */
    private function loginUser(array $user, bool $remember = false): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
        $_SESSION['user_logged_in'] = true;

        // Migrate session cart to database cart if session cart exists
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            $sessionCart = $_SESSION['cart'];
            $this->cartModel->migrateSessionCart($user['id'], $sessionCart);
            $_SESSION['cart'] = [];
            unset($_SESSION['cart']);
        }
    }
}

