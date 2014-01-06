<?php namespace Flatturtle\Sitecore\Models;

use DateTime;
use Exception;
use Lang;
use Config;
use Validator;
use Log;
use Input;
use Jenssegers\Model\Model;
use Guzzle\Http\Client;

class Reservation extends Model {

	protected $baseUrl = 'https://reservations.flatturtle.com/';

	public function save()
	{
		// Attribute validation rules
		$validator = Validator::make($this->attributes, array(
			'company' => 'required',
		    'email' => 'required|email',
		    'subject' => 'required',
		    'name' => 'required',
		    'cluster' => 'required',
		    'from' => array('required', 'regex:#[0-9]{1,2}:[0-9]{2}#'),
		    'to' => array('required', 'regex:#[0-9]{1,2}:[0-9]{2}#'),
		    'type' => 'required',
		));

		// Throw exception
		if ($validator->fails())
		{
			$errors = $validator->messages();
			throw new Exception(implode('<br>', $errors->all()));
		}

		// Create datetime objects
		try {
			$from = new DateTime($this->from);
			$to = new DateTime($this->to);
		}
		catch (\Exception $e)
		{
			throw new Exception(Lang::get('sitecore::reservations.bad_date'));
		}

		$data = array(
			'thing' => $this->baseUrl . $this->cluster . '/things/' . $this->name,
			'type' => $this->type,
			'time' => array(
				'from' => $from->format('c'),
				'to' => $to->format('c'),
			),
			'subject' => $this->subject,
			'announce' => $this->announce,
			'comment' => $this->comment ?: 'No comment',
			'customer' => array(
				'email' => $this->email,
				'company' => $this->company,
			),
			'announce' => $this->announce ?: array(),
		);

		// Create request
		$client = new Client($this->baseUrl);
		$request = $client->post($this->cluster . '/reservations', null, json_encode($data));

		// Add basic auth
		$password = Config::get('sitecore::passwords.reservations');
		$request->setAuth($this->cluster, $password);

		try
		{
			$response = $request->send();
		}
		catch (\Exception $e)
		{
			$response = $e->getResponse();

			// Get JSON
			$json = json_decode($response->getBody());

			// Something went wrong, logging response
			if (!$json)
			{
				Log::error((string) $response);
				throw new Exception(Lang::get("sitecore::reservations.error"));
			}

			// Get errors
			$errors = $json->errors;

			// Only first error at this moment
			$error = reset($errors);
			$type = strtolower($error->type);

			// Replace some characters
			$type = str_replace(array(' ', '_', '.'), '-', $type);

			if (Lang::has("sitecore::reservations.$type"))
			{
				throw new Exception(Lang::get("sitecore::reservations.$type"));
			}
			else
			{
				throw new Exception(Lang::get("sitecore::reservations.error") . ' (' . $error['message'] . ')');
			}

		}

		return true;
	}

	/*
	|--------------------------------------------------------------------------
	| Mutators
	|--------------------------------------------------------------------------
	*/

	public function setAnnounceAttribute($value)
	{
		// Replace delimiters
		$value = str_replace(array(';'), ',', $value);

		// Explode & loop
		$parts = explode(',', $value);
		foreach ($parts as &$part)
		{
			$part = trim($part);
		}

		$this->attributes['announce'] = $parts;
	}

}
