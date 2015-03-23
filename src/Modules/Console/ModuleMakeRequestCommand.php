<?php
namespace Veemo\Core\Modules\Console;

use Veemo\Core\Modules\Handlers\ModuleMakeRequestHandler;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ModuleMakeRequestCommand extends Command
{
	/**
	 * @var string $name The console command name.
	 */
	protected $name = 'veemo:module:make-request';

	/**
	 * @var string $description The console command description.
	 */
	protected $description = 'Create a new module form request class';

	/**
	 * @var \Veemo\Core\Modules\Handlers\ModuleMakeRequestHandler
	 */
	protected $handler;

	/**
	 * Create a new command instance.
	 *
	 * @param \Veemo\Core\Modules\Handlers\ModuleMakeRequestHandler $handler
	 */
	public function __construct(ModuleMakeRequestHandler $handler)
	{
		parent::__construct();

		$this->handler = $handler;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		return $this->handler->fire($this, $this->argument('module'), $this->argument('name'));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['module', InputArgument::REQUIRED, 'The slug of the module'],
			['name', InputArgument::REQUIRED, 'The name of the class']
		];
	}
}
