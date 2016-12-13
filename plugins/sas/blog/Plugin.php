<?php namespace Sas\Blog;

use Backend;
use Backend\Classes\BackendController;
use Controller;
use Sas\Blog\Models\Post;
use System\Classes\PluginBase;
use Sas\Blog\Classes\TagProcessor;
use Sas\Blog\Models\Category;
use Sas\Blog\Controllers\Posts as PostsController;
use Event;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'sas.blog::lang.plugin.name',
            'description' => 'sas.blog::lang.plugin.description',
            'author'      => '',
            'icon'        => 'icon-pencil',
            'homepage'    => 'https://github.com/sas/blog-plugin'
        ];
    }

    public function registerComponents()
    {
        return [
            'Sas\Blog\Components\Post'       => 'blogPost',
            'Sas\Blog\Components\Posts'      => 'blogPosts',
            'Sas\Blog\Components\Categories' => 'blogCategories',
            'Sas\Blog\Components\RssFeed'    => 'blogRssFeed',
            \Sas\Blog\Components\CustomForm::class    => 'sasBlogCustomForm',
            \Sas\Blog\Components\EmbedPost::class    => 'sasBlogEmbedPost',
            \Sas\Blog\Components\EmbedPosts::class    => 'sasBlogEmbedPosts',
            \Sas\Blog\Components\PostToc::class    => 'sasBlogPostToc'
        ];
    }

    public function registerPermissions()
    {
        return [
            'sas.blog.access_posts' => [
                'tab'   => 'sas.blog::lang.blog.tab',
                'label' => 'sas.blog::lang.blog.access_posts'
            ],
            'sas.blog.access_categories' => [
                'tab'   => 'sas.blog::lang.blog.tab',
                'label' => 'sas.blog::lang.blog.access_categories'
            ],
            'sas.blog.access_other_posts' => [
                'tab'   => 'sas.blog::lang.blog.tab',
                'label' => 'sas.blog::lang.blog.access_other_posts'
            ],
            'sas.blog.access_import_export' => [
                'tab'   => 'sas.blog::lang.blog.tab',
                'label' => 'sas.blog::lang.blog.access_import_export'
            ],
            'sas.blog.access_publish' => [
                'tab'   => 'sas.blog::lang.blog.tab',
                'label' => 'sas.blog::lang.blog.access_publish'
            ]
        ];
    }

    public function registerNavigation()
    {
        return [
            'blog' => [
                'label'       => 'sas.blog::lang.blog.menu_label',
                'url'         => Backend::url('sas/blog/posts'),
                'icon'        => 'icon-pencil',
                'iconSvg'     => 'plugins/sas/blog/assets/images/blog-icon.svg',
                'permissions' => ['sas.blog.*'],
                'order'       => 30,

                'sideMenu' => [
                    'new_post' => [
                        'label'       => 'sas.blog::lang.posts.new_post',
                        'icon'        => 'icon-plus',
                        'url'         => Backend::url('sas/blog/posts/create'),
                        'permissions' => ['sas.blog.access_posts']
                    ],
                    'posts' => [
                        'label'       => 'sas.blog::lang.blog.posts',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('sas/blog/posts'),
                        'permissions' => ['sas.blog.access_posts']
                    ],
                    'categories' => [
                        'label'       => 'sas.blog::lang.blog.categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('sas/blog/categories'),
                        'permissions' => ['sas.blog.access_categories']
                    ]
                ]
            ]
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'Sas\Blog\FormWidgets\Preview' => [
                'label' => 'Preview',
                'code'  => 'preview'
            ],

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

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        /*
         * Register the image tag processing callback
         */
        TagProcessor::instance()->registerCallback(function($input, $preview) {
            if (!$preview) {
                return $input;
            }

            return preg_replace('|\<img src="image" alt="([0-9]+)"([^>]*)\/>|m',
                '<span class="image-placeholder" data-index="$1">
                    <span class="upload-dropzone">
                        <span class="label">Click or drop an image...</span>
                        <span class="indicator"></span>
                    </span>
                </span>',
            $input);
        });

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

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

        // Local event hook that affects all users
        Post::extend(function($model) {
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

        /*
         * Register menu items for the RainLab.Pages plugin
         */
        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'blog-category'       => 'sas.blog::lang.menuitem.blog_category',
                'all-blog-categories' => 'sas.blog::lang.menuitem.all_blog_categories',
                'blog-post'           => 'sas.blog::lang.menuitem.blog_post',
                'all-blog-posts'      => 'sas.blog::lang.menuitem.all_blog_posts',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'blog-category' || $type == 'all-blog-categories') {
                return Category::getMenuTypeInfo($type);
            }
            elseif ($type == 'blog-post' || $type == 'all-blog-posts') {
                return Post::getMenuTypeInfo($type);
            }
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'blog-category' || $type == 'all-blog-categories') {
                return Category::resolveMenuItem($item, $url, $theme);
            }
            elseif ($type == 'blog-post' || $type == 'all-blog-posts') {
                return Post::resolveMenuItem($item, $url, $theme);
            }
        });
    }
}
