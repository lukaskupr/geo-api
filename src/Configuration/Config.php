<?php
declare(strict_types=1);

namespace App\Configuration;

use App\Exception\ConfigurationException;
use App\Exception\IErrorCode;

class Config implements IConfig
{
	private const DEFAULT_EXTERNAL_OSM_TIMEOUT = 5000;

	public function getEnvironmentType(): string
	{
		return $this->getValue('APPLICATION_ENV');
	}

	public function getProjectId(): string
	{
		return $this->getValue('PROJECT_ID');
	}

	public function getExternalOsmUrl(): string
	{
		return $this->getValue('EXTERNAL_OSM_URL');
	}

	public function getExternalOsmTimeout(): int
	{
		try {
			return intval($this->getValue('EXTERNAL_OSM_TIMEOUT'));
		} catch (ConfigurationException) {
			return self::DEFAULT_EXTERNAL_OSM_TIMEOUT;
		}
	}

	private function getValue(string $id): string
	{
		$val = getenv($id);

		($val === false || $val === '') && throw new ConfigurationException(
			sprintf('Configuration value not exists for (%s)', $id),
			IErrorCode::MISSING_CONFIGURATION_VALUE,
		);

		!is_string($val) && throw new ConfigurationException(
			sprintf('Invalid configuration type for (%s)', $id),
			IErrorCode::INVALID_CONFIGURATION_VALUE_TYPE,
		);

		return strval($val);
	}
}
