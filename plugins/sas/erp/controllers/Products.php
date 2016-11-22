<?php namespace Sas\Erp\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use Sas\Erp\Models\Product;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = ['sas.erp.manage_products'];

    public function __construct()
    {
        parent::__construct();
		
		BackendMenu::setContext('Sas.Erp', 'sas-erp-main-menu-item', 'sas-erp-side-menu-products');
    }

    public function index()
    {
        $this->vars['productsTotal'] = Product::count();
        $this->vars['productsPublished'] = Product::isPublished()->count();
        $this->vars['productsDrafts'] = $this->vars['productsTotal'] - $this->vars['productsPublished'];

        $this->asExtension('ListController')->index();
    }

    public function create()
    {
        $this->addCss('/plugins/sas/erp/assets/css/sas.erp.css');
        return $this->asExtension('FormController')->create();
    }

    public function update($recordId = null)
    {
        $this->addCss('/plugins/sas/erp/assets/css/sas.erp.css');
        return $this->asExtension('FormController')->update($recordId);
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if ((!$product = Product::find($productId)) || !$product->canEdit($this->user))
                    continue;

                $product->delete();
            }

            Flash::success(sas.erp::lang.message.delete_success);
        }

        return $this->listRefresh();
    }

    public function index_onShow()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if ((!$product = Product::find($productId)) || !$product->canEdit($this->user))
                    continue;

                $product->published = 1;
                $product->save();
            }

            Flash::success(sas.erp::lang.message.show_success);
        }

        return $this->listRefresh();
    }

    public function index_onHide()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if ((!$product = Product::find($productId)) || !$product->canEdit($this->user))
                    continue;

                $product->published = 0;
                $product->save();
            }

            Flash::success(sas.erp::lang.message.hide_success);
        }

        return $this->listRefresh();
    }

    public function formBeforeCreate($model)
    {
        $model->user_id = $this->user->id;
    }

}