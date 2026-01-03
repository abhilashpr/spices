<?php
require_once __DIR__ . '/db.php';

function get_hero_slides(): array
{
    $sql = 'SELECT * FROM hero_slides ORDER BY display_order';
    return fetch_all($sql);
}

function get_best_sellers(int $limit = 3): array
{
    $sql = 'SELECT * FROM products WHERE best_seller = 1 ORDER BY best_seller_order LIMIT :limit';
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_collection_products(int $limit = 3): array
{
    $sql = 'SELECT * FROM products WHERE collection = 1 ORDER BY collection_order LIMIT :limit';
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_all_products(): array
{
    $sql = 'SELECT * FROM products ORDER BY name';
    return fetch_all($sql);
}

function get_product_by_slug(string $slug): ?array
{
    $sql = 'SELECT * FROM products WHERE slug = :slug LIMIT 1';
    return fetch_one($sql, [':slug' => $slug]);
}

function get_products_by_slugs(array $slugs): array
{
    if (empty($slugs)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($slugs), '?'));
    $sql = "SELECT * FROM products WHERE slug IN ($placeholders)";
    $pdo = get_db_connection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($slugs));
    $products = $stmt->fetchAll();

    $indexed = [];
    foreach ($products as $product) {
        $indexed[$product['slug']] = $product;
    }

    return $indexed;
}

function format_price(float $amount, string $currency = '$'): string
{
    return $currency . number_format($amount, 0);
}

function split_notes(?string $notes): array
{
    if ($notes === null || trim($notes) === '') {
        return [];
    }

    $lines = preg_split('/\r?\n/', trim($notes));
    return array_values(array_filter(array_map('trim', $lines)));
}

function get_product_media(int $productId): array
{
    $sql = 'SELECT media_type, media_url, caption FROM product_media WHERE product_id = :product_id ORDER BY display_order, id';
    return fetch_all($sql, [':product_id' => $productId]);
}

function get_related_products(int $productId, int $limit = 4): array
{
    $sql = 'SELECT p.*
            FROM related_products rp
            JOIN products p ON p.id = rp.related_product_id
            WHERE rp.product_id = :product_id
            ORDER BY p.name
            LIMIT :limit';

    $pdo = get_db_connection();
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_product_reviews(int $productId, int $limit = 6): array
{
    $sql = 'SELECT reviewer_name, rating, headline, review_text, created_at
            FROM product_reviews
            WHERE product_id = :product_id
            ORDER BY created_at DESC
            LIMIT :limit';

    $pdo = get_db_connection();
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}
