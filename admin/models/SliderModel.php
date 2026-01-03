<?php
/**
 * Slider Model
 * Handles all database operations for sliders
 */

require_once __DIR__ . '/../core/Model.php';

class SliderModel extends Model
{
    protected $table = 'sliders';

    /**
     * Get all sliders ordered by display order
     * 
     * @return array
     */
    public function getAll(): array
    {
        return $this->findAll('display_order ASC, created_at DESC');
    }

    /**
     * Get paginated sliders
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        return $this->findPaginated($page, $perPage, 'display_order ASC, created_at DESC');
    }

    /**
     * Get only active sliders ordered by display order
     * 
     * @return array
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 
                ORDER BY display_order ASC, created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get slider by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->findById($id);
    }

    /**
     * Create a new slider
     * 
     * @param array $data
     * @return int The ID of the newly created slider
     * @throws Exception
     */
    public function createSlider(array $data): int
    {
        // Validate required fields
        if (empty($data['title']) || empty($data['image_url'])) {
            throw new Exception('Title and image URL are required');
        }

        // If slider type is 'link', validate link fields
        if (($data['slider_type'] ?? 'static') === 'link') {
            if (empty($data['link_url'])) {
                throw new Exception('Link URL is required for link-type sliders');
            }
        }

        // Ensure is_active is boolean
        if (isset($data['is_active'])) {
            $data['is_active'] = (int)(bool)$data['is_active'];
        }

        return $this->create($data);
    }

    /**
     * Update an existing slider
     * 
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function updateSlider(int $id, array $data): bool
    {
        // Validate required fields if provided
        if (isset($data['title']) && empty($data['title'])) {
            throw new Exception('Title cannot be empty');
        }

        if (isset($data['image_url']) && empty($data['image_url'])) {
            throw new Exception('Image URL cannot be empty');
        }

        // If slider type is 'link', validate link fields
        if (($data['slider_type'] ?? null) === 'link') {
            if (empty($data['link_url'])) {
                throw new Exception('Link URL is required for link-type sliders');
            }
        }

        // Ensure is_active is boolean
        if (isset($data['is_active'])) {
            $data['is_active'] = (int)(bool)$data['is_active'];
        }

        return $this->update($id, $data);
    }

    /**
     * Delete a slider
     * 
     * @param int $id
     * @return bool
     */
    public function deleteSlider(int $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Toggle slider active status
     * 
     * @param int $id
     * @return bool
     */
    public function toggleActive(int $id): bool
    {
        $slider = $this->getById($id);
        if (!$slider) {
            return false;
        }

        $newStatus = $slider['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }
}

