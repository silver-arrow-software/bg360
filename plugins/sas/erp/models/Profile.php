<?php namespace Sas\Erp\Models;

use Model;

/**
 * Model
 */
class Profile extends Model
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
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_profiles';

    public $hasMany = [
        'users' => 'RainLab\User\Models\User'
    ];

    public function getUsersOptions()
    {
        return RainLab\User\Models\User::lists('name', 'id');
    }
}