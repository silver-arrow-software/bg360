<?php namespace Sas\Erp\Models;

use Model;
use Backend\Models\User;
use Sas\Erp\Models\Tag;

/**
 * Model
 */
class Place extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_places';

    private $tags = [];

    public $implement = [
        'RainLab.Location.Behaviors.LocationModel'
    ];

    public $hasMany = [
        'owners' => 'RainLab\User\Models\User'
    ];

    public $belongsTo = [
        'country' => 'RainLab\Location\Models\Country',
        'state' => 'RainLab\Location\Models\State'
    ];

    public $morphToMany = [
        'tags' => [
            'Sas\Erp\Models\Tag',
            'table' => 'sas_erp_taggables',
            'name' => 'taggable'
        ],
    ];

    public $morphMany = [
        'accounts' => [
            'Sas\Erp\Models\Account',
            'name' => 'accountable'
        ],
    ];

    public $attachOne = [
        'logo' => ['System\Models\File']
    ];

    public function getOwnersOptions()
    {
        return RainLab\User\Models\User::lists('name', 'id');
    }

    public function canEdit(User $user)
    {
        return ($this->user_id == $user->id) || $user->hasAnyAccess(['sas.erp.edit_place']);
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

    public function getCodeIdAttribute($value) {
        return $value ? $value : uniqid();
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
            'perPage'    => 10,
            'search'     => '',
        ], $options));

        $searchableFields = ['name', 'code_id', 'description'];

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
        $params = [
            'id' => $this->id,
            'slug' => $this->code_id,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    /**
     * Returns the public image file path to this user's avatar.
     */
    public function getLogoThumb($size = 25, $options = null) {
        if (is_string($options)) {
            $options = ['default' => $options];
        }
        elseif (!is_array($options)) {
            $options = [];
        }

        // Default is "mm" (Mystery man)
        $default = array_get($options, 'default', 'mm');

        if ($this->logo) {
            return $this->logo->getThumb($size, $size, $options);
        }
        else {
            return null;
        }
    }
}