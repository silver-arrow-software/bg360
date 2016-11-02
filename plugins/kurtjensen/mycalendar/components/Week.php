<?php namespace KurtJensen\MyCalendar\Components;

use Carbon\Carbon;
use Cms\Classes\ComponentBase;

class Week extends ComponentBase {
	public $w_start;
	public $day;
	public $month;
	public $year;
	public $dayprops;
	public $color;
	public $events;
	public $calHeadings;

	public $monthTitle;
	public $monthNum;
	public $running_day;
	public $days_in_month;
	public $dayPointer;

	public function componentDetails() {
		return [
			'name' => 'kurtjensen.mycalendar::lang.week.name',
			'description' => 'kurtjensen.mycalendar::lang.week.description',
		];
	}

	public function defineProperties() {
		return [
			'firstday' => [
				'title' => 'First Day',
				'description' => 'The first day of the week.',
			],
			'day' => [
				'title' => 'Day',
				'description' => 'The week containing day you want to show.',
			],
			'month' => [
				'title' => 'Month',
				'description' => 'The month you want to show.',
			],
			'year' => [
				'title' => 'Year',
				'description' => 'The year you want to show.',
			],
			'events' => [
				'title' => 'Events',
				'description' => 'Array of the events you want to show.',
			],
			'color' => [
				'title' => 'Calendar Color',
				'description' => 'Array of the events you want to show.',
				'type' => 'dropdown',
				'default' => 'red',
			],
			'dayprops' => [
				'title' => 'Day Properties',
				'description' => 'Array of the properties you want to put on the day indicator.',
			],
			'loadstyle' => [
				'title' => 'Load Style Sheet',
				'description' => 'Load the default CSS file.',
				'type' => 'dropdown',
				'default' => '1',
				'options' => [0 => 'No', 1 => 'Yes'],
			],
		];
	}

	public function getColorOptions() {
		return ['red' => 'red', 'green' => 'green', 'blue' => 'blue', 'yellow' => 'yellow'];
	}

	public function onRender() {
		if ($this->property('loadstyle')) {
			$this->addCss('/plugins/kurtjensen/mycalendar/assets/css/calendar.css');
		}

		$this->day = $this->property('day', date('d'));
		$this->month = $this->property('month', date('m'));
		$this->year = $this->property('year', date('Y'));
		$this->calcElements();
		$this->dayprops = $this->property('dayprops');
		$this->color = $this->property('color');
		$this->events = $this->property('events');
	}

	public function calcElements() {

		$this->calHeadings = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

		$targetDay = new Carbon($this->year . '-' . $this->month . '-' . $this->day);
		$dayOfWeek = $targetDay->dayOfWeek;
		$firstWeekDay = $this->property('firstday', 1);
		$DaysToShift = $dayOfWeek - $firstWeekDay;
		if ($DaysToShift < 0) {
			$DaysToShift += 6;
		}

		$this->monthNum = $targetDay->month;
		$this->running_day = $firstWeekDay;
		$this->days_in_month = $targetDay->daysInMonth;
		//$this->dayPointer = 0 - $this->running_day;

		$this->dayPointer = $targetDay->subDay($dayOfWeek + 1)->day;

	}

}
