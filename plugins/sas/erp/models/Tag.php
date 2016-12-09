<?php namespace Sas\Erp\Models;

use Model;
use Sas\Blog\Models\Post;
use Sas\Erp\Models\Place;

/**
 * Model
 */
class Tag extends Model
{
    use \October\Rain\Database\Traits\Validation;
    //use \October\Rain\Database\Traits\SoftDelete;

    //protected $dates = ['deleted_at'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_tags';

    /**
     * @var array Relations
     */
    public $morphedByMany = [
        'places' => [
            'Sas\Erp\Models\Place',
            'table' => 'sas_erp_taggables',
            'name' => 'taggable',
            'order' => 'updated_at desc'
        ],
        'posts' => [
            'Sas\Blog\Models\Post',
            'table' => 'sas_erp_taggables',
            'name' => 'taggable',
            'order' => 'published_at desc'
        ],
        'products' => [
            'Sas\Erp\Models\Product',
            'table' => 'sas_erp_taggables',
            'name' => 'taggable',
            'order' => 'updated_at desc'
        ],
    ];

    /**
     * @var array Fillable fields
     */
    public $fillable = ['name'];

    /**
     * @var array Validation rules
     */
    public $rules = [
        //'name' => 'required|unique:sas_erp_tags|regex:/^[a-z0-9-]+$/'
        'name' => 'required|unique:sas_erp_tags'
    ];

    public $customMessages = [
        'name.required' => 'A tag name is required.',
        'name.unique'   => 'A tag by that name already exists.',
        'name.regex'    => 'Tags may only contain alpha-numeric characters and hyphens.'
    ];

    /**
     * Convert tag names to lower case
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = mb_strtolower($value);
    }

     /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    /*public function setUrl($pageName, $controller) {
        $params = [
            'id' => $this->id,
            'slug' => $this->slug,
        ];
       
        return $this->url = $controller->pageUrl($pageName, $params);
    }*/
}