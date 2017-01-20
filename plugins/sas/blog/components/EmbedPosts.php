<?php namespace Sas\Blog\Components;

use Sas\Blog\Components\Posts as ComponentBase;
use Sas\Blog\Models\Post as BlogPost;
use Exception;
use Str;
use Config;

class EmbedPosts extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'sas.blog::lang.embedposts.posts_name',
            'description' => 'sas.blog::lang.embedposts.posts_self_desc'
        ];
    }

    public function defineProperties()
    {
        return array_merge( [
                'sasEmbedCode' => [
                    'title'       => 'sas.blog::lang.embedposts.embed_title',
                    'description' => 'sas.blog::lang.embedposts.embed_desc',
                    'type'        => 'string',
                ]
            ], parent::defineProperties()
        );
    }

    public function init()
    {
        $code = $this->property('sasEmbedCode');

        if (!$code) {
            throw new Exception('No code specified for the Blog Embed component');
        }

        // redefine variable
        $className = Str::normalizeClassName(parent::class);
        $this->dirName = strtolower(str_replace('\\', '/', $className));
        $this->assetPath = Config::get('cms.pluginsPath', '/plugins').dirname(dirname($this->dirName));
    }

    protected function listPosts()
    {
        $category = $this->category ? $this->category->id : null;

        $code = $this->property('sasEmbedCode');

        /*
         * List all the posts, eager load their categories
         */
        $posts = BlogPost::with('categories')
            ->whereSasEmbedCode($code)
            ->listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'sort'       => $this->property('sortOrder'),
            'perPage'    => $this->property('postsPerPage'),
            'search'     => trim(input('search')),
            'category'   => $category,
            'exceptPost' => $this->property('exceptPost'),
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post) {
            $post->setUrl($this->postPage, $this->controller);

            $post->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        return $posts;
    }
}
