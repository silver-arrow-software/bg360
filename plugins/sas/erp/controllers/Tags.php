<?php namespace Sas\Erp\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Sas\Erp\Models\Tag;
use Flash;

/**
 * Tags Back-end Controller
 */
class Tags extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Sas.Erp', 'sas-erp-main-menu-item', 'sas-erp-side-menu-tags');
    }

    /**
     * Delete tags
     *
     * @return  $this->listRefresh()
     */
    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds))
            $delete = Tag::whereIn('id', $checkedIds)->delete();

        if (!isset($delete) && !$delete)
            return Flash::error('sas.erp::lang.message.error');

        Flash::success('sas.erp::lang.message.delete_success');

        return $this->listRefresh();
    }

    /**
     * Removes tags with no associated posts
     *
     * @return  $this->listRefresh()
     */
    public function index_onRemoveOrphanedTags()
    {
        if (!$delete = Tag::has('posts', 0)->delete())
            return Flash::error('sas.erp::lang.message.error');

        Flash::success('sas.erp::lang.message.delete_success');

        return $this->listRefresh();
    }
}
