<?php
declare(strict_types=1);

namespace App\Common;

interface ITimeMeasuring
{
	public function start(): void;

	/**
	 * @return float Time in milliseconds since $this->start method was called.
	 */
	public function finish(): float;
}
