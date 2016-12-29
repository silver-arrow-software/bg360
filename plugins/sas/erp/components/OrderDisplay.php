<?php namespace Sas\Erp\Components;

use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Order;
use Sas\Erp\Models\Product;
use Sas\Erp\Models\Settings;

class OrderDisplay extends ComponentBase
{
    /**
     * @var Sas\Erp\Models\Order The order model used for display.
     */
    public $order;

    /**
     * An array of products
     * @var Collection
     */
    public $items;

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

    public function componentDetails() {
        return [
            'name'        => 'sas.erp::lang.order.name',
            'description' => 'sas.erp::lang.order.description'
        ];
    }

    public function defineProperties() {
        return [
            'id' => [
                'title'       => 'sas.erp::lang.order.id',
                'description' => 'sas.erp::lang.order.id_description',
                'default'     => '{{ :id }}',
                'type'        => 'string'
            ],
        ];
    }

    public function onRun() {
        $this->prepareVars();
    }

    protected function prepareVars() {
        $this->order = $this->page['order'] = $this->loadOrder();

        /*
         * Page links
         */
        $settings = Settings::instance();
        $this->productDisplayPage = $this->page['productDisplayPage'] = $settings->productDisplayPage;
        //$this->categoryPage = $this->page['categoryPage'] = $settings->categoryPage;

        $this->items = $this->page['items'] = $this->listItems();
    }

    protected function loadOrder() {
        $id = $this->property('id');
        $order = Order::find($id);
        $order->billing_info = json_decode($order->billing_info, true);
        $order->shipping_info = json_decode($order->shipping_info, true);
        $order->items = json_decode($order->items, true);
        return $order;
    }

    protected function listItems() {
        $items = $this->order->items;
        foreach ($items as $itemId => $item) {
            $product = Product::find($item['product']);
            $product->setUrl($this->productDisplayPage, $this->controller);
            // $product->categories->each(function ($category) {
            //     $category->setUrl($this->categoryPage, $this->controller);
            // });
            $items[$itemId]['product'] = $product;
        }
        return $items;
    }

}
