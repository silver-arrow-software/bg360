<?php namespace Sas\Erp\Models;

use Model;
use October\Rain\Database\Traits\Validation;

/**
 * Feedback Model
 */
class Feedback extends Model
{
    use Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_feedbacks';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'email',
        'message',
        'channel_id'
    ];

    /**
     * @var array The rules to be applied to the data.
     */
    public $rules = [
        'name' => '',
        'email' => 'email|required',
        'message' => 'required',
        'channel_id' => 'integer|required'
    ];


    /**
     * @var array The array of custom attribute names.
     */
    public $attributeNames = [
        'name' => 'sas.erp::lang.feedback.name',
        'email' => 'sas.erp::lang.feedback.email',
        'message' => 'sas.erp::lang.feedback.message'
    ];

    /**
     * @var array The array of custom error messages.
     */
    public $customMessages = [
        'email' => 'sas.erp::lang.component.onSend.error.email.email'
    ];


    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'channel' => '\Sas\Erp\Models\FeedbackChannel'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public static function archive($query)
    {
        $query->update(['archived' => true]);
    }

}
