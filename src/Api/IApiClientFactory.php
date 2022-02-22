<?php
declare(strict_types=1);

namespace App\Api;

interface IApiClientFactory
{
	public function create(IApiClientConfig $config): IApiClient;
}
