<?php namespace Sas\Erp\Models;

use Model;

/**
 * AccountTransaction Model
 */
class AccountTransaction extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'sas_erp_account_transactions';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['user_id', 'description', 'amount', 'account_id', 'created_at'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'account' => ['Sas\Erp\Models\Account'],
        'user' => ['RainLab\User\Models\User']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [
        'attachments' => ['System\Models\File']
    ];

}
