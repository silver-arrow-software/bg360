<?php namespace Sas\Social\Components;

use Auth;
use Cms\Classes\ComponentBase;
use Sas\Social\Models\NotificationUser;
use ApplicationException;

/**
 * Notifications component
 */
class Notifications extends ComponentBase
{

    /**
     * Returns information about this component, including name and description.
     */
    public function componentDetails()
    {
        return [
            'name'        => 'Notifications',
            'description' => 'Shows an stream of notifications for an user at the top menu.'
        ];
    }

    public function onGetNotifications()
    {
        if (!$user = Auth::getUser()) {
            throw new ApplicationException('You should be logged in.');
        }

        return [
            '#notifications-content' => $this->renderPartial('@result', [
                'notifications' => $user->notifications
            ])
        ];
    }

    public function onMarkAsRead()
    {
        if (!$user = Auth::getUser()) {
            throw new ApplicationException('You should be logged in.');
        }

        $notifications = NotificationUser::where('user_id', $user->id)->get();

        foreach($notifications as $notification) {
            $notification->setRead();
        }
    }

}