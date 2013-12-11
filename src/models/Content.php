<?php namespace Flatturtle\Sitecore\Models;

use Illuminate\Support\Collection;
use Jenssegers\Model\Model;
use Parsedown;
use Cache;
use File;
use Config;

class Content extends Model {

	public static function all()
	{
		// Get directory from config
		$directory = Config::get('sitecore::directory', 'content');

		// Get content files
		$files = File::files(base_path() . '/' . $directory);

		$models = array();

		// Loop all files
		foreach ($files as $file)
		{
			// Generate cache based on file modification date
			$cache_key = 'flatturtle.' . md5($file . filemtime($file));

			// Try to get model from cache
			$model = Cache::rememberForever($cache_key, function() use ($file)
			{
			    // Create block id based on file name
				$id = pathinfo($file, PATHINFO_FILENAME);
				$id = preg_replace('#[0-9]+-#', '', $id);
				$id = str_replace(' ', '-', $id);
				$id = strtolower($id);

				// Create a new model
				$model = new self;
				$model->id = $id;
				$model->type = pathinfo($file, PATHINFO_EXTENSION);
				$model->html = File::get($file);

				// Parse markdown
				if ($model->type == 'md')
				{
					$model->html = Parsedown::instance()->parse($model->html);
				}

				return $model;
			});

			// Add model to list
			$models[] = $model;
		}

		return new Collection($models);
	}

	/*
	|--------------------------------------------------------------------------
	| Accessors
	|--------------------------------------------------------------------------
	*/

	public function getTitleAttribute($value)
	{
		// Get first h1
		preg_match('#<h1[^>]*>([^>]+)</h1>#', $this->html, $matches);

		if (isset($matches[1]))
		{
			return strip_tags($matches[1]);
		}

	}

}
