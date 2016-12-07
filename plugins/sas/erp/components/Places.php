<?php namespace Sas\Erp\Components;

use Redirect;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

class Places extends ComponentBase {

    public $places;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    /**
     * Message to display when there are no messages.
     * @var string
     */
    public $noPlacesMessage;

    /**
     * Reference to the page name for linking to place.
     * @var string
     */
    public $placePage;

    public function componentDetails()
    {
        return [
            'name'        => 'sas.erp::lang.places.component_name',
            'description' => 'sas.erp::lang.places.component_desc'
        ];
    }

    public function defineProperties() {
        return [
            'pageNumber' => [
                'title'       => 'sas.erp::lang.settings.places_pagination',
                'description' => 'sas.erp::lang.settings.places_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'placesPerPage' => [
                'title'             => 'sas.erp::lang.settings.places_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'sas.erp::lang.settings.places_per_page_validation',
                'default'           => '10',
            ],
            'noPlacesMessage' => [
                'title'        => 'sas.erp::lang.settings.places_no_places',
                'description'  => 'sas.erp::lang.settings.places_no_places_description',
                'type'         => 'string',
                'default'      => 'No places found',
                'showExternalParam' => false
            ],
            'placePage' => [
                'title'       => 'sas.erp::lang.settings.places_place',
                'description' => 'sas.erp::lang.settings.places_place_description',
                'type'        => 'dropdown',
                'default'     => 'place',
                //'group'       => 'Links',
            ],
        ];
    }
    
    public function getPlacePageOptions() {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun() {
        $this->prepareVars();

        $this->places = $this->page['places'] = $this->listPlaces();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->places->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars() {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->noPlacesMessage = $this->page['noPlacesMessage'] = $this->property('noPlacesMessage');

        /*
         * Page links
         */
        $this->placePage = $this->page['placePage'] = $this->property('placePage');
    }

    protected function listPlaces() {
        /*
         * List all the posts, eager load their categories
         */
        $places = \Sas\Erp\Models\Place::listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'perPage'    => $this->property('placesPerPage'),
            'search'     => trim(input('search'))
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $places->each(function($place) {
            $place->setUrl($this->placePage, $this->controller);
        });

        return $places;
    }

}