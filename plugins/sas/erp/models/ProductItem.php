<?php namespace Sas\Erp\Models;

use Model;

/**
 * Item in stock Model
 */
class ProductItem extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_product_items';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['code', 'price', 'quantity'];

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
    
    public function getCodeAttribute($value) {
        return $value ? $value : uniqid();
    }

}