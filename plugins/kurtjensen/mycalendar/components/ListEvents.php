<?php namespace KurtJensen\MyCalendar\Components;

use KurtJensen\MyCalendar\Components\Events;
use KurtJensen\MyCalendar\Components\EvList;

class ListEvents extends EvList {
	public $EventsComp = null;

	public function componentDetails() {
		return [
			'name' => 'kurtjensen.mycalendar::lang.list_events.name',
			'description' => 'kurtjensen.mycalendar::lang.list_events.description',
		];
	}

	public function defineProperties() {
		$this->EventsComp = new Events();
		$this->EventsComp->compLink = 'ListEvents';
		$properties = parent::defineProperties();

		return array_merge($properties, $this->EventsComp->defineProperties());
	}

	public function init() {
		$this->EventsComp->month = $this->property('month');
		$this->EventsComp->year = $this->property('year');
		$this->EventsComp->category = $this->property('category', null);
		$this->EventsComp->usePermissions = $this->property('usePermissions', 0);
		if (!$this->property('month') || !$this->property('year')) {
			$this->EventsComp->dayspast = $this->property('dayspast', 120);
			$this->EventsComp->daysfuture = $this->property('daysfuture', 60);
		}
	}

	public function onRun() {
		$this->events = $this->EventsComp->loadEvents();
	}

	public function onRender() {
		if ($this->property('loadstyle')) {
			$this->addCss('/plugins/kurtjensen/mycalendar/assets/css/calendar.css');
		}
		$this->dayprops = $this->property('dayprops');
		$this->color = $this->property('color');
	}

	public function onShowEvent() {
		$this->EventsComp->compLink = 'ListEvents';
		return $this->page['ev'] = $this->EventsComp->onShowEvent();
	}
}
