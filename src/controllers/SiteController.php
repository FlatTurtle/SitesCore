<?php

use Illuminate\Routing\Controller;
use Flatturtle\Sitecore\Models\Content;
use Flatturtle\Sitecore\Models\FlatTurtle;
use Flatturtle\Sitecore\Models\Image;

class SiteController extends Controller {

    public function getIndex()
    {
        // Content blocks array
        $blocks = Content::all();

        // Get FlatTurtle config
        $flatturtle = FlatTurtle::get(Config::get('sitecore::id'));
        if (!$flatturtle) App::abort(500, 'Invalid FlatTurtle configuration');

        // Carousel images
        $images = Image::all();

        // Detect template
        $template = View::exists('template') ? 'template' : Config::get('sitecore::template', 'sitecore::template');

        // Check if reservations are enabled
        $reservations = Config::has('sitecore::passwords.reservations') && isset($flatturtle->interface->clustername);

        // Render the template
        return View::make($template, array('flatturtle' => $flatturtle, 'blocks' => $blocks, 'images' => $images, 'reservations' => $reservations));
    }

}
