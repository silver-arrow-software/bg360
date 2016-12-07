<?php namespace Sas\Social;

use System\Classes\PluginBase;
use RainLab\User\Models\User;
use Illuminate\Foundation\AliasLoader;

/**
 * Social Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['RainLab.User', 'Sas.Utils'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails() {
        return [
            'name'        => 'Social plugin',
            'description' => 'Social network system.',
            'author'      => 'Silver Arrow Software',
            'icon'        => 'icon-child'
        ];
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot() {
        $this->extendUserModel();
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register() {
        $alias = AliasLoader::getInstance();
        $alias->alias('Uuid', 'Webpatser\Uuid\Uuid');
        $alias->alias('Notification', 'Sas\Social\Facades\Notification');
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents() {
        return [
            'Sas\Social\Components\MailNotifications' => 'messageNotifications',
            'Sas\Social\Components\Threads'       => 'userThreads',
            'Sas\Social\Components\Messages'      => 'userMessages',
            'Sas\Social\Components\Notifications' => 'notifications',
            'Sas\Social\Components\Profile'  => 'socialProfile',
            'Sas\Social\Components\Profiles' => 'socialProfiles',
            'Sas\Social\Components\ActivityStream' => 'socialActivityStream',
            'Sas\Social\Components\WallStream' => 'socialWallStream',
        ];
    }

    public function registerNotifications() {
        return [
            'Sas\Social\Notifications\CommentPosted' => 'comment_posted',
            'Sas\Social\Notifications\PostLiked' => 'post_liked'
        ];
    }

    protected function extendUserModel() {
        User::extend(function($model) {
            $model->hasMany['notifications'] = ['Sas\Social\Models\NotificationUser'];
            $model->implement[] = 'Sas\Social\Behaviors\MessageModel';
            $model->implement[] = 'Sas\Social\Behaviors\SocialModel';
        });
    }
}
