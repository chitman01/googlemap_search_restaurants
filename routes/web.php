<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'IndexController@index');
Route::get('/getLocationList', 'IndexController@getLocationList');
Route::get('/getDetailData', 'IndexController@getDetailData');
Route::get('/getImageFromApi/{photo_reference}', 'IndexController@getImageFromApi');

