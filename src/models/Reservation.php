<?php namespace Flatturtle\Sitecore\Models;

use DateTime;
use Exception;
use Lang;
use Config;
use Validator;
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
				'to' => $to->format('c')
			),
			'comment' => $this->comment ?: 'No comment',
			'customer' => array(
				'mail' => $this->email,
				'company' => $this->company,
			),
			'subject' => $this->subject ?: 'No subject',
			'announce' => $this->announce ?: array(),
		);

		// Create request
		$client = new Client($this->baseUrl);
		$request = $client->post($this->cluster . '/reservations', null, json_encode($data));

		// Add basic auth
		$password = Config::get('sitecore::reservation_password');
		$request->setAuth($this->cluster, $password);

		try
		{
			$response = $request->send();
		}
		catch (\Exception $e)
		{
			$response = $e->getResponse();

			// Bad credentials
			if ($response->getStatusCode() == 401)
			{
				throw new Exception(Lang::get('sitecore::reservations.bad_credentials'));
			}

			// Get JSON error message
			$json = $response->json();
			if (isset($json['error']) && $error = $json['error'])
			{
				// Entity not available
				if (stristr($error, 'not available at that time'))
				{
					throw new Exception(Lang::get('sitecore::reservations.not_available'));
				}

				throw new Exception($error);
			}
		}

		return true;
	}

}
