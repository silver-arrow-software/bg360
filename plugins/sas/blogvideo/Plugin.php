<?php namespace Sas\BlogVideo;

use Backend;
use Controller;
use Backend\Classes\BackendController;
use System\Classes\PluginBase;
use Sas\Blog\Controllers\Posts as PostsController;
use Sas\Blog\Classes\TagProcessor;

class Plugin extends PluginBase
{
    public $require = ['Sas.Blog'];

    public function pluginDetails()
    {
        return [
            'name'        => 'Blog Video Extension',
            'description' => 'Adds responsive video embedding features to the RainLab Blog module.',
            'author'      => 'Silver Arrow Software',
            'icon'        => 'icon-video-camera',
            'homepage'    => 'https://github.com/sas/blogvideo-plugin'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        PostsController::extend(function($controller) {
            if (!in_array(BackendController::$action, ['create', 'update'])) {
                return;
            }

            $controller->addJs('/plugins/sas/blogvideo/assets/js/blog-video.js');
            $controller->addCss('/plugins/sas/blogvideo/assets/css/blog-video.css');
        });

        /*
         * Register the video tag processing callback
         */
        TagProcessor::instance()->registerCallback(function($input, $preview) {
            if (!$preview) {
                return $input;
            }

            $popup = file_get_contents(__DIR__.'/partials/popup.htm');

            return preg_replace('|\<img src="video" alt="([0-9]+)" \/>|m', 
                '<span class="video-placeholder" data-index="$1">
                    <a href="#">Click to embed a video...</a>
                    '.$popup.'
                </span>',
            $input);
        });
    }
}
