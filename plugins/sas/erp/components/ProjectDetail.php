<?php namespace Sas\Erp\Components;

use Log;
use Input;
use Cms\Classes\ComponentBase;
use Sas\Erp\Models\Project;
use Sas\Erp\Models\Task;
use Sas\Erp\Models\ProjectColumn;

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

    /**
     * @var array
     */
    public $linked_statuses;

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
        $this->addJs('/plugins/sas/erp/assets/js/kanban.js?nocache=1');
        $this->addJs('/plugins/sas/erp/assets/js/toggleLoading.js');
        $this->addJs('/plugins/sas/erp/assets/js/jquery-ui/jquery-ui.js');
        $this->addCss('/plugins/sas/erp/assets/js/jquery-ui/jquery-ui.css');
        $this->addCss('/plugins/sas/erp/assets/css/sas.erp.css');

        $this->prepareVars();
    }

    protected function prepareVars()
    {
        $this->project = $this->page['project'] = $this->loadProject();
        $tasks = $this->project->tasks;
        $_columns = $this->getProjectColumns();
        $columns = [];

        /** @var ProjectColumn $_column */
        foreach ($_columns as $_column) {
            $items = [];
            foreach($tasks as $task){
                if($task->column_id == $_column->id) {
                    $items[] = $task->toArray();
                }
            }
            $column = $_column->toArray();
            $column['items'] = $items;
            $columns[] = $column;
        }
        $this->columns = $this->page['columns'] = $columns;

        $this->linked_statuses = Task::getStatusOptions();
    }

    protected function loadProject()
    {
        $slug = $this->property('slug');

        if (! $slug) {
            throw new SystemException('There is no project identification.');
        }

        $project = Project::where('team_id', 1)->where('id', $slug)->first()->load('tasks');
        return $project;
    }

    protected function getProjectColumns()
    {
        return ProjectColumn::where('project_id', $this->property('slug'))->orderBy('position')->get();
    }

    public function onUpdateItem()
    {
        $task = null;
        $params = Input::all();

        if ( empty($params['id']) ) { // add new task
            if( empty($params['pid']) || empty($params['list_id'])) {
                return [];
            }

            $task = new Task;

            $task->title = empty($params['name']) ? 'New Task' : $params['name'];
            $task->description = empty($params['description']) ? '' : $params['description'];
            $task->status = 1;
            $task->project_id = $params['pid'];
            $task->column_id = $params['list_id'];
            $task->position = $this->_calculateItemPosition($params);

        } else {
            if ($task = Task::find($params['id'])) {
                if (empty($params['del'])) {
                    if( isset($params['list_id']) ) {
                        $task->column_id = $params['list_id'];
                    }

                    if( isset($params['name']) ) {
                        $task->title = $params['name'];
                    }
                    if( isset($params['description']) ) {
                        $task->description = $params['description'];
                    }
                } else {
                    $task->delete();
                    $task = null;
                }
            }
        }

        return $task && $task->save() ? $task->toArray() : [];
    }

    public function onUpdateColumn()
    {
        $column = null;
        $params = Input::all();

        if (empty($params['id'])) { // new column
            if( empty($params['pid']) ) {
                return [];
            }

            $column = new ProjectColumn;

            $column->name = empty($params['name']) ? 'New Column' : $params['name'];
            $column->linked_status = isset($params['ls']) ? $params['ls'] : 1;
            $column->project_id = $params['pid'];
            $column->position = $this->_calculateColumnPosition($params);

        } else {
            if ($column = ProjectColumn::find($params['id'])) {
                if (empty($params['del'])) {
                    if (isset($params['name'])) {
                        $column->name = $params['name'];
                    }
                    if (isset($params['ls'])) {
                        $column->linked_status = $params['ls'];
                    }
                } else {
                    $column->delete();
                    $column = null;
                }
            }
        }

        return $column && $column->save() ? $column->toArray() : [];
    }

    public function onUpdateOrder()
    {
        $params = Input::all();

        $record = null;
        if (empty($params['mode']) || $params['mode'] == 'list') {
            if (!empty($params['id'])) {

                if ($record = ProjectColumn::find($params['id'])) {
                    $record->position = $this->_calculateColumnPosition($params);
                }
            }
        } else if($params['mode'] == 'item') {
            if (!empty($params['id'])) {

                if ($record = Task::find($params['id'])) {
                    if (!empty($params['list_id'])) {
                        if ($column = ProjectColumn::find($params['list_id'])) {
                            $record->column_id = $params['list_id'];
                            $record->status = $column->linked_status;
                        }
                    }
                    $record->position = $this->_calculateItemPosition($params);
                }
            }
        }

        return $record && $record->save() ? $record->toArray() : [];
    }

    protected function _calculateColumnPosition($params)
    {
        $previousPosition = null;
        $nextPosition = null;
        if (!empty($params['previous_list_id'])) {
            if ($previousColumn = ProjectColumn::find($params['previous_list_id'])) {
                $previousPosition = $previousColumn->position;
            }
        }
        if (!empty($params['next_list_id'])) {
            if ($nextColumn = ProjectColumn::find($params['next_list_id'])) {
                $nextPosition = $nextColumn->position;
            }
        }

        $delta = 0.0000000001;
        if (is_null($previousPosition)) {
            $previousPosition = ($nextPosition ?: 5) - $delta;
        }
        if (is_null($nextPosition)) {
            $nextPosition = ($previousPosition ?: 5) + $delta;
        }

        return ($previousPosition + $nextPosition)/2 ?: 5;
    }

    protected function _calculateItemPosition($params)
    {
        $previousPosition = null;
        $nextPosition = null;
        if (!empty($params['previous_item_id'])) {
            if ($previousTask = Task::find($params['previous_item_id'])) {
                $previousPosition = $previousTask->position;
            }
        }
        if (!empty($params['next_item_id'])) {
            if ($nextTask = Task::find($params['next_item_id'])) {
                $nextPosition = $nextTask->position;
            }
        }

        $delta = 0.0000000001;
        if (is_null($previousPosition)) {
            $previousPosition = ($nextPosition ?: 5.00) - $delta;
        }
        if (is_null($nextPosition)) {
            $nextPosition = ($previousPosition ?: 5.00) + $delta;
        }

        return floatval(($previousPosition + $nextPosition)/2) ?: 5.00;
    }

}