<?php
/**
 * Category Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class CategoryController extends Controller
{
    private $categoryModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->getAll();
        $categoriesWithSubcategories = $this->categoryModel->getCategoriesWithSubcategories();
        
        // Get filter from query parameter (can be single value or array for multi-select)
        // PHP automatically converts filter[]=X&filter[]=Y to an array in $_GET['filter']
        $filter = $_GET['filter'] ?? $_GET['subcategory'] ?? null;
        $subcategoryIds = [];
        $selectedSubcategories = [];
        $selectedCategory = null;
        
        // Handle multiple filter values (array) or single value
        $filterValues = [];
        if ($filter) {
            if (is_array($filter)) {
                // Multiple filters: filter[]=3&filter[]=5 becomes an array
                $filterValues = $filter;
            } else {
                // Single filter: filter=3 becomes a string
                $filterValues = [$filter];
            }
        }
        
        // Process each filter value
        foreach ($filterValues as $filterValue) {
            if (is_numeric($filterValue)) {
                $filterId = (int)$filterValue;
                
                // Try to find in categories with subcategories
                $found = false;
                foreach ($categoriesWithSubcategories as $cat) {
                    foreach ($cat['subcategories'] as $subcat) {
                        if ($subcat['id'] == $filterId) {
                            if (!in_array($filterId, $subcategoryIds)) {
                                $subcategoryIds[] = $filterId;
                                $selectedSubcategories[] = $subcat;
                            }
                            if (!$selectedCategory) {
                                $selectedCategory = $cat;
                            }
                            $found = true;
                            break 2;
                        }
                    }
                    
                    // Check if filter matches category
                    if (!$found && $cat['id'] == $filterId) {
                        $selectedCategory = $cat;
                        $found = true;
                        break;
                    }
                }
                
                // If not found in categories list, still use it as subcategory ID
                if (!$found && !in_array($filterId, $subcategoryIds)) {
                    $subcategoryIds[] = $filterId;
                }
            } else {
                // Filter is a slug - find by slug
                foreach ($categoriesWithSubcategories as $cat) {
                    foreach ($cat['subcategories'] as $subcat) {
                        if (!empty($subcat['slug']) && $subcat['slug'] === $filterValue) {
                            if (!in_array($subcat['id'], $subcategoryIds)) {
                                $subcategoryIds[] = $subcat['id'];
                                $selectedSubcategories[] = $subcat;
                            }
                            if (!$selectedCategory) {
                                $selectedCategory = $cat;
                            }
                            break 2;
                        }
                    }
                    
                    // Check if filter matches category slug
                    if (!empty($cat['slug']) && $cat['slug'] === $filterValue) {
                        $selectedCategory = $cat;
                        break;
                    }
                }
            }
        }
        
        // Get products based on filter
        if (!empty($subcategoryIds)) {
            // Filter by multiple subcategories (group_id)
            $products = $this->productModel->getBySubcategories($subcategoryIds);
        } elseif ($selectedCategory) {
            // Filter by category (category_id)
            $products = $this->productModel->getByCategory($selectedCategory['id']);
        } else {
            // Show all active products
            $products = $this->productModel->getAll();
        }
        
        // Get price info and ratings for each product
        $productIds = array_column($products, 'id');
        $ratings = $this->productModel->getProductsRatings($productIds);
        
        foreach ($products as &$product) {
            $priceInfo = $this->productModel->getPriceInfo($product['id']);
            $product['min_price'] = $priceInfo['price'];
            $product['min_offer_price'] = $priceInfo['offer_price'];
            
            // Add rating info
            if (isset($ratings[$product['id']])) {
                $product['rating'] = $ratings[$product['id']]['average'];
                $product['review_count'] = $ratings[$product['id']]['count'];
            } else {
                $product['rating'] = 0;
                $product['review_count'] = 0;
            }
        }
        unset($product);

        // Generate page title based on selected filters
        $pageTitle = 'Shop by Category | Wynvalley';
        if (!empty($selectedSubcategories)) {
            if (count($selectedSubcategories) === 1) {
                $pageTitle = $selectedSubcategories[0]['name'] . ' | Wynvalley';
            } else {
                $pageTitle = 'Filtered Products | Wynvalley';
            }
        } elseif ($selectedCategory) {
            $pageTitle = $selectedCategory['name'] . ' | Wynvalley';
        }
        
        $this->render('category/index', [
            'layout' => 'main',
            'pageTitle' => $pageTitle,
            'categories' => $categories,
            'categoriesWithSubcategories' => $categoriesWithSubcategories,
            'products' => $products,
            'selectedSubcategory' => !empty($selectedSubcategories) ? $selectedSubcategories[0] : null, // For backward compatibility
            'selectedSubcategories' => $selectedSubcategories,
            'selectedCategory' => $selectedCategory,
            'subcategoryIds' => $subcategoryIds,
            'filter' => $filter
        ]);
    }
}

