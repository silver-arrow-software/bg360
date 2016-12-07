<?php namespace Sas\Blog\Http;

use Backend\Classes\Controller;

/**
 * Api Post Back-end Controller
 */
class ApiPost extends Controller
{
    public $implement = [
        'Mohsin.Rest.Behaviors.RestController'
    ];

    public $restConfig = 'config_rest.yaml';

}
