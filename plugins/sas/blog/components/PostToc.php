<?php namespace Sas\Blog\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Sas\Blog\Models\Post as BlogPost;

class PostToc extends ComponentBase
{
    public $toc;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.blog::lang.settings.post_toc_title',
            'description' => 'sas.blog::lang.settings.post_toc_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'sas.blog::lang.settings.post_slug',
                'description' => 'sas.blog::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->toc = $this->page['toc'] = $this->loadToc();
    }

    protected function loadToc()
    {
        $slug = $this->property('slug');

        $post = new BlogPost;

        $post = $post->isClassExtendedWith('RainLab.Translate.Behaviors.TranslatableModel')
            ? $post->transWhere('slug', $slug)
            : $post->where('slug', $slug);

        $toc_html = '';

        if ( $post = $post->isPublished()->first() ) {
            $content_html = $post ? $post->content_html : '';
            $toc = $post->html_toc($content_html);

            if($toc){
                $prev_level = 0;
                foreach ($toc as $_toc){
                    $a = '<a href="#'.$_toc['anchor'].'" >'.$_toc['text'].'</a>';
                    if($prev_level > $_toc['level']){
                        $toc_html.= str_repeat('</li></ul>', $prev_level - $_toc['level']);
                        $toc_html.= '</li><li>';
                    } else if($prev_level < $_toc['level']){
                        $toc_html.= '<ul><li>';
                    } else { // equal
                        $toc_html.= '</li><li>';
                    }

                    $toc_html.= $a;
                    $prev_level = $_toc['level'];
                }
                $toc_html.= str_repeat('</li></ul>', $prev_level - 1);
            }
        }

        return $toc_html;
    }

}
