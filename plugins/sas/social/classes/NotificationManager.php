<?php namespace Sas\Social\Classes;

use October\Rain\Support\Collection;
use October\Rain\Support\Singleton;
use System\Classes\PluginManager;

class NotificationManager extends Singleton
{

    /**
     * @var array Cache of registration callbacks.
     */
    private $callbacks = [];

    /**
     * @var array List of registered notifications.
     */
    private $notifications;

    /**
     * @var \System\Classes\PluginManager
     */
    protected $pluginManager;

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->pluginManager = PluginManager::instance();
    }

    /**
     * Loads the notifications from modules and plugins
     *
     * @return void
     */
    protected function loadNotifications()
    {
        /*
         * Load module items
         */
        foreach ($this->callbacks as $callback) {
            $callback($this);
        }

        /*
         * Load plugin items
         */
        $plugins = $this->pluginManager->getPlugins();

        foreach ($plugins as $id => $plugin) {
            if (!method_exists($plugin, 'registerNotifications')) {
                continue;
            }

            $notifications = $plugin->registerNotifications();
            if (!is_array($notifications)) {
                continue;
            }

            $this->registerNotifications($id, $notifications);
        }
    }

    /**
     * Registers a callback function that defines a notifications.
     * The callback function should register notifications by calling the manager's
     * registerNotifications() function. The manager instance is passed to the
     * callback function as an argument. Usage:
     * <pre>
     *   NotificationManager::registerCallback(function($manager){
     *       $manager->registerNotifications([...]);
     *   });
     * </pre>
     * @param callable $callback A callable function.
     */
    public function registerCallback(callable $callback)
    {
        $this->callbacks[] = $callback;
    }

    public function registerNotifications($owner, array $classes)
    {
        if (!$this->notifications) {
            $this->notifications = [];
        }

        foreach ($classes as $class => $alias) {
            $notification = (object)[
                'owner' => $owner,
                'class' => $class,
                'alias' => $alias,
            ];

            $this->notifications[$alias] = $notification;
        }
    }

    /**
     * Returns a list of the notification type classes.
     *
     * @param boolean $asObject As a collection with extended information found in the class object.
     * @return array
     */
    public function listNotifications($asObject = true)
    {
        if ($this->notifications === null) {
            $this->loadNotifications();
        }

        if (!$asObject) {
            return $this->notifications;
        }

        /*
         * Enrich the collection with notification objects
         */
        $collection = [];
        foreach ($this->notifications as $notification) {
            if (!class_exists($notification->class)) {
                continue;
            }

            $notificationObj = new $notification->class;
            $collection[$notification->alias] = (object)[
                'owner'       => $notification->owner,
                'class'       => $notification->class,
                'alias'       => $notification->alias,
                'object'      => $notificationObj,
            ];
        }

        return new Collection($collection);
    }

    /**
     * Returns a notification based on its unique alias.
     */
    public function findByAlias($alias)
    {
        $notifications = $this->listNotifications();
        if (!isset($notifications[$alias])) {
            return false;
        }

        return $notifications[$alias];
    }

}