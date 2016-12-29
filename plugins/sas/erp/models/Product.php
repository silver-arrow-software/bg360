<?php namespace Sas\Erp\Models;

use Db;
use App;
use Html;
use Log;
use Str;
use Lang;
use Model;
use Markdown;
use Carbon\Carbon;
use Backend\Models\User;
use ValidationException;
use Sas\Erp\Models\Tag;

/**
 * Model
 */
class Product extends Model {
    use \October\Rain\Database\Traits\Validation;

    private $tags = [];

    /*
     * Validation
     */
    public $rules = [];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_products';

    public $morphToMany = [
        'tags' => [
            'Sas\Erp\Models\Tag',
            'table' => 'sas_erp_taggables',
            'name' => 'taggable'
        ],
    ];

    public $belongsTo = [
        'place' => 'Sas\Erp\Models\Place'
    ];

    public $hasMany = [
        'productAttributes' => [
            'Sas\Erp\Models\ProductAttribute',
            'order' => 'name', //TODO Custom order
        ],
        'productItems' => 'Sas\Erp\Models\ProductItem',
    ];

    public $attachMany = [
        'images' => ['System\Models\File', 'order' => 'sort_order'],
    ];

    /**
     * The attributes on which the product list can be ordered
     * @var array
     */
    public static $allowedSortingOptions = array(
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
        'random' => 'Random'
    );

    public function canEdit(User $user) {
        return ($this->user_id == $user->id) || $user->hasAnyAccess(['sas.erp.edit_product']);
    }

    public function getTagboxAttribute() {
        return $this->tags()->lists('name');
    }

    public function setTagboxAttribute($tags) {
        $this->tags = $tags;
    }

    public function afterSave() {
        if ($this->tags) {
            $ids = [];
            foreach ($this->tags as $name) {
                $create = Tag::firstOrCreate(['name' => $name]);
                $ids[] = $create->id;
            }
            $this->tags()->sync($ids);
        }
    }

    //
    // Scopes
    //
    public function scopeIsPublished($query) {
        return $query
            ->whereNotNull('published')
            ->where('published', true)
            ->whereNotNull('created_at')
            ->where('created_at', '<', Carbon::now())
            ;
    }

    public function scopeIsFeatured($query) {
        return $query
            ->whereNotNull('featured')
            ->where('featured', true)
            ;
    }

    public function getDescriptionAttribute($value) {
        return $value ? $value : trans('sas.erp::lang.product.default_boardgame_desc');
    }

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
            'perPage'    => 8,
            'sort'       => 'created_at',
            //'categories' => null,
            //'category'   => null,
            'search'     => '',
            'published'  => true,
            'promote'    => false,
        ], $options));

        $searchableFields = ['title', 'description'];

        if ($published) {
            $query->isPublished();
        }

        if ($promote) {
            $query->isFeatured();
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
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        /*
         * Categories
         */
        // if ($categories !== null) {
        //     if (!is_array($categories)) $categories = [$categories];
        //     $query->whereHas('categories', function($q) use ($categories) {
        //         $q->whereIn('id', $categories);
        //     });
        // }

        /*
         * Category, including children
         */
        // if ($category !== null) {
        //     $category = Category::find($category);
        //
        //     $categories = $category->getAllChildrenAndSelf()->lists('id');
        //     $query->whereHas('categories', function($q) use ($categories) {
        //         $q->whereIn('id', $categories);
        //     });
        // }

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
            'slug' => $this->slug,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    public function getSquareThumb($size, $image) {
        return $image->getThumb($size, $size, ['mode' => 'crop']);
    }

}
