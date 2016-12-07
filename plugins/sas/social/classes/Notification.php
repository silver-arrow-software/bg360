<?php namespace Sas\Social\Classes;

use Model;
use Sas\Social\Models\NotificationUser;
use Exception;

class Notification
{

    public function getClass($type) {
        if (empty($type)) {
            throw new Exception('No notification type given');
        }

        $notification = NotificationManager::instance()->findByAlias($type);
        return $notification->class;
    }

    /**
     * Creates a notification and assigns it to some users
     *
     * @param string $type  The notification type
     * @param \Model $sender The object that initiated the notification (a user, a group, a web service etc.)
     * @param \Model|null $object An object that was changed (a post that has been liked).
     * @param mixed $users The user(s) which should receive this notification.
     *
     * @return \Sas\Social\Models\Notification
     */
    public function create($type, Model $sender, Model $object = null, $users = [])
    {
        $class = $this->getClass($type);
        $notification = new $class();
        $notification->type = $type;

        $notification->sender()->associate($sender);
        if ($object) {
            $notification->object()->associate($object);
        }

        $notification->save();

        $notification_users = [];

        if ($users instanceof Model) {
            $notification_user = new NotificationUser;
            $notification_user->user_id = $users->id;
            $notification_user->notification_id = $notification->id;
            $notification_users[] = $notification_user;
        }
        else {
            foreach ($users as $user) {
                $notification_user = new NotificationUser;
                $notification_user->user_id = $user->id;
                $notification_user->notification_id = $notification->id;
                $notification_users[] = $notification_user;
            }
        }

        $notification->users()->saveMany($notification_users);

        return $notification;
    }

}