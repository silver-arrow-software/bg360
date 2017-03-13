<?php namespace Sas\Erp\Models;

use Model;

/**
 * Model
 */
class ProjectColumn extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
        'project_id' => 'required',
        'name' => 'required'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_project_columns';

    public $belongsTo = [
        'project' => ['Sas\Erp\Models\Project']
    ];

    public $hasMany = [
        'tasks' => [ 'Sas\Erp\Models\Task', 'order' => 'position', 'key' => 'column_id' ]
    ];

    public function getProjectIdOptions()
    {
        return \Sas\Erp\Models\Project::lists('name', 'id');
    }

}