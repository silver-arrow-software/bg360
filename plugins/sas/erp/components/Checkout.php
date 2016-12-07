<?php namespace Sas\Erp\Components;

use Log;
use Mail;
use Input;
use SasCart;
use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Order;
use Sas\Erp\Models\Product;
use Backend\Models\BrandSettings;
use Sas\Erp\Models\Settings;

class Checkout extends ComponentBase
{

    public $successPage;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.erp::lang.checkout.name',
            'description' => 'sas.erp::lang.checkout.description'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRender() {
        $this->prepareVars();
    }

    protected function prepareVars()
    {
        /*
         * Page links
         */
        $settings = Settings::instance();
        $this->successPage = $this->page['successPage'] = $settings->successPage;
    }

    public function onCheckout()
    {
        $input    = Input::all();
        $billing  = array();
        $shipping = array();

        // Prepare vars
        if (isset($input['billing'])) {
            $billing = $input['billing'];
        }

        if (isset($input['shipping'])) {
            $shipping = $input['shipping'];
        }

        $items = $products = SasCart::get();
        if (!is_null($items)) {
            foreach ($items as $itemId => $item) {
                $product = Product::find($item['product']);
                $product->setUrl($this->productDisplayPage, $this->controller);
                $product->categories->each(function($category) {
                    $category->setUrl($this->categoryPage, $this->controller);
                });
                $products[$itemId]['product'] = $product;
            }
        }

        $totalPrice = SasCart::total();
        $count = SasCart::count();

        // Get Settings
        $settings = Settings::instance();

        // Mail
        $sendUserMessage = $settings->send_user_message;

        // To Customer
        if($sendUserMessage && !empty($sendUserMessage) && isset($billing['email'])) {
            Mail::sendTo($billing['email'], 'sas.erp::mail.order_confirm', [
                'name'     => isset($billing['name']) ? $billing['name'] : 'Customer',
                'site'     => BrandSettings::get('app_name'),
                'items'    => $products,
                'shipping' => $shipping,
                'billing'  => $billing,
                'total'    => $totalPrice->total,
                'vat'      => $totalPrice->vat,
                'count'    => $count,
            ]);
        }

        // To Admins
        $adminEmails = $settings->admin_emails;
        if($adminEmails && !empty($adminEmails)) {
            $adminEmails = explode("\n", $adminEmails);
            foreach($adminEmails as $email) {
                $email = trim($email);
                Mail::sendTo($email, 'sas.erp::mail.order_confirm_admin', [
                    'billing'  => $billing,
                    'shipping' => $shipping,
                    'site'     => BrandSettings::get('app_name'),
                    'items'    => $products,
                    'total'    => $totalPrice->total,
                    'vat'      => $totalPrice->vat,
                    'count'    => $count,
                ]);
            }
        }

        // Create New Order
        $order = new Order;
        $order->email = isset($billing['email']) ? $billing['email'] : NULL;
        $order->items = json_encode($items);
        $order->billing_info = json_encode($billing);
        $order->shipping_info = json_encode($shipping);
        $order->total = $totalPrice->total;
        $order->vat = $totalPrice->vat;
        $order->currency = $settings->currency;
        $order->save();

        // Clear Cart
        $cart = SasCart::clear();

        // Go to the success page
        //if($this->successPage)
        //    return Redirect::to('/' . $this->successPage);
        //else
        //    return Redirect::to('/');

    }

}