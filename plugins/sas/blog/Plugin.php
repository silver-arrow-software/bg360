<?php namespace Sas\Blog;

use Backend;
use System\Classes\PluginBase;
use RainLab\Blog\Controllers\Posts as PostsController;
use RainLab\Blog\Models\Post as PostModel;
use Log;
use App;
use Illuminate\Foundation\AliasLoader;

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
            'author'      => 'SanBT',
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

            $form->addFields([
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
            \Sas\Blog\Components\CustomForm::class    => 'sasBlogCustomForm',
            \Sas\Blog\Components\EmbedPost::class    => 'sasBlogEmbedPost',
            \Sas\Blog\Components\EmbedPosts::class    => 'sasBlogEmbedPosts'
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

    public function registerFormWidgets()
    {
        return [
            'Backend\FormWidgets\CodeEditor' => [
                'label' => 'Code editor',
                'code'  => 'codeeditor'
            ],

            'Backend\FormWidgets\RichEditor' => [
                'label' => 'Rich editor',
                'code'  => 'richeditor'
            ],

            'Backend\FormWidgets\MarkdownEditor' => [
                'label' => 'Markdown editor',
                'code'  => 'markdown'
            ],

            'Backend\FormWidgets\FileUpload' => [
                'label' => 'File uploader',
                'code'  => 'fileupload'
            ],

            'Backend\FormWidgets\Relation' => [
                'label' => 'Relationship',
                'code'  => 'relation'
            ],

            'Backend\FormWidgets\DatePicker' => [
                'label' => 'Date picker',
                'code'  => 'datepicker'
            ],

            'Backend\FormWidgets\TimePicker' => [
                'label' => 'Time picker',
                'code'  => 'timepicker'
            ],

            'Backend\FormWidgets\ColorPicker' => [
                'label' => 'Color picker',
                'code'  => 'colorpicker'
            ],

            'Backend\FormWidgets\DataTable' => [
                'label' => 'Data Table',
                'code'  => 'datatable'
            ],

            'Backend\FormWidgets\RecordFinder' => [
                'label' => 'Record Finder',
                'code'  => 'recordfinder'
            ],

            'Backend\FormWidgets\Repeater' => [
                'label' => 'Repeater',
                'code'  => 'repeater'
            ],

            'Backend\FormWidgets\TagList' => [
                'label' => 'Tag List',
                'code'  => 'taglist'
            ]
        ];
    }
}