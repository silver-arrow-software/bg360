<?php namespace Hambern\Company\Models;

/**
 * Gallery Model
 */
class Gallery extends Model
{
	/**
	 * @var string The database table used by the model.
	 */
	public $table = 'hambern_company_galleries';
	/**
	 * @var array Relations
	 */
	public $hasOne = [];
	public $hasMany = [];
	public $belongsTo = [];
	public $belongsToMany = [
		'tags' => [
			'Hambern\Company\Models\Tag',
			'table' => 'hambern_company_pivots',
		],
	];
	public $morphTo = [];
	public $morphOne = [];
	public $morphMany = [];
	public $attachOne = [];
	public $attachMany = [
		'pictures' => ['System\Models\File'],
	];

}
