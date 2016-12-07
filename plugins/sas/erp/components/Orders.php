<?php namespace Sas\Erp\Components;

use Auth;
use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Order;
use Sas\Erp\Models\Settings;

class Orders extends ComponentBase
{

    /**
     * A collection of orders to display
     * @var Collection
     */
    public $orders;

    /**
     * Message to display when there are no products.
     * @var string
     */
    public $noOrdersMessage;

    /**
     * Reference to the page name for linking to order.
     * @var string
     */
    public $orderDisplayPage;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.erp::lang.orders.name',
            'description' => 'sas.erp::lang.orders.description'
        ];
    }

    public function defineProperties()
    {
        return [
            'noOrdersMessage' => [
                'title'        => 'sas.erp::lang.orders.no_orders',
                'description'  => 'sas.erp::lang.orders.no_orders_description',
                'type'         => 'string',
                'default'      => 'No orders found',
                'showExternalParam' => false
            ]
        ];
    }

    public function onRun()
    {
        $this->prepareVars();
        $this->orders = $this->page['orders'] = $this->loadOrders();
    }

    protected function prepareVars()
    {
        $this->noOrdersMessage = $this->page['noOrdersMessage'] = $this->property('noOrdersMessage');

        /*
         * Page links
         */
        $settings = Settings::instance();
        $this->orderDisplayPage = $this->page['orderDisplayPage'] = $settings->orderDisplayPage;
    }

    protected function loadOrders()
    {
        $user = Auth::getUser();
        if (!isset($user)) {
            return array();
        }
        else {
            $orders = Order::all()->where('user_id', $user->id);
            /*
             * Add a "url" helper attribute for linking to each category
             */
            return $orders->each(function($order) {
                $order->setUrl($this->orderDisplayPage, $this->controller);
            });
        }
    }

}