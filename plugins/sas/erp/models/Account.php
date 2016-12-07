<?php namespace Sas\Erp\Models;

use Model;

/**
 * Account Model
 */
class Account extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_accounts';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Hidden fields from array/json access
     */
    protected $hidden = ['accountable_id', 'accountable_type']; 

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [
        'accountable' => []
    ];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}