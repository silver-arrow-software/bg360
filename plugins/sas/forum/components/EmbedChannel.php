<?php namespace Sas\Forum\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Sas\Forum\Models\Topic as TopicModel;
use Sas\Forum\Models\Channel as ChannelModel;
use Exception;

class EmbedChannel extends ComponentBase
{
    /**
     * @var boolean Determine if this component is being used by the EmbedChannel component.
     */
    public $embedMode = true;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.forum::lang.embedch.channel_name',
            'description' => 'sas.forum::lang.embedch.channel_self_desc'
        ];
    }

    public function defineProperties()
    {
        return [
            'embedCode' => [
                'title'       => 'sas.forum::lang.embedch.embed_title',
                'description' => 'sas.forum::lang.embedch.embed_desc',
                'type'        => 'string',
                'group'       => 'Parameters',
            ],
            'channelSlug' => [
                'title'       => 'sas.forum::lang.embedch.channel_title',
                'description' => 'sas.forum::lang.embedch.channel_desc',
                'type'        => 'dropdown'
            ],
            'topicSlug' => [
                'title'       => 'sas.forum::lang.embedch.topic_name',
                'description' => 'sas.forum::lang.embedch.topic_desc',
                'type'        => 'string',
                'default'     => '{{ :topicSlug }}',
                'group'       => 'Parameters',
            ],
            'memberPage' => [
                'title'       => 'sas.forum::lang.member.page_name',
                'description' => 'sas.forum::lang.member.page_help',
                'type'        => 'dropdown',
                'group'       => 'Links',
            ],
        ];
    }

    public function getChannelSlugOptions()
    {
        return ChannelModel::listsNested('title', 'slug', ' - ');
    }

    public function getMemberPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function init()
    {
        $code = $this->property('embedCode');

        if (!$code) {
            throw new Exception('No code specified for the Forum Embed component');
        }

        $parentChannel = ($channelSlug = $this->property('channelSlug'))
            ? ChannelModel::whereSlug($channelSlug)->first()
            : null;

        if (!$parentChannel) {
            throw new Exception('No channel specified for Forum Embed component');
        }

        $properties = $this->getProperties();

        /*
         * Proxy as topic
         */
        if (input('channel') || $this->property('topicSlug')) {
            $properties['slug'] = '{{' . $this->propertyName('topicSlug') . '}}';
            $component = $this->addComponent('Sas\Forum\Components\Topic', $this->alias, $properties);
        }
        /*
         * Proxy as channel
         */
        else {
            if ($channel = ChannelModel::forEmbed($parentChannel, $code)->first()) {
                $properties['slug'] = $channel->slug;
            }

            $properties['topicPage'] = $this->page->baseFileName;
            $component = $this->addComponent('Sas\Forum\Components\Channel', $this->alias, $properties);
            $component->embedTopicParam = $this->paramName('topicSlug');

            /*
             * If a channel does not already exist, generate it when the page ends.
             * This can be disabled by the page setting embedMode to FALSE, for example,
             * if the page returns 404 a channel should not be generated.
             */
            if (!$channel) {
                $this->controller->bindEvent('page.end', function() use ($component, $parentChannel, $code) {
                    if ($component->embedMode !== false) {
                        $channel = ChannelModel::createForEmbed($code, $parentChannel, $this->page->title);
                        $component->setProperty('slug', $channel->slug);
                        $component->onRun();
                    }
                });
            }
        }

        /*
         * Set the default embedding mode
         */
        if (input('channel')) {
            $component->embedMode = 'post';
        }
        elseif (input('search')) {
            $component->embedMode = 'search';
        }
        elseif ($this->property('topicSlug')) {
            $component->embedMode = 'topic';
        }
        else {
            $component->embedMode = 'channel';
        }
    }
}
