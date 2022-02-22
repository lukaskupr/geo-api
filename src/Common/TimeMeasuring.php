<?php
declare(strict_types=1);

namespace App\Common;

use App\Exception\IErrorCode;
use App\Exception\InvalidStateException;

class TimeMeasuring implements ITimeMeasuring
{
	private ?float $startTime = null;

	public function start(): void
	{
		$this->startTime = microtime(true);
	}

	public function finish(): float
	{
		$result = $this->getCurrentDuration();
		$this->startTime = null;

		return $result;
	}

	private function getCurrentDuration(): float
	{
		$this->startTime === null && throw new InvalidStateException(
			'Time measuring error. Did you forget to call the start method?',
			IErrorCode::START_TIME_NOT_INITIALIZED
		);

		return microtime(true) - $this->startTime;
	}
}
