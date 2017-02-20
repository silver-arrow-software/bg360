<?php namespace Sas\Erp\Models;

use Model;
use Auth;
use RainLab\User\Models\User;

/**
 * Model
 */
class Task extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
        'title'    => 'required',
        'project_id'    => 'required|numeric',
        'status'   => 'required|between:0,4|numeric'
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_tasks';

    public $belongsTo = [
        'project' => [ 'Sas\Erp\Models\Project' ],
    ];

    public function getProjectIdOptions()
    {
        return Project::lists('name', 'id');
    }

    public function getStatusOptions()
    {
        return [
            0 => 'sas.erp::lang.task_status.deleted',
            1 => 'sas.erp::lang.task_status.draft',
            2 => 'sas.erp::lang.task_status.doing',
            3 => 'sas.erp::lang.task_status.testing',
            4 => 'sas.erp::lang.task_status.completed'
        ];
    }

    public function scopeBelongUser($query, $user = null)
    {
        if(is_null($user)){
            $user = Auth::getUser();
        }
        if($user instanceof User){
            $user = $user->getKey();
        }
        return $query->whereHas('project.team.users', function ($q) use ($user){
            return $q->where('id', $user);
        });
    }

}
