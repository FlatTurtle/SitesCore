<?php

use Illuminate\Routing\Controller;
use Flatturtle\Sitecore\Models\Reservation;
use Guzzle\Http\Client;

class ReservationController extends Controller {

    public function getIndex()
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
    }

    public function Availability() 
    {
        $availability = View::exists('availability') ? 'availability' : Config::get('sitecore::availability', 'sitecore::availability');
   
        return View::make($availability);
    }

}
