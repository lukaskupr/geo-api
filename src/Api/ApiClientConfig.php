<?php
declare(strict_types=1);

namespace App\Api;

class ApiClientConfig implements IApiClientConfig
{
	private string $basePath;

	private ?string $authToken;

	private float $timeout;

	public function __construct(
		string $basePath,
		?string $authToken = null,
		?float $timeout = null,
	) {
		$this->basePath = rtrim($basePath, '/');
		$this->authToken = $authToken;
		$this->timeout = $timeout ?? 0;
	}

	public function getBaseUrl(): string
	{
		return $this->basePath;
	}

	public function getAuthToken(): ?string
	{
		return $this->authToken;
	}

	public function getTimeout(): float
	{
		return $this->timeout;
	}
}
