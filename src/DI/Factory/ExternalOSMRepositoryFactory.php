<?php
declare(strict_types=1);

namespace App\DI\Factory;

use App\Api\ApiClientConfig;
use App\Api\ExternalOSM\ExternalOSMRepository;
use App\Api\IApiClientFactory;
use App\Configuration\IConfig;

class ExternalOSMRepositoryFactory
{
	public function create(IApiClientFactory $apiClientFactory, IConfig $config): ExternalOSMRepository
	{
		$config = new ApiClientConfig($config->getExternalOsmUrl(), timeout: $config->getExternalOsmTimeout());

		return new ExternalOSMRepository($apiClientFactory->create($config));
	}
}
