<?php
declare(strict_types=1);

namespace App\Service\Log;

interface IEventLog
{
	/**
	 * Registered values will be added to all logged events.
	 */
	public function addExtraValue(string $key, mixed $value): void;

	public function debug(string $msg, array $payload = []): void;

	public function info(string $msg, array $payload = []): void;

	public function error(string $msg, array $payload = []): void;

	public function warning(string $msg, array $payload = []): void;

	public function critical(string $msg, array $payload = []): void;
}
