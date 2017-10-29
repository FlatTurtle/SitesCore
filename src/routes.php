<?php

/*
|--------------------------------------------------------------------------
| Reservations API "proxy"
|--------------------------------------------------------------------------
*/

Route::post('/reserve', 'ReservationController@getIndex');
Route::get('/reservations/availability/', 'ReservationController@availability');


/*
|--------------------------------------------------------------------------
| Main site route
|--------------------------------------------------------------------------
*/

Route::any('/', 'SiteController@getIndex');
