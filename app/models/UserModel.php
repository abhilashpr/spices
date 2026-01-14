<?php
/**
 * User Model
 */

require_once __DIR__ . '/../core/Model.php';

class UserModel extends Model
{
    protected $table = 'users';

    /**
     * Create a new user
     */
    public function create(array $data): ?int
    {
        try {
            // Clean and normalize OTP before storing
            $otp = $data['otp'] ?? null;
            if ($otp) {
                $otp = trim($otp);
                $otp = preg_replace('/[^0-9]/', '', $otp); // Remove any non-numeric characters
                error_log("Creating user with OTP: '$otp' (length: " . strlen($otp) . ")");
            }
            
            $sql = "INSERT INTO users (firstname, lastname, email, password, status, login_type, gmail_id, otp, otp_expires_at) 
                    VALUES (:firstname, :lastname, :email, :password, :status, :login_type, :gmail_id, :otp, :otp_expires_at)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':firstname' => $data['firstname'],
                ':lastname' => $data['lastname'],
                ':email' => $data['email'],
                ':password' => $data['password'] ?? null,
                ':status' => $data['status'] ?? 'pending',
                ':login_type' => $data['login_type'] ?? 'web',
                ':gmail_id' => $data['gmail_id'] ?? null,
                ':otp' => $otp,
                ':otp_expires_at' => $data['otp_expires_at'] ?? null
            ]);
            
            $userId = (int)$this->pdo->lastInsertId();
            error_log("User created successfully with ID: $userId, Email: " . $data['email']);
            
            return $userId;
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error finding user by email: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find user by Gmail ID
     */
    public function findByGmailId(string $gmailId): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE gmail_id = :gmail_id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':gmail_id' => $gmailId]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error finding user by Gmail ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify OTP and activate user
     */
    public function verifyOTP(string $email, string $otp): bool
    {
        try {
            // Trim and normalize OTP
            $email = trim($email);
            $otp = trim($otp);
            $otp = preg_replace('/[^0-9]/', '', $otp); // Remove any non-numeric characters
            
            // Log verification attempt
            error_log("OTP Verification attempt - Email: $email, OTP: $otp");
            
            // First check if user exists and get stored OTP
            $userSql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $userStmt = $this->pdo->prepare($userSql);
            $userStmt->execute([':email' => $email]);
            $user = $userStmt->fetch();
            
            if (!$user) {
                error_log("OTP Verification failed - User not found: $email");
                return false;
            }
            
            $storedOTP = trim($user['otp'] ?? '');
            $storedOTP = preg_replace('/[^0-9]/', '', $storedOTP); // Remove any non-numeric characters
            $expiresAt = $user['otp_expires_at'] ?? null;
            
            error_log("OTP Verification - Stored OTP: '$storedOTP', Input OTP: '$otp', Expires: $expiresAt");
            
            // Check if OTP matches (exact match)
            if ($storedOTP !== $otp) {
                error_log("OTP Verification failed - OTP mismatch. Stored: '$storedOTP' (len:" . strlen($storedOTP) . "), Input: '$otp' (len:" . strlen($otp) . ")");
                return false;
            }
            
            // Check expiration using database time to avoid timezone issues
            $expireCheckSql = "SELECT 
                (UNIX_TIMESTAMP(otp_expires_at) - UNIX_TIMESTAMP(NOW())) as time_diff_seconds
                FROM users WHERE id = :id";
            $expireStmt = $this->pdo->prepare($expireCheckSql);
            $expireStmt->execute([':id' => $user['id']]);
            $expireResult = $expireStmt->fetch();
            
            if ($expireResult && isset($expireResult['time_diff_seconds'])) {
                $timeDiff = (int)$expireResult['time_diff_seconds'];
                if ($timeDiff <= 0) {
                    error_log("OTP Verification failed - OTP expired. Time diff: $timeDiff seconds");
                    return false;
                }
                error_log("OTP Verification - OTP is valid. Time remaining: $timeDiff seconds");
            }
            
            // Verify with query (includes expiration check) - Using database time
            $sql = "SELECT * FROM users WHERE email = :email AND otp = :otp AND otp_expires_at > NOW() LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':otp' => $otp
            ]);
            
            $verifiedUser = $stmt->fetch();
            if ($verifiedUser) {
                // Update user status
                $updateSql = "UPDATE users SET 
                              status = 'active', 
                              email_verified = 1, 
                              otp = NULL, 
                              otp_expires_at = NULL 
                              WHERE id = :id";
                $updateStmt = $this->pdo->prepare($updateSql);
                $updateStmt->execute([':id' => $verifiedUser['id']]);
                error_log("OTP Verification successful - User activated: " . $verifiedUser['id']);
                return true;
            }
            
            error_log("OTP Verification failed - Query returned no results");
            return false;
        } catch (PDOException $e) {
            error_log("Error verifying OTP: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Update OTP
     */
    public function updateOTP(int $userId, string $otp, string $expiresAt): bool
    {
        try {
            $sql = "UPDATE users SET otp = :otp, otp_expires_at = :otp_expires_at WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $userId,
                ':otp' => $otp,
                ':otp_expires_at' => $expiresAt
            ]);
        } catch (PDOException $e) {
            error_log("Error updating OTP: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Set auth token
     */
    public function setAuthToken(int $userId, string $token, string $expiresAt): bool
    {
        try {
            $sql = "UPDATE users SET auth_token = :auth_token, token_expires_at = :token_expires_at WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $userId,
                ':auth_token' => $token,
                ':token_expires_at' => $expiresAt
            ]);
        } catch (PDOException $e) {
            error_log("Error setting auth token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find user by auth token
     */
    public function findByToken(string $token): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE auth_token = :token AND token_expires_at > NOW() LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':token' => $token]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error finding user by token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error finding user by ID: " . $e->getMessage());
            return null;
        }
    }
}

