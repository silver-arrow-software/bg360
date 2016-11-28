<?php namespace Sas\Blog;

use Backend;
use System\Classes\PluginBase;
use RainLab\Blog\Controllers\Posts as PostsController;
use RainLab\Blog\Models\Post as PostModel;
use Log;

/**
 * Blog Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Plugin dependency
     * @var array
     */
    public $require = ['RainLab.Blog'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'sas.blog::lang.plugin.name',
            'description' => 'sas.blog::lang.plugin.description',
            'author'      => 'Sanbt',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

        // Local event hook that affects all users
        PostModel::extend(function($model) {
            $model->bindEvent('model.beforeListFrontEnd', function() {
            });

            $model->addDynamicMethod('scopeHasSasEmbedCode', function($query) {
                return $query->whereNotNull('sas_embed_code');
            });

        });

        PostsController::extendListColumns(function ($list, $model){
            if (!$model instanceof PostModel){
                return;
            }

            $list->addColumns([
                'sas_embed_code' => [
                    'label' => 'Sas Embed Code'
                ]
            ]);
        });

        PostsController::extendFormFields(function ($form, $model, $context){
            if (!$model instanceof PostModel){
                return;
            }

            $form->addTabFields([
                'sas_embed_code' => [
                    'label' => 'Sas Embed Code',
                ]
            ]);
        });

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            \Sas\Blog\Components\EmbedPost::class    => 'blogEmbedPost',
            \Sas\Blog\Components\EmbedPosts::class    => 'blogEmbedPosts'
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'sas.blog.some_permission' => [
                'tab' => 'Blog',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'blog' => [
                'label'       => 'Blog',
                'url'         => Backend::url('sas/blog/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['sas.blog.*'],
                'order'       => 500,
            ],
        ];
    }

}
