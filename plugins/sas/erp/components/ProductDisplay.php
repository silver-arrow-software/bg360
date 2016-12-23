<?php namespace Sas\Erp\Components;

use Log;
use Input;
use SasCart;
use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Product;
use Sas\Erp\Models\Settings;

class ProductDisplay extends ComponentBase {

    /**
     * @var Sas\Erp\Models\Product The product model used for display.
     */
    public $product;

    /**
     * Reference to the page name for linking to cart.
     * @var string
     */
    public $cartPage;

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $categoryPage;

    public function componentDetails() {
        return [
            'name'        => 'sas.erp::lang.product.component_name',
            'description' => 'sas.erp::lang.product.component_desc'
        ];
    }

    public function defineProperties() {
        return [
            'slug' => [
                'title'       => 'sas.erp::lang.product.slug',
                'description' => 'sas.erp::lang.product.slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
        ];
    }

    public function onRun() {
        $this->addCss('assets/css/jquery.bxslider.css');
        $this->addJs('assets/js/jquery.bxslider.js');
		
        $this->prepareVars();
    }

    protected function prepareVars() {
        /*
         * Page links
         */
        $settings = Settings::instance();
        $this->cartPage = $this->page['cartPage'] = $settings->redirect_user_after_add_to_cart;
        //$this->categoryPage = $this->page['categoryPage'] = $settings->categoryPage;

        $this->product = $this->page['product'] = $this->loadProduct();
    }

    protected function loadProduct() {
        $slug = $this->property('slug');
        $product = Product::isPublished()->where('slug', $slug)->first();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        /*if ($product && $product->categories->count()) {
            $product->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }*/

        return $product;
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
            Log::warning('OctoCart: ProductDisplay - onAddToCart().');
        }
    }

}