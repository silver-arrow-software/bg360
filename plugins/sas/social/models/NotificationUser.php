<?php namespace Sas\Social\Models;

use Event;
use Model;
use Carbon\Carbon;

/**
 * NotificationUser model
 */
class NotificationUser extends Model
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'sas_social_notifications_users';

    /**
     * @var array List of datetime attributes to convert to an instance of Carbon/DateTime objects.
     */
    public $dates = ['read_at'];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['RainLab\User\Models\User'],
        'notification' => ['Sas\Social\Models\Notification']
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function($model) {
            Event::fire('notification::assigned', [$model]);
        });

        static::saving(function($model) {
            $model->updateTimestamps();
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('read_at', null);
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function getIsUnreadAttribute()
    {
        return $this->read_at == null;
    }

    public function setRead()
    {
        $this->read_at = Carbon::now();
        $this->save();

        return $this;
    }

    public function setUnread()
    {
        $this->read_at = null;
        $this->save();

        return $this;
    }

    public function render()
    {
        $class = $this->notification->type;

        return (new $class)->render($this->notification);
    }

}