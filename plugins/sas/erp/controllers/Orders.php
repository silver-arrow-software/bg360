<?php namespace Sas\Erp\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use Sas\Erp\Models\Order;

/**
 * Orders Back-end Controller
 */
class Orders extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController'
    ];

    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

		BackendMenu::setContext('Sas.Erp', 'sas-erp-main-menu-item', 'sas-erp-side-menu-orders');
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $orderId) {
                if (!$order = Order::find($orderId))
                    continue;

                $order->delete();
            }

            Flash::success('Successfully deleted those orders.');
        }

        return $this->listRefresh();
    }
}