<?php
declare(strict_types=1);

namespace App\Api;

use App\Common\ITimeMeasuring;
use App\Configuration\IConfig;
use GuzzleHttp\Client as GuzzleClient;

class ApiClientFactory implements IApiClientFactory
{
	public function __construct(
		private IConfig $appConfig,
		private IApiClientLogger $apiClientLogger,
		private GuzzleClient $client,
		private ITimeMeasuring $timeMeasuring,
	) {
	}

	public function create(IApiClientConfig $config): IApiClient
	{
		return new ApiClient(
			$this->appConfig,
			$this->apiClientLogger,
			$this->client,
			$config,
			$this->timeMeasuring,
		);
	}
}
