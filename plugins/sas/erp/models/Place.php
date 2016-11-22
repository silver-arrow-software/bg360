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
    public $rules = [
    ];

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
}