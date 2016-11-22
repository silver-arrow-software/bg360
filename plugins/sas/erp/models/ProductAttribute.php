<?php namespace Sas\Erp\Models;

use Model;

/**
 * Attribute Model
 */
class ProductAttribute extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_product_attributes';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name', 'value'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'products' => ['Sas\Erp\Models\Product']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}