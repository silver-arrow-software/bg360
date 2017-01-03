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
    //protected $guarded = ['*'];

    /**
     * @var array Hidden fields from array/json access
     */
    protected $hidden = ['accountable_id', 'accountable_type'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name', 'description'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'transactions' => [
            'Sas\Erp\Models\AccountTransaction',
            'order' => 'created_at desc',
            'delete' => true,
        ],
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [
        'accountable' => []
    ];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * Lists products for the front end
     * @param  array $options Display options
     * @return self
     */
    public function scopeListFrontEnd($query, $options) {
        /*
         * Default options
         */
        extract(array_merge([
            'page'       => 1,
            'perPage'    => 5,
            'search'     => '',
        ], $options));

        $searchableFields = ['name', 'description'];

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        return $query->paginate($perPage, $page);
    }

    /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    public function setUrl($pageName, $controller) {
        $slugstr = str_slug($this->name . ' ' . $this->id, '-');
        $params = [
            'id' => $this->id,
            'slug' => $slugstr,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
    }
}
