<?php namespace Sas\Erp\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Sas\Erp\Models\Task;

class Tasks extends Controller
{
    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'sas.erp.manage_tasks' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Sas.Erp', 'sas-erp-main-menu-item', 'side-menu-item-tasks');
    }

    /*public function create_onSave()
    {
        $result = [];
        $inputs = post('Task');

        $task = new Task();
        $task->name = "asjfl";
        $task->save();

        $result['id'] = $task->getKey();

        // Render the partial and return it as our list item
        return $result;
    }*/

}