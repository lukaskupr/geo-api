<?php
declare(strict_types=1);

namespace App\DI;

use App\Api\ApiClientFactory;
use App\Api\ApiClientLogger;
use App\Api\ExternalOSM\ExternalOSMRepository;
use App\Api\IApiClientFactory;
use App\Api\IApiClientLogger;
use App\Common\Header;
use App\Common\IHeader;
use App\Common\IUuidProvider;
use App\Common\TimeMeasuring;
use App\Common\UuidProvider;
use App\Configuration\Config;
use App\Configuration\EnvironmentType;
use App\Configuration\IConfig;
use App\Configuration\IEnvironmentType;
use App\DI\Factory\CoordinatesServiceFactory;
use App\DI\Factory\EventLogFactory;
use App\DI\Factory\ExternalOSMRepositoryFactory;
use App\DI\Factory\ServerRequestHandlerFactory;
use App\DI\Factory\ServerRequestServiceFactory;
use App\DI\Factory\UuidProviderFactory;
use App\Service\Flusher\Flusher;
use App\Service\Flusher\IFlusher;
use App\Service\ICoordinatesService;
use App\Service\Log\IEventLog;
use App\Service\Log\IRequestLogger;
use App\Service\Log\RequestLogger;
use App\Service\ServerRequest\IServerRequestHandler;
use App\Service\ServerRequest\IServerRequestService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use function DI\autowire;
use function DI\factory;
use function DI\get;

class Definitions
{
	public const API_REQUEST_DURATION = 'apiRequestDuration';

	public const WORKER_PROCESSING_DURATION = 'workerProcessingDuration';

	public function getDefinitions(): array
	{
		return [
			ResponseFactoryInterface::class => autowire(Psr17Factory::class),
			IConfig::class => autowire(Config::class),
			IEnvironmentType::class => autowire(EnvironmentType::class),
			ICoordinatesService::class => factory([CoordinatesServiceFactory::class, 'create']),
			IServerRequestService::class => factory([ServerRequestServiceFactory::class, 'create']),
			IServerRequestHandler::class => factory([ServerRequestHandlerFactory::class, 'create']),
			IEventLog::class => factory([EventLogFactory::class, 'create']),
			IRequestLogger::class => autowire(RequestLogger::class),
			IUuidProvider::class => factory([UuidProviderFactory::class, 'create']),
			IFlusher::class => autowire(Flusher::class),
			IHeader::class => autowire(Header::class),

			# Instances for time measuring
			self::API_REQUEST_DURATION => autowire(TimeMeasuring::class),
			self::WORKER_PROCESSING_DURATION => autowire(TimeMeasuring::class),

			# Api clients
			IApiClientLogger::class => autowire(ApiClientLogger::class),
			IApiClientFactory::class => autowire(ApiClientFactory::class)
				->constructorParameter('timeMeasuring', get(self::API_REQUEST_DURATION)),

			# Repositories
			ExternalOSMRepository::class => factory([ExternalOSMRepositoryFactory::class, 'create']),
		];
	}
}
