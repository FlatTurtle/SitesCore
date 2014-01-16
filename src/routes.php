<?php

use Flatturtle\Sitecore\Models\Content;
use Flatturtle\Sitecore\Models\FlatTurtle;
use Flatturtle\Sitecore\Models\Image;
use Flatturtle\Sitecore\Models\Reservation;

/*
|--------------------------------------------------------------------------
| Reservations API "proxy"
|--------------------------------------------------------------------------
*/

Route::post('/reserve', function()
{
	// Create reservation object
	$reservation = new Reservation;
	$reservation->name = Input::get('name');
	$reservation->type = Input::get('type');
	$reservation->cluster = Input::get('cluster');
	$reservation->company = Input::get('company');
	$reservation->email = Input::get('email');
	$reservation->subject = Input::get('subject');
	$reservation->announce = Input::get('announce');
	$reservation->comment = Input::get('comment');
	$reservation->from = Input::get('from');
	$reservation->to = Input::get('to');

	// Any exceptions that get thrown will be returned as a json error message.
	// Only when the reservation is successful, we need to return a success message.
	$response = $reservation->save();

	return Response::json(array(
		'message' => Lang::get('sitecore::reservations.success')
	));
});


/*
|--------------------------------------------------------------------------
| Main site route
|--------------------------------------------------------------------------
*/

Route::any('/', function()
{
	// Content blocks array
	$blocks = Content::all();

	// Get FlatTurtle config
	$flatturtle = FlatTurtle::get(Config::get('sitecore::id'));
	if (!$flatturtle) App::abort(500, 'Invalid FlatTurtle configuration');

	// Carousel images
	$images = Image::all();

	// Get template from configu
	$template = Config::get('sitecore::template');

	// Check if reservations are enabled
	$reservations = Config::has('sitecore::passwords.reservations') && isset($flatturtle->interface->clustername);

	// Render the template
	return View::make($template, array('flatturtle' => $flatturtle, 'blocks' => $blocks, 'images' => $images, 'reservations' => $reservations));

});
