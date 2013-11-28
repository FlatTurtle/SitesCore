<?php namespace Flatturtle\Sitecore\Models;

use Jenssegers\Model\Model;
use Guzzle\Http\Client;

class FlatTurtle extends Model {

	protected static $baseUrl = 'http://s.flatturtle.com/';

	public static function get($id)
	{
		// Query FlatTurtle endpoint
		$client = new Client(self::$baseUrl);
		$request = $client->get("$id.json");
		$response = $request->send();

		// Decode json
		$data = (array) json_decode($response->getBody());

		if (!$data) return;

		return new self($data);
	}

	/*
	|--------------------------------------------------------------------------
	| Accessors
	|--------------------------------------------------------------------------
	*/

	public function getLongitudeAttribute()
	{
		return $this->interface->longitude;
	}

	public function getLatitudeAttribute()
	{
		return $this->interface->latitude;
	}

	public function getColorAttribute()
	{
		return $this->interface->color;
	}

	public function getTitleAttribute()
	{
		return $this->interface->title;
	}

}
