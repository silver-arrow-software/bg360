<?php

Route::group(['prefix' => 'api'], function() {
    Route::post('auth/register', '\Sas\Api\Controllers\ApiAuth@register');
    Route::post('auth/login', '\Sas\Api\Controllers\ApiAuth@authenticate');
});
