<?php namespace Sas\Social\Notifications;

use Sas\Social\Models\Notification;

class CommentPosted extends Notification
{
    public static $type = 'comment_posted';

    public function render($notification)
    {
        return "{$notification->sender->name} commented {$notification->object->content}";
    }
}
