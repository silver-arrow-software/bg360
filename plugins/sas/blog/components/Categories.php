<?php namespace Sas\Blog\Components;

use Db;
use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Sas\Blog\Models\Category as BlogCategory;

class Categories extends ComponentBase
{
    /**
     * @var Collection A collection of categories to display
     */
    public $categories;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    /**
     * @var string Reference to the current category slug.
     */
    public $currentCategorySlug;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.blog::lang.settings.category_title',
            'description' => 'sas.blog::lang.settings.category_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'sas.blog::lang.settings.category_slug',
                'description' => 'sas.blog::lang.settings.category_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'displayEmpty' => [
                'title'       => 'sas.blog::lang.settings.category_display_empty',
                'description' => 'sas.blog::lang.settings.category_display_empty_description',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'categoryPage' => [
                'title'       => 'sas.blog::lang.settings.category_page',
                'description' => 'sas.blog::lang.settings.category_page_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
                'group'       => 'Links',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->currentCategorySlug = $this->page['currentCategorySlug'] = $this->property('slug');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->categories = $this->page['categories'] = $this->loadCategories();
    }

    protected function loadCategories()
    {
        $categories = BlogCategory::orderBy('name');
        if (!$this->property('displayEmpty')) {
            $categories->whereExists(function($query) {
                $prefix = Db::getTablePrefix();

                $query
                    ->select(Db::raw(1))
                    ->from('sas_blog_posts_categories')
                    ->join('sas_blog_posts', 'sas_blog_posts.id', '=', 'sas_blog_posts_categories.post_id')
                    ->whereNotNull('sas_blog_posts.published')
                    ->where('sas_blog_posts.published', '=', 1)
                    ->whereRaw($prefix.'sas_blog_categories.id = '.$prefix.'sas_blog_posts_categories.category_id')
                ;
            });
        }

        $categories = $categories->getNested();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        return $this->linkCategories($categories);
    }

    protected function linkCategories($categories)
    {
        return $categories->each(function($category) {
            $category->setUrl($this->categoryPage, $this->controller);

            if ($category->children) {
                $this->linkCategories($category->children);
            }
        });
    }
}
