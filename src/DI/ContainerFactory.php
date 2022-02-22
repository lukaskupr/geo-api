<?php
declare(strict_types=1);

namespace App\DI;

use DI\Container;
use DI\ContainerBuilder;
use Exception;

class ContainerFactory
{
	private ContainerBuilder $containerBuilder;

	public function __construct()
	{
		$this->containerBuilder = new ContainerBuilder();
	}

	/**
	 * @return Container
	 * @throws Exception
	 */
	public function create(): Container
	{
		$this->containerBuilder->addDefinitions((new Definitions())->getDefinitions());

		return $this->containerBuilder->build();
	}
}
