<?php namespace Sas\Blog\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Sas\Blog\Models\Post as PostModel;
use Exception;

class EmbedPost extends ComponentBase
{
    /**
     * @var boolean Determine if this component is being used by the EmbedChannel component.
     */
    public $embedMode = true;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.blog::lang.slugpost.post_name',
            'description' => 'sas.blog::lang.slugpost.post_self_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'slugCode' => [
                'title'       => 'sas.blog::lang.slugpost.slug_title',
                'description' => 'sas.blog::lang.slugpost.slug_desc',
                'type'        => 'string',
            ],
            'memberPage' => [
                'title'       => 'sas.blog::lang.member.page_name',
                'description' => 'sas.blog::lang.member.page_help',
                'type'        => 'dropdown',
                'group'       => 'Links',
            ]
        ];
    }

    public function getMemberPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function init()
    {
        $code = $this->property('slugCode');

        if (!$code) {
            throw new Exception('No code specified for the Blog Embed component');
        }

        $properties = $this->getProperties();

        /*
         * Proxy as post
         */
        if ($post = PostModel::whereSlug($code)->first()) {
            $properties['slug'] = $post->slug;
        }

        $component = $this->addComponent(\Sas\Blog\Components\Post::class, $this->alias, $properties);

        /*
         * If a post does not already exist, generate it when the page ends.
         * This can be disabled by the page setting embedMode to FALSE, for example,
         * if the page returns 404 a post should not be generated.
         */
        if (!$post) {
            $this->controller->bindEvent('page.end', function() use ($component, $code) {
                if ($component->embedMode !== false) {
//                    $post = PostModel::createForEmbed($code, $this->page->title);
//                    $component->setProperty('slug', $post->slug);
//                    $component->onRun();
                }
            });
        }

        /*
         * Set the embedding mode: Single post
         */
        $component->embedMode = 'single';
    }
}
