<?php namespace Sas\Erp\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Places extends Controller
{
    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct() {
        parent::__construct();

        BackendMenu::setContext('Sas.Erp', 'sas-erp-main-menu-item', 'sas-erp-side-menu-places');
    }

    public function formBeforeCreate($model) {
        $model->created_by = $this->user->id;
    }

    public function listExtendQuery($query) {
        $query->whereNull('deleted_at');
    }

    public function index_onDelete() {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $placeId) {
                if ((!$place = Place::find($companyId)) || !$place->canEdit($this->user))
                    continue;

                $place->deleted_at = Carbon::now();
                $place->save();
            }

            Flash::success('sas.erp::lang.message.delete_success');
        }

        return $this->listRefresh();
    }
}