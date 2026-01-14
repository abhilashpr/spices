<?php
/**
 * Address Model
 */

require_once __DIR__ . '/../config/database.php';

class AddressModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = get_db_connection();
    }

    /**
     * Get all addresses for a user
     */
    public function getUserAddresses(int $userId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM addresses 
                WHERE user_id = :user_id 
                ORDER BY is_default DESC, created_at DESC
            ");
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("AddressModel::getUserAddresses error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get default address for a user
     */
    public function getDefaultAddress(int $userId): ?array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM addresses 
                WHERE user_id = :user_id AND is_default = 1 
                LIMIT 1
            ");
            $stmt->execute([':user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (PDOException $e) {
            error_log("AddressModel::getDefaultAddress error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get address by ID
     */
    public function getById(int $addressId, int $userId): ?array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM addresses 
                WHERE id = :id AND user_id = :user_id 
                LIMIT 1
            ");
            $stmt->execute([
                ':id' => $addressId,
                ':user_id' => $userId
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result === false ? null : $result;
        } catch (PDOException $e) {
            error_log("AddressModel::getById error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new address
     */
    public function create(array $data): int
    {
        try {
            // If this is set as default, unset other defaults
            if (isset($data['is_default']) && $data['is_default'] == 1) {
                $this->unsetDefaultAddress($data['user_id']);
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO addresses (
                    user_id, contact_name, contact_email, contact_phone, 
                    address_line1, address_line2, city, state, country, 
                    post_code, landmark, note, is_default
                ) VALUES (
                    :user_id, :contact_name, :contact_email, :contact_phone,
                    :address_line1, :address_line2, :city, :state, :country,
                    :post_code, :landmark, :note, :is_default
                )
            ");
            $stmt->execute([
                ':user_id' => $data['user_id'],
                ':contact_name' => $data['contact_name'] ?? null,
                ':contact_email' => $data['contact_email'] ?? null,
                ':contact_phone' => $data['contact_phone'] ?? null,
                ':address_line1' => $data['address_line1'],
                ':address_line2' => $data['address_line2'] ?? null,
                ':city' => $data['city'],
                ':state' => $data['state'],
                ':country' => $data['country'] ?? 'India',
                ':post_code' => $data['post_code'],
                ':landmark' => $data['landmark'] ?? null,
                ':note' => $data['note'] ?? null,
                ':is_default' => $data['is_default'] ?? 0
            ]);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $errorInfo = $this->pdo->errorInfo();
            error_log("AddressModel::create error: " . $e->getMessage());
            error_log("AddressModel::create SQL error code: " . ($errorInfo[0] ?? 'N/A'));
            error_log("AddressModel::create SQL error message: " . ($errorInfo[2] ?? 'N/A'));
            error_log("AddressModel::create SQL: " . $stmt->queryString ?? 'N/A');
            
            // Check if it's a column not found error
            if (strpos($e->getMessage(), 'Unknown column') !== false || 
                (isset($errorInfo[2]) && strpos($errorInfo[2], 'Unknown column') !== false)) {
                error_log("AddressModel::create - Column missing. Please run: database/add_contact_fields_to_addresses.sql");
            }
            throw $e; // Re-throw to let controller handle it
        }
    }

    /**
     * Update an address
     */
    public function update(int $addressId, int $userId, array $data): bool
    {
        try {
            // If this is set as default, unset other defaults
            if (isset($data['is_default']) && $data['is_default'] == 1) {
                $this->unsetDefaultAddress($userId);
            }

            $stmt = $this->pdo->prepare("
                UPDATE addresses SET
                    contact_name = :contact_name,
                    contact_email = :contact_email,
                    contact_phone = :contact_phone,
                    address_line1 = :address_line1,
                    address_line2 = :address_line2,
                    city = :city,
                    state = :state,
                    country = :country,
                    post_code = :post_code,
                    landmark = :landmark,
                    note = :note,
                    is_default = :is_default,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':id' => $addressId,
                ':user_id' => $userId,
                ':contact_name' => $data['contact_name'] ?? null,
                ':contact_email' => $data['contact_email'] ?? null,
                ':contact_phone' => $data['contact_phone'] ?? null,
                ':address_line1' => $data['address_line1'],
                ':address_line2' => $data['address_line2'] ?? null,
                ':city' => $data['city'],
                ':state' => $data['state'],
                ':country' => $data['country'] ?? 'India',
                ':post_code' => $data['post_code'],
                ':landmark' => $data['landmark'] ?? null,
                ':note' => $data['note'] ?? null,
                ':is_default' => $data['is_default'] ?? 0
            ]);
        } catch (PDOException $e) {
            error_log("AddressModel::update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an address
     */
    public function delete(int $addressId, int $userId): bool
    {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM addresses 
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':id' => $addressId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("AddressModel::delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Set an address as default
     */
    public function setDefault(int $addressId, int $userId): bool
    {
        try {
            // Unset all other defaults
            $this->unsetDefaultAddress($userId);

            // Set this one as default
            $stmt = $this->pdo->prepare("
                UPDATE addresses 
                SET is_default = 1, updated_at = CURRENT_TIMESTAMP
                WHERE id = :id AND user_id = :user_id
            ");
            return $stmt->execute([
                ':id' => $addressId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("AddressModel::setDefault error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unset default address for a user
     */
    private function unsetDefaultAddress(int $userId): void
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE addresses 
                SET is_default = 0 
                WHERE user_id = :user_id AND is_default = 1
            ");
            $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("AddressModel::unsetDefaultAddress error: " . $e->getMessage());
        }
    }
}

