<?php namespace Sas\Social\Components;

use Auth;
use Redirect;
use Validator;
use Carbon\Carbon;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\User\Models\User as UserModel;
use Sas\Social\Models\Message;
use Sas\Social\Models\Participant;
use Sas\Social\Models\Thread;
use ApplicationException;
use ValidationException;

/**
 * Notifications component
 */
class MailNotifications extends ComponentBase
{

    /**
     * Reference to the page name for linking to messages.
     *
     * @var string
     */
    public $messagesPage;

    /**
     * Returns information about this component, including name and description.
     */
    public function componentDetails()
    {
        return [
            'name'        => 'Mail Notifications',
            'description' => 'Display a mail notifications widget.'
        ];
    }

    /**
     * Defines the properties used by this class.
     */
    public function defineProperties()
    {
        return [
            'messagesPage' => [
                'title'       => 'Messages page',
                'description' => 'Name of the messages page file for the messages link.',
                'type'        => 'dropdown'
            ],
        ];
    }

    public function getMessagesPageOptions()
    {
        return Page::withComponent('messages')->sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function init()
    {
        $this->page['users'] = UserModel::where('id', '!=', Auth::getUser()->id)->get();
    }

    /**
     * Executed when this component is bound to a page or layout, part of
     * the page life cycle.
     */
    public function onRun()
    {
        $this->messagesPage = $this->page['messagesPage'] = $this->property('messagesPage');
    }

    /**
     * Creates a new Message
     */
    public function onAddMessage()
    {
        if (!$user = Auth::getUser()) {
            throw new ApplicationException('You should be logged in.');
        }

        $data = input();

        $rules = [
            'recipients' => 'required',
            'subject'    => 'required',
            'body'       => 'required'
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $thread = Thread::create([
            'subject' => input('subject')
        ]);

        Message::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => input('body')
        ]);

        Participant::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'last_read' => Carbon::now()
        ]);

        $thread->addParticipants(input('recipients'));

        return Redirect::back();
    }

    public function onNewMessagesCount()
    {
        if (!$user = Auth::getUser()) {
            throw new ApplicationException('You should be logged in.');
        }

        return [
            'count' => $user->newMessagesCount()
        ];
    }

    public function onRecent()
    {
        if (!$user = Auth::getUser()) {
            throw new ApplicationException('You should be logged in.');
        }

        $userMessages = Participant::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return [
            '#dropdown-messages' => $this->renderPartial('@recent', [
                'userMessages' => $userMessages,
                'messagesPage' => $this->property('messagesPage')
            ])
        ];
    }

}