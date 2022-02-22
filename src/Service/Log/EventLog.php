<?php
declare(strict_types=1);

namespace App\Service\Log;

use App\Common\IUuidProvider;
use App\Exception\IErrorCode;
use App\Exception\InvalidStateException;
use Monolog\Logger;

class EventLog implements IEventLog
{
	private const EXTRA_DATA_KEY = 'extra';

	private const UUID_KEY = 'uuid';

	private const FORBIDDEN_KEYS = [
		self::EXTRA_DATA_KEY,
		self::UUID_KEY,
	];

	private array $extraData = [];

	public function __construct(private Logger $logger, private IUuidProvider $uuidProvider)
	{
	}

	public function addExtraValue(string $key, mixed $value): void
	{
		$this->extraData[$key] = $value;
	}

	public function debug(string $msg, array $payload = []): void
	{
		$this->logger->debug($msg, $this->getPayload($payload));
	}

	public function info(string $msg, array $payload = []): void
	{
		$this->logger->info($msg, $this->getPayload($payload));
	}

	public function error(string $msg, array $payload = []): void
	{
		$this->logger->error($msg, $this->getPayload($payload));
	}

	public function warning(string $msg, array $payload = []): void
	{
		$this->logger->warning($msg, $this->getPayload($payload));
	}

	public function critical(string $msg, array $payload = []): void
	{
		$this->logger->critical($msg, $this->getPayload($payload));
	}

	private function getPayload(array $payload = []): array
	{
		foreach (self::FORBIDDEN_KEYS as $forbiddenKey) {
			isset($payload[$forbiddenKey]) && throw new InvalidStateException(
				sprintf('key %s is not allowed to be logged', $forbiddenKey),
				IErrorCode::LOG_FORBIDDEN_KEY
			);
		}

		return array_merge($payload, $this->getUuidAsArray(), $this->getExtraDataAsArray());
	}

	public function flush(): void
	{
		$this->extraData = [];
	}

	private function getExtraDataAsArray(): array
	{
		return [
			self::EXTRA_DATA_KEY => $this->extraData,
		];
	}

	public function getUuidAsArray(): array
	{
		return [self::UUID_KEY => $this->uuidProvider->getUuidWithCount()];
	}
}
