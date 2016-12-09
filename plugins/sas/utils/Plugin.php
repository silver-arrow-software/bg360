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

/**
 * echo debug data
 *
 * @param $value
 * @param $key
 * @param bool $exit
 * @param bool $backtrack
 */
function echoDbg($value, $key = '', $exit = false, $backtrack = false)
{
    echo '<br/>';
    if($key){
        echo '<strong>'.$key.'</strong><br/>';
    }
    echo '<pre>'.print_r($value, true).'</pre><br/>';

    if($backtrack) {
        echo '<pre>';
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 100);
        echo '</pre>';
    }
    if($exit) exit('end debug');
}