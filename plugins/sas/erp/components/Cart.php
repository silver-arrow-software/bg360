<?php namespace Sas\Erp\Components;

use Log;
use Input;
use Session;
use SasCart;
use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Product;
use Sas\Erp\Models\Settings;

class Cart extends ComponentBase {

    /**
     * An array of products
     * @var Collection
     */
    public $items;

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
     * Reference to the page name for linking to checkout.
     * @var string
     */
    public $checkoutPage;

    /**
     * The price total.
     * @var float
     */
    public $totalPrice;

    /**
     * The number of items in the cart.
     * @var integer
     */
    public $count;

    public function componentDetails() {
        return [
            'name'        => 'sas.erp::lang.cart.name',
            'description' => 'sas.erp::lang.cart.description'
        ];
    }

    public function defineProperties() {
        return [
            'noProductsMessage' => [
                'title'        => 'sas.erp::lang.cart.no_products',
                'description'  => 'sas.erp::lang.cart.no_products_description',
                'type'         => 'string',
                'default'      => 'No products found',
                'showExternalParam' => false
            ],
        ];
    }

    public function onRun() {
        $this->prepareVars();
    }

    public function onRender() {
        //$this->prepareVars();
        $this->items = $this->page['items'] = $this->listItems();
    }

    protected function prepareVars() {
        $this->noPostsMessage = $this->page['noProductsMessage'] = $this->property('noProductsMessage');

        $this->totalPrice = $this->page['totalPrice'] = SasCart::total();
        $this->count = $this->page['count'] = SasCart::count();

        /*
         * Page links
         */
        $settings = Settings::instance();
        $this->cartPage = $this->page['cartPage'] = $settings->cartPage;
        $this->checkoutPage = $this->page['checkoutPage'] = $settings->checkoutPage;
        $this->productDisplayPage = $this->page['productDisplayPage'] = $settings->productDisplayPage;
        //$this->categoryPage = $this->page['categoryPage'] = $settings->categoryPage;
    }

    protected function listItems() {
        $items = SasCart::get();
        if (!is_null($items)) {
            foreach ($items as $itemId => $item) {
                $product = Product::find($item['product']);
                $product->setUrl($this->productDisplayPage, $this->controller);
                $items[$itemId]['product'] = $product;
            }
        }
        return $items;
    }

    public function onUpdateQuantity() {
        $params = Input::all();
        if (isset($params['itemId']) && isset($params['quantity']) && is_numeric($params['quantity'])) {
            $itemId = $params['itemId'];
            $quantity = $params['quantity'];
            $cart = SasCart::update($itemId, $quantity);
        }
    }

    public function onRemoveProduct() {
        $params = Input::all();
        if (isset($params['itemId'])) {
            $itemId = $params['itemId'];
            $cart = SasCart::remove($itemId);
        }
    }

    public function onClear() {
        $cart = SasCart::clear();
    }

}
