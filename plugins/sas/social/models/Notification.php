<?php namespace Sas\Social\Models;

use Model;
use Event;

/**
 * Notification model
 */
class Notification extends BaseModel
{
    /**
     * @var string The database table used by the model.
     */
    protected $table = 'sas_social_notifications';

    /**
     * @var array Hidden fields from array/json access
     */
    protected $hidden = ['sender_id', 'sender_type', 'object_id', 'object_type'];

    /**
     * @var array Relations
     */
    public $belongsToMany = [
        'users' => [
            'RainLab\User\Models\User',
            'table' => 'sas_social_notifications_users',
            'pivot' => ['read_at'],
            'pivotModel' => 'Sas\Social\Models\NotificationUser'
        ]
    ];

    public $morphTo = [
        'sender' => [],
        'object' => []
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function($model) {
            Event::fire('notification::created', [$model]);
        });
    }

}
