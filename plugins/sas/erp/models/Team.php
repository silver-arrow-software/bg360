<?php namespace Sas\Erp\Models;

use Model;

/**
 * Model
 */
class Team extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_teams';

    public $belongsToMany = [
        'users' => ['RainLab\User\Models\User', 'table' => 'sas_erp_teams_users']
    ];

    public function getPlaceIdOptions()
    {
        return Place::lists('name', 'id');
    }
}