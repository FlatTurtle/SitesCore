<?php namespace Flatturtle\Sitecore\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'flatturtle:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install the FlatTurtle sitecore package';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// Generate session key
		$this->call('key:generate');

		// Publish the configuration file
		$this->call('config:publish', array('package' => 'flatturtle/sitecore'));

		// Publish the assets
		$this->call('asset:publish', array('package' => 'flatturtle/sitecore'));

		// Clear cache
		$this->call('cache:clear');
	}

}
