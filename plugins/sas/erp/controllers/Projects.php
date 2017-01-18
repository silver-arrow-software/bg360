<?php namespace Sas\Erp\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Projects extends Controller
{
    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'sas.erp.manage_projects' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Sas.Erp', 'sas-erp-main-menu-item', 'side-menu-item-projects');
    }

    public function onTags()
    {
        // Search tags on DB
        $term = input('q');

        $searchField = 'name';
        $tags = Tag::where($searchField, 'LIKE', '%'.$term.'%')
            ->orderByRaw("CASE WHEN {$searchField} = '{$term}' THEN 0  
                          WHEN {$searchField} LIKE '{$term}%' THEN 1  
                          WHEN {$searchField} LIKE '%{$term}%' THEN 2  
                          WHEN {$searchField} LIKE '%{$term}' THEN 3  
                          ELSE 4
                     END, {$searchField} ASC")
            ->get()
        ;

        $result = [];

        // add new result
        $result[$term] = $term;

        foreach ($tags as $tag){
            $result[$tag->name] = $tag->name;
        }

        // Render the partial and return it as our list item
        return $result;
    }
}