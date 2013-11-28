<?php namespace Flatturtle\Sitecore\Models;

use Jenssegers\Model\Model;
use \Michelf\MarkdownExtra;

class Content extends Model {

	public static function all()
	{
		// Get directory from config
		$directory = \Config::get('sitecore::directory', 'content');

		// Get content files
		$files = \File::files(base_path() . '/' . $directory);

		$models = array();

		// Loop all files
		foreach ($files as $file)
		{
			// Generate cache based on file modification date
			$cache_key = 'flatturtle.' . md5($file . filemtime($file));

			if (\Cache::has($cache_key))
			{
				// Get model from cache
				$models[] = \Cache::get($cache_key);
			}
			else
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
				$model->html = \File::get($file);

				// Parse markdown
				if ($model->type == 'md')
				{
					$parser = new MarkdownExtra;
					$model->html = $parser->transform($model->html);
				}

				// Store cache
				\Cache::forever($cache_key, $model);

				$models[] = $model;
			}
		}

		return $models;
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
