<?php
declare(strict_types=1);

namespace App\DI\Factory;

use App\Common\IUuidProvider;
use App\Configuration\IConfig;
use App\Service\Log\EventLog;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class EventLogFactory
{
	public function create(IConfig $config, IUuidProvider $uuidProvider): EventLog
	{
		$handler = new StreamHandler('php://stderr');
		$handler->setFormatter(new JsonFormatter());

		$logger = new Logger($config->getProjectId());
		$logger->pushHandler($handler);

		return new EventLog($logger, $uuidProvider);
	}
}
