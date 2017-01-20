<?php namespace Sas\Erp\Models;

use Model;
use Carbon\Carbon;

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
        'product' => ['Sas\Erp\Models\Product'],
        'place' => 'Sas\Erp\Models\Place'
    ];

    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];

    public $attachMany = [
        'images' => ['System\Models\File', 'order' => 'sort_order'],
    ];

    public function getCodeAttribute($value) {
        return $value ? $value : uniqid();
    }

    public static $allowedSortingOptions = array(
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
        'random' => 'Random'
    );

    public function scopeListFrontEnd($query, $options) {
        /*
         * Default options
         */
        extract(array_merge([
            'owner' => 0,
            'page'       => 1,
            'perPage'    => 8,
            'sort'       => 'created_at',
            'search'     => '',
            'status' => 1,
        ], $options));

        if ($owner > 0) {
            $query->where('place_id', '=', $owner);
        }
        /*
         * Sorting
         */
        if (!is_array($sort)) {
            $sort = [$sort];
        }

        foreach ($sort as $_sort) {

            if (in_array($_sort, array_keys(self::$allowedSortingOptions))) {
                $parts = explode(' ', $_sort);
                if (count($parts) < 2) {
                    array_push($parts, 'desc');
                }
                list($sortField, $sortDirection) = $parts;
                if ($sortField == 'random') {
                    $sortField = DB::raw('RAND()');
                }
                $query->orderBy($sortField, $sortDirection);
            }
        }

        /*
         * Search
         */
        $searchableFields = ['title', 'description'];
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
        $params = [
            'id' => $this->id,
            'slug' => $this->code,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    public function scopeIsPublished($query) {
        return $query
            ->whereNotNull('status')
            ->where('status', 1)
            ->whereNotNull('created_at')
            ->where('created_at', '<', Carbon::now())
            ;
    }
}
