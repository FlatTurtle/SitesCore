<?php

use Flatturtle\Sitecore\Models\Content;
use Flatturtle\Sitecore\Models\FlatTurtle;
use Flatturtle\Sitecore\Models\Image;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	// Content blocks array
	$blocks = Content::all();

	// Get FlatTurtle config
	$flatturtle = FlatTurtle::get(Config::get('sitecore::id'));
	if (!$flatturtle) App::abort(500, 'Invalid FlatTurtle configuration');

	// Carousel images
	$images = Image::all();

	// Render the template
	return View::make(Config::get('sitecore::template', 'template'), array('flatturtle' => $flatturtle, 'blocks' => $blocks, 'images' => $images));

});
