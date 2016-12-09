<?php

Route::group(['prefix' => 'api/v1', 'middleware' => 'jwt.auth'], function () {
    Route::resource('apiposts', 'Sas\Blog\Http\ApiPost');
});