<?php namespace Flatturtle\Sitecore\Models;

use Illuminate\Support\Collection;
use Jenssegers\Model\Model;
use Parsedown;
use Cache;
use File;
use Config;
use View;

class Content extends Model {

	public static function all()
	{
		// Cache the directory listing
		return Cache::rememberForever('flatturtle.files', function()
		{
			// Get directory from config
			$directory = Config::get('sitecore::directory', 'content');

			// Get content files
			$files = File::files(base_path() . '/' . $directory);

			// The model collection
			$models = new Collection;

			// Loop all files
			foreach ($files as $file)
			{
			    // Create id based on file name
				$id = pathinfo($file, PATHINFO_FILENAME);
				$id = preg_replace('#[0-9]+-#', '', $id);
				$id = str_replace(' ', '-', $id);
				$id = strtolower($id);

				// Create a new model
				$model = new Content;
				$model->id = $id;
				$model->file = $file;
				$model->type = pathinfo($file, PATHINFO_EXTENSION);

				// Add model to list
				$models->push($model);
			}

			return $models;
		});
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

	public function getHtmlAttribute()
	{
		// Parse markdown
		if ($this->type == 'md')
		{
			$model = $this;

			// Cache parsed markdown forever
			return Cache::rememberForever('flatturtle.file.{$this->id}', function() use($model)
			{
				$html = File::get($model->file);
				return Parsedown::instance()->parse($html);
			});
		}

		// Include php files
		else if ($this->type == 'php')
		{
			// Filename without .blade.php
			$view = str_replace('.blade', '', pathinfo($this->file, PATHINFO_FILENAME));
			return (string) View::make($view);
		}

		return File::get($this->file);
	}

}
