<?php namespace Sas\Erp\Components;

use Log;
use Input;
use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Project;
use Sas\Erp\Models\Task;

class ProjectDetail extends ComponentBase
{

    /**
     * @var Project
     */
    public $project;

    /**
     * A collection of project columns to display
     * @var Collection
     */
    public $columns;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.erp::lang.project.component_name',
            'description' => 'sas.erp::lang.project.component_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'sas.erp::lang.project.slug',
                'description' => 'sas.erp::lang.project.slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
        ];
    }

    public function onRun()
    {
        // Add assets for kanban
        $this->addJs('/plugins/sas/erp/assets/js/kanban.js');
        $this->addJs('/plugins/sas/erp/assets/js/toggleLoading.js');
        $this->addJs('/plugins/sas/erp/assets/js/jquery-ui/jquery-ui.js');
        $this->addCss('/plugins/sas/erp/assets/js/jquery-ui/jquery-ui.css');
        $this->addCss('/plugins/sas/erp/assets/css/sas.erp.css');

        $this->prepareVars();
    }

    protected function prepareVars()
    {
        $this->project = $this->page['project'] = $this->loadProject();
        $this->columns = $this->page['columns'] = $this->getProjectColumns();
    }

    protected function loadProject()
    {
        $slug = $this->property('slug');
        $project = Project::where('team_id', 1)->where('id', $slug)->first()->load('tasks');
        return $project;
    }

    protected function getProjectColumns()
    {
        return [
            ['id' => 1, 'name' => 'Backlog', 'wip' => 0],
            ['id' => 2, 'name' => 'In Progress', 'wip' => 0],
            ['id' => 3, 'name' => 'Finished', 'wip' => 0]
        ];
    }

    public function onUpdateTask()
    {
        $params = Input::all();

        $task = null;
        if ( empty($params['tsk']) ) { // add new task
            if( empty($params['prj']) ) {
                return [];
            }

            $params['project_id'] = $params['prj'];

            $params = array_merge([
                'title' => 'New Task',
                'description' => 'Update new task',
                'status' => 1
            ], $params);

            $task = new Task();

            foreach (['title', 'description', 'status', 'project_id'] as $f){
                $task->{$f} = $params[$f];
            }

        } else {
            $task = Task::find($params['tsk']);

            if (empty($params['del'])) {
                if( isset($params['col']) ) {
                    $task->status = $params['col'];
                }

                if( isset($params['title']) ) {
                    $task->title = $params['title'];
                }
                if( isset($params['description']) ) {
                    $task->description = $params['description'];
                }
                if( isset($params['wip']) ) {
//            $task->wip = $params['wip'];
                }
            } else {
                $task->delete();
                return [];
            }

        }

        return $task ? ($task->save() ? $task->toArray() : []) : [];
    }

    public function onAddToCart() {
        $params = Input::all();
        if (isset($params['productId']) && isset($params['quantity']) && is_numeric($params['productId']) && is_numeric($params['quantity'])) {
            $productId = $params['productId'];
            $quantity = $params['quantity'];
            $attributes = [];
            if (isset($params['attributes']) && is_array($params['attributes']) && !empty($params['attributes'])) {
                $attributes = $params['attributes'];
            }
            $cart = OctoCart::add($productId, $quantity, $attributes);
        }
        else {
            Log::warning('OctoCart: ProductDisplay - onAddToCart().');
        }
    }

}