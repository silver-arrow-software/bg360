<?php namespace Sas\Erp\Models;

use Model;
use Auth;
use RainLab\User\Models\User;

/**
 * Model
 */
class Project extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required'
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = true;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_projects';

    public $belongsTo = [
        'place' => [ 'Sas\Erp\Models\Place' ],
        'team' => [ 'Sas\Erp\Models\Team' ],
    ];

    public $hasMany = [
        'tasks' => [ 'Sas\Erp\Models\Task', 'order' => 'position' ]
    ];

    public $attachMany = [
        'files' => 'System\Models\File',
    ];

    public function getTeamIdOptions()
    {
        return Team::lists('name', 'id');
    }

    public function getPlaceIdOptions()
    {
        return Place::lists('name', 'id');
    }

    public function scopeBelongUser($query, $user = null)
    {
        if(is_null($user)){
            $user = Auth::getUser();
        }
        if($user instanceof User){
            $user = $user->getKey();
        }
        return $query->whereHas('team.users', function ($q) use ($user){
            return $q->where('id', $user);
        });
    }

}