<?php

namespace Sas\Social\Behaviors;

use System\Classes\ModelBehavior;
use Sas\Social\Models\Thread;
use Sas\Social\Models\Participant;

class MessageModel extends ModelBehavior
{
    /**
     * Constructor
     */
    public function __construct($model)
    {
        parent::__construct($model);

        $model->hasMany['messages'] = ['Sas\Social\Models\Message'];
        $model->hasMany['participants'] = ['Sas\Social\Models\Participant'];
        $model->belongsToMany['threads'] = [
            'Sas\Social\Models\Thread',
            'table' => 'sas_social_messages_participants',
            'order' => 'updated_at desc'
        ];
    }

    /**
     * Returns the new messages count for user.
     *
     * @return int
     */
    public function newMessagesCount()
    {
        return count($this->threadsWithNewMessages());
    }

    /**
     * Returns all threads with new messages.
     *
     * @return array
     */
    public function threadsWithNewMessages()
    {
        $threadsWithNewMessages = [];

        $participants = Participant::where('user_id', $this->model->id)->lists('last_read', 'thread_id');

        if ($participants) {
            $threads = Thread::whereIn('id', array_keys($participants))->get();
            foreach ($threads as $thread) {
                if ($thread->updated_at > $participants[$thread->id]) {
                    $threadsWithNewMessages[] = $thread->id;
                }
            }
        }

        return $threadsWithNewMessages;
    }
}
