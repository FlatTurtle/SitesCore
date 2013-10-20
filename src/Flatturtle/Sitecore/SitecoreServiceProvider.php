<?php namespace Flatturtle\Sitecore;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Flatturtle\Sitecore\Commands\InstallCommand;
use Flatturtle\Sitecore\Commands\UpdateCommand;

class SitecoreServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('flatturtle/sitecore');

		// Register custom routes
    	include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register custom commands
		$this->app['command.flatturtle.install'] = new InstallCommand;
		$this->app['command.flatturtle.update'] = new UpdateCommand;
    	$this->commands('command.flatturtle.install', 'command.flatturtle.update');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
