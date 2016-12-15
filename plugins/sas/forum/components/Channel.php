<?php namespace Sas\Forum\Components;

use Auth;
use Request;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Sas\Forum\Models\Topic as TopicModel;
use Sas\Forum\Models\Channel as ChannelModel;
use Sas\Forum\Models\Member as MemberModel;
use Sas\Forum\Classes\TopicTracker;

/**
 * Channel component
 *
 * Displays a list of posts belonging to a channel.
 */
class Channel extends ComponentBase
{
    /**
     * @var boolean Determine if this component is being used by the EmbedChannel component.
     */
    public $embedMode = false;

    /**
     * @var string If this channel is embedded, pass the topic slug to this route parameter for linking to topics.
     */
    public $embedTopicParam = 'topicSlug';

    /**
     * @var Sas\Forum\Models\Member Member cache
     */
    protected $member = null;

    /**
     * @var Sas\Forum\Models\Channel Channel cache
     */
    protected $channel = null;

    /**
     * @var string Reference to the page name for linking to members.
     */
    public $memberPage;

    /**
     * @var string Reference to the page name for linking to topics.
     */
    public $topicPage;

    /**
     * @var Collection Topics cache for Twig access.
     */
    public $topics = null;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.forum::lang.channel.component_name',
            'description' => 'sas.forum::lang.channel.component_description',
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'sas.forum::lang.slug.name',
                'description' => 'sas.forum::lang.slug.desc',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ],
            'memberPage' => [
                'title'       => 'sas.forum::lang.member.page_name',
                'description' => 'sas.forum::lang.member.page_help',
                'type'        => 'dropdown',
                'group'       => 'Links',
            ],
            'topicPage' => [
                'title'       => 'sas.forum::lang.topic.page_name',
                'description' => 'sas.forum::lang.topic.page_help',
                'type'        => 'dropdown',
                'group'       => 'Links',
            ],
        ];
    }

    public function getPropertyOptions($property)
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->addCss('assets/css/forum.css');

        $this->prepareVars();
        $this->page['channel'] = $this->getChannel();

        return $this->prepareTopicList();
    }

    protected function prepareVars()
    {
        /*
         * Page links
         */
        $this->topicPage = $this->page['topicPage'] = $this->property('topicPage');
        $this->memberPage = $this->page['memberPage'] = $this->property('memberPage');
    }

    public function getChannel()
    {
        if ($this->channel !== null) {
            return $this->channel;
        }

        if (!$slug = $this->property('slug')) {
            return null;
        }

        return $this->channel = ChannelModel::whereSlug($slug)->first();
    }

    protected function prepareTopicList()
    {
        /*
         * If channel exists, load the topics
         */
        if ($channel = $this->getChannel()) {
            $currentPage = input('page');
            $searchString = trim(input('search'));
            $topics = TopicModel::with('last_post_member')->listFrontEnd([
                'page'     => $currentPage,
                'sort'     => 'updated_at',
                'channels' => $channel->id,
                'search'   => $searchString,
            ]);

            /*
             * Add a "url" helper attribute for linking to each topic
             */
            $topics->each(function($topic) {
                if ($this->embedMode) {
                    $topic->url = $this->pageUrl($this->topicPage, [$this->embedTopicParam => $topic->slug]);
                }
                else {
                    $topic->setUrl($this->topicPage, $this->controller);
                }

                if ($topic->last_post_member) {
                    $topic->last_post_member->setUrl($this->memberPage, $this->controller);
                }

                if ($topic->start_member) {
                    $topic->start_member->setUrl($this->memberPage, $this->controller);
                }
            });

            /*
             * Signed in member
             */
            $this->page['member'] = $this->member = MemberModel::getFromUser();

            if ($this->member) {
                $this->member->setUrl($this->memberPage, $this->controller);
                $topics = TopicTracker::instance()->setFlagsOnTopics($topics, $this->member);
            }

            $this->page['topics'] = $this->topics = $topics;

            /*
             * Pagination
             */
            if ($topics) {
                $queryArr = [];
                if ($searchString) {
                    $queryArr['search'] = $searchString;
                }
                $queryArr['page'] = '';
                $paginationUrl = Request::url() . '?' . http_build_query($queryArr);

                if ($currentPage > ($lastPage = $topics->lastPage()) && $currentPage > 1) {
                    return Redirect::to($paginationUrl . $lastPage);
                }

                $this->page['paginationUrl'] = $paginationUrl;
            }
        }

        $this->page['isGuest'] = !Auth::check();
    }
}