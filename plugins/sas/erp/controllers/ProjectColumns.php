<?php namespace Sas\Erp\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class ProjectColumns extends Controller
{
    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'sas.erp.manage_columns'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Sas.Erp', 'sas-erp-main-menu-item', 'side-menu-item-project-columns');
    }

}