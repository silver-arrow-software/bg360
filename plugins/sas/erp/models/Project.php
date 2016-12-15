<?php namespace Sas\Erp\Models;

use Model;

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
        'place' => [ 'Sas\Erp\Models\Place' ]
    ];

    public function getTeamIdOptions()
    {
        return [];
    }

    public function getPlaceIdOptions()
    {
        return Place::lists('name', 'id');
    }
}