<?php
/**
 * Slider Model
 */

require_once __DIR__ . '/../core/Model.php';

class SliderModel extends Model
{
    public function getHeroSlides(): array
    {
        try {
            // Check if is_active column exists
            $checkColumn = $this->pdo->query("SHOW COLUMNS FROM hero_slides LIKE 'is_active'");
            $hasActiveColumn = $checkColumn->rowCount() > 0;
            
            if ($hasActiveColumn) {
                $sql = "SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY display_order";
            } else {
                $sql = "SELECT * FROM hero_slides ORDER BY display_order";
            }
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting hero slides: " . $e->getMessage());
            return [];
        }
    }

    public function getActiveSliders(): array
    {
        $sql = "SELECT * FROM sliders WHERE is_active = 1 ORDER BY display_order";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
}

