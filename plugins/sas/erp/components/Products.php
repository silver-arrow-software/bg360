<?php namespace Sas\Erp\Components;

use Input;
use SasCart;
use Redirect;
use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Product;
use Sas\Erp\Models\Settings;
use Sas\Erp\Models\Category;

class Products extends ComponentBase
{

    /**
     * A collection of products to display
     * @var Collection
     */
    public $products;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    /**
     * If the product list should be filtered by a category, the model to use.
     * @var Model
     */
    public $category;

    /**
     * Message to display when there are no products.
     * @var string
     */
    public $noProductsMessage;

    /**
     * Reference to the page name for linking to products.
     * @var string
     */
    public $productDisplayPage;

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $categoryPage;

    /**
     * Reference to the page name for linking to cart.
     * @var string
     */
    public $cartPage;

    /**
     * If the product list should be ordered by another attribute.
     * @var string
     */
    public $sortOrder;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.erp::lang.products.name',
            'description' => 'sas.erp::lang.products.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'sas.erp::lang.products.pagination',
                'description' => 'sas.erp::lang.products.pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            /*'categoryFilter' => [
                'title'       => 'sas.erp::lang.products.filter',
                'description' => 'sas.erp::lang.products.filter_description',
                'type'        => 'string',
                'default'     => ''
            ],*/
            'productsPerPage' => [
                'title'             => 'sas.erp::lang.products.products_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'sas.erp::lang.products.products_per_page_validation',
                'default'           => '10',
            ],
            'noProductsMessage' => [
                'title'        => 'sas.erp::lang.products.no_products',
                'description'  => 'sas.erp::lang.products.no_products_description',
                'type'         => 'string',
                'default'      => 'No products found',
                'showExternalParam' => false
            ],
            'sortOrder' => [
                'title'       => 'sas.erp::lang.products.order',
                'description' => 'sas.erp::lang.products.order_description',
                'type'        => 'dropdown',
                'default'     => 'created_at desc'
            ],
            'promote' => [
                'title'       => 'sas.erp::lang.product.feature',
                'type'        => 'checkbox',
                'default'     => false
            ],

        ];
    }

    public function getSortOrderOptions() {
        return Product::$allowedSortingOptions;
    }

    public function onRun() {
        $this->prepareVars();

        //$this->category = $this->page['category'] = $this->loadCategory();
        $this->products = $this->page['products'] = $this->listProducts();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->products->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars()
    {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->noProductsMessage = $this->page['noProductsMessage'] = $this->property('noProductsMessage');

        /*
         * Page links
         */
        $settings = Settings::instance();
        $this->productDisplayPage = $this->page['productDisplayPage'] = $settings->productDisplayPage;
        //$this->categoryPage = $this->page['categoryPage'] = $settings->categoryPage;
        $this->cartPage = $this->page['cartPage'] = $settings->redirect_user_after_add_to_cart;
    }

    protected function listProducts()
    {
        //$category = $this->category ? $this->category->id : null;

        /*
         * List all the products, eager load their categories
         */
        $products = Product::listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'sort'       => $this->property('sortOrder'),
            'perPage'    => $this->property('productsPerPage'),
            //'category'   => $category,
            'search'     => isset($_GET['search']) ? $_GET['search'] : '',
            'promote'    => $this->property('promote'),
        ]);

        /*
         * Add a "url" helper attribute for linking to each product and category
         */
        $products->each(function($product) {
            $product->setUrl($this->productDisplayPage, $this->controller);

            /*$product->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });*/
        });

        return $products;
    }

    protected function loadCategory()
    {
        if (!$categoryId = $this->property('categoryFilter'))
            return null;

        if (!$category = Category::whereSlug($categoryId)->first())
            return null;

        return $category;
    }

    public function onAddToCart() {
        $params = Input::all();
        if (isset($params['productId']) && isset($params['quantity']) && is_numeric($params['productId']) && is_numeric($params['quantity'])) {
            $productId = $params['productId'];
            $quantity = $params['quantity'];
            $attributes = [];
            if (isset($params['attributes']) && is_array($params['attributes']) && !empty($params['attributes'])) {
                $attributes = $params['attributes'];
            }
            $cart = OctoCart::add($productId, $quantity, $attributes);
        }
        else {
            Log::warning('SasCart: Products - onAddToCart().');
        }
    }

}