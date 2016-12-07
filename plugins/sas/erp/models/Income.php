<?php namespace Sas\Erp\Models;

use Model;

/**
 * Income Model
 */
class Income extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_incomes';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

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
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}