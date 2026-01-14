<?php
/**
 * Home Controller
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/SliderModel.php';

class HomeController extends Controller
{
    private $productModel;
    private $sliderModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->sliderModel = new SliderModel();
    }

    public function index(): void
    {
        $heroSlides = $this->sliderModel->getHeroSlides();
        $bestSellers = $this->productModel->getBestSellers(3);
        $collectionProducts = $this->productModel->getCollectionProducts(3);

        $this->render('home/index', [
            'layout' => 'main',
            'pageTitle' => 'Wynvalley | Premium Spice Boutique',
            'heroSlides' => $heroSlides,
            'bestSellers' => $bestSellers,
            'collectionProducts' => $collectionProducts
        ]);
    }
}

