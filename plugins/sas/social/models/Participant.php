<?php

namespace Sas\Social\Models;

use Model;

/**
 * Participant model
 */
class Participant extends Model
{
    use \October\Rain\Database\Traits\SoftDelete;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'sas_social_messages_participants';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['last_read'];

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['thread_id', 'user_id', 'last_read'];

    /**
     * Relations
     *
     * @var array
     */
    public $belongsTo = [
        'thread' => ['Sas\Social\Models\Thread'],
        'user'   => ['RainLab\User\Models\User']
    ];
}
