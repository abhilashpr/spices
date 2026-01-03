<?php
/**
 * Script to add more reviews to products
 * Visit this page in your browser to add 6 additional reviews to amber-garam product
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Reviews</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #32c68d; }
        .success { color: #32c68d; padding: 10px; background: #e8f8f3; border-radius: 4px; margin: 10px 0; }
        .info { color: #666; padding: 10px; background: #f0f0f0; border-radius: 4px; margin: 10px 0; }
        .error { color: #d32f2f; padding: 10px; background: #ffebee; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Product Reviews</h1>
        <?php
        require_once __DIR__ . '/includes/db.php';

        try {
            $pdo = get_db_connection();

            // Get product ID for amber-garam
            $stmt = $pdo->prepare("SELECT id, name FROM products WHERE slug = 'amber-garam' LIMIT 1");
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo '<div class="error">Error: Product \'amber-garam\' not found. Please make sure products are inserted first.</div>';
                exit;
            }

            $productId = $product['id'];
            $productName = $product['name'];

            // Check if reviews already exist
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM product_reviews WHERE product_id = :product_id");
            $stmt->execute([':product_id' => $productId]);
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            echo "<div class='info'><strong>Product:</strong> $productName (ID: $productId)<br>";
            echo "<strong>Current reviews:</strong> $count</div>";

            // Reviews to add
            $reviews = [
                ['Priya Sharma', 5, 'Authentic flavor profile', 'This garam masala reminds me of my grandmother\'s kitchen. The saffron notes are subtle but present, and it works beautifully in both vegetarian and non-vegetarian dishes. Highly recommend!'],
                ['Rajesh Kumar', 4, 'Great for everyday cooking', 'I use this in my daily dal and sabzi preparations. The quality is excellent and the packaging keeps the spices fresh. Good value for money.'],
                ['Sarah Chen', 5, 'Restaurant quality at home', 'As a home cook, I\'ve been trying to recreate restaurant-style biryanis. This spice blend made all the difference! The mace and long pepper add such complexity.'],
                ['Michael Thompson', 4, 'Well balanced blend', 'The heat level is perfect - not too mild, not too spicy. I\'ve used it in curries, marinades, and even sprinkled on roasted vegetables. Very versatile.'],
                ['Anita Desai', 5, 'Premium quality spices', 'You can tell these are high-quality, freshly ground spices. The aroma when you open the container is incredible. Worth every penny.'],
                ['David Wilson', 4, 'Excellent for special occasions', 'I bought this for a dinner party and everyone asked where I got the spices from. The flavor profile is sophisticated and the presentation is beautiful.'],
            ];

            // Insert reviews
            $insertStmt = $pdo->prepare("
                INSERT INTO product_reviews (product_id, reviewer_name, rating, headline, review_text)
                VALUES (:product_id, :reviewer_name, :rating, :headline, :review_text)
            ");

            $added = 0;
            $skipped = 0;
            $messages = [];

            foreach ($reviews as $review) {
                // Check if review already exists
                $checkStmt = $pdo->prepare("
                    SELECT COUNT(*) as count FROM product_reviews 
                    WHERE product_id = :product_id AND reviewer_name = :reviewer_name AND headline = :headline
                ");
                $checkStmt->execute([
                    ':product_id' => $productId,
                    ':reviewer_name' => $review[0],
                    ':headline' => $review[2]
                ]);
                
                if ($checkStmt->fetch(PDO::FETCH_ASSOC)['count'] == 0) {
                    $insertStmt->execute([
                        ':product_id' => $productId,
                        ':reviewer_name' => $review[0],
                        ':rating' => $review[1],
                        ':headline' => $review[2],
                        ':review_text' => $review[3]
                    ]);
                    $added++;
                    $messages[] = "✓ Added review by: <strong>{$review[0]}</strong> - {$review[2]}";
                } else {
                    $skipped++;
                    $messages[] = "⊘ Review by <strong>{$review[0]}</strong> already exists, skipping...";
                }
            }

            echo '<div class="success">';
            echo "<h2>Results:</h2>";
            echo "<p><strong>Added:</strong> $added new reviews<br>";
            echo "<strong>Skipped:</strong> $skipped existing reviews<br>";
            echo "<strong>Total reviews now:</strong> " . ($count + $added) . "</p>";
            echo "</div>";

            if (!empty($messages)) {
                echo "<h3>Details:</h3><ul>";
                foreach ($messages as $msg) {
                    echo "<li>$msg</li>";
                }
                echo "</ul>";
            }

            echo '<div class="info">';
            echo '<p><strong>Next steps:</strong></p>';
            echo '<ol>';
            echo '<li>Visit your product page: <a href="/online-sp/product.php?slug=amber-garam" target="_blank">View Product</a></li>';
            echo '<li>You should now see ' . ($count + $added) . ' reviews with scroll functionality</li>';
            echo '</ol>';
            echo '</div>';

        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>

