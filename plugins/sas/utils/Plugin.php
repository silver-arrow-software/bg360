<?php namespace Sas\Utils;

use System\Classes\PluginBase;

/**
 * sasUtils Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Utilities',
            'description' => 'Some useful tools for other plugins.',
            'author'      => 'Silver Arrow Software',
            'icon'        => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
           'Sas\Utils\Components\FileUploader'  => 'fileUploader',
           'Sas\Utils\Components\ImageUploader' => 'imageUploader',
        ];
    }
}