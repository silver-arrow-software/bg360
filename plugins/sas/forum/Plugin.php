<?php namespace Sas\Forum;

use Event;
use Backend;
use RainLab\User\Models\User;
use Sas\Forum\Models\Member;
use System\Classes\PluginBase;
use RainLab\User\Controllers\Users as UsersController;

/**
 * Forum Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'sas.forum::lang.plugin.name',
            'description' => 'sas.forum::lang.plugin.description',
            'author'      => 'Silver Arrow Software',
            'icon'        => 'icon-comments',
            'homepage'    => 'https://github.com/silver-arrow-software'
        ];
    }

    public function boot()
    {
        User::extend(function($model) {
            $model->hasOne['forum_member'] = ['Sas\Forum\Models\Member'];

            $model->bindEvent('model.beforeDelete', function() use ($model) {
                $model->forum_member && $model->forum_member->delete();
            });
        });

        UsersController::extendFormFields(function($widget, $model, $context) {
            // Prevent extending of related form instead of the intended User form
            if (!$widget->model instanceof \RainLab\User\Models\User) {
                return;
            }
            if ($context != 'update') {
                return;
            }
            if (!Member::getFromUser($model)) {
                return;
            }

            $widget->addFields([
                'forum_member[username]' => [
                    'label'   => 'sas.forum::lang.settings.username',
                    'tab'     => 'Forum',
                    'comment' => 'sas.forum::lang.settings.username_comment'
                ],
                'forum_member[is_moderator]' => [
                    'label'   => 'sas.forum::lang.settings.moderator',
                    'type'    => 'checkbox',
                    'tab'     => 'Forum',
                    'span'    => 'auto',
                    'comment' => 'sas.forum::lang.settings.moderator_comment'
                ],
                'forum_member[is_banned]' => [
                    'label'   => 'sas.forum::lang.settings.banned',
                    'type'    => 'checkbox',
                    'tab'     => 'Forum',
                    'span'    => 'auto',
                    'comment' => 'sas.forum::lang.settings.banned_comment'
                ]
            ], 'primary');
        });

        UsersController::extendListColumns(function($widget, $model) {
            if (!$model instanceof \RainLab\User\Models\User) {
                return;
            }

            $widget->addColumns([
                'forum_member_username' => [
                    'label'      => 'sas.forum::lang.settings.forum_username',
                    'relation'   => 'forum_member',
                    'select'     => 'username',
                    'searchable' => false
                ]
            ]);
        });
    }

    public function registerComponents()
    {
        return [
           '\Sas\Forum\Components\Channels'     => 'forumChannels',
           '\Sas\Forum\Components\Channel'      => 'forumChannel',
           '\Sas\Forum\Components\Topic'        => 'forumTopic',
           '\Sas\Forum\Components\Topics'       => 'forumTopics',
           '\Sas\Forum\Components\Member'       => 'forumMember',
           '\Sas\Forum\Components\EmbedTopic'   => 'forumEmbedTopic',
           '\Sas\Forum\Components\EmbedChannel' => 'forumEmbedChannel'
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'sas.forum::lang.settings.channels',
                'description' => 'sas.forum::lang.settings.channels_desc',
                'icon'        => 'icon-comments',
                'url'         => Backend::url('sas/forum/channels'),
                'category'    => 'SAS',
                'order'       => 500
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'sas.forum::mail.topic_reply'   => 'Notification to followers when a post is made to a topic.',
            'sas.forum::mail.member_report' => 'Notification to moderators when a member is reported to be a spammer.'
        ];
    }
}
