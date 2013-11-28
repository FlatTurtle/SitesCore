<?php namespace Flatturtle\Sitecore\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'flatturtle:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update the FlatTurtle sitecore package';

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
		// Flatturtle not installed
		if (!\File::exists(app_path() . '/config/packages/flatturtle/sitecore/config.php'))
		{
			return $this->call('flatturtle:install');
		}

		// Publish the assets
		$this->call('asset:publish', array('package' => 'flatturtle/sitecore'));

		// Clear cache
		$this->call('cache:clear');

		$this->comment('FlatTurtle SiteCore updated');
	}

}
