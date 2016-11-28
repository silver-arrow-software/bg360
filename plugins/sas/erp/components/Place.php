<?php namespace Sas\Erp\Components;

use Cms\Classes\ComponentBase;

class Place extends ComponentBase {

    public $place;

    public function componentDetails() {
        return [
            'name'        => 'sas.erp::lang.plugin.place',
            'description' => 'sas.erp::lang.place.component_desc'
        ];
    }

    public function defineProperties() {
        return [
            'slug' => [
                'title'       => 'sas.erp::lang.settings.place_slug',
                'description' => 'sas.erp::lang.settings.place_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
        ];
    }

    public function onRun() {
        $this->place = $this->page['place'] = $this->loadPlace();
    }

    protected function loadPlace() {
        $slug = $this->property('slug');

        $place = \Sas\Erp\Models\Place::where('code_id', $slug)->firstOrFail();

        return $place;
    }
}