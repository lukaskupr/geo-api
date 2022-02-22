<?php
declare(strict_types=1);

namespace App\Configuration;

class EnvironmentType implements IEnvironmentType
{
	private static array $ENV_MAPPING = [
		'testing' => 'test',
		'production' => 'prod',
	];

	private const PRODUCTION_ENV = 'prod';

	private const DEV_ENV = 'dev';

	public function __construct(private IConfig $config)
	{
	}

	public function isProd(): bool
	{
		return $this->getType() === self::PRODUCTION_ENV;
	}

	public function isDev(): bool
	{
		return $this->getType() === self::DEV_ENV;
	}

	private function getType(): string
	{
		$env = strtolower($this->config->getEnvironmentType());

		return self::$ENV_MAPPING[$env] ?? $env;
	}
}
