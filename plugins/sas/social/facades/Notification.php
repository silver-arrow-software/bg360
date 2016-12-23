<?php namespace Sas\Social\Facades;

use October\Rain\Support\Facade;

class Notification extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sas.social';
    }

    protected static function getFacadeInstance()
    {
        return new \Sas\Social\Classes\Notification;
    }
}