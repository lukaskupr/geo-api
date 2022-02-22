<?php
declare(strict_types=1);

namespace App\Service\Flusher;

class Flusher implements IFlusher
{
	private array $flushables = [];

	public function register(IFlushable $flushable): void
	{
		$this->flushables[] = $flushable;
	}

	public function flush(): void
	{
		/** @var IFlushable $flushable */
		foreach ($this->flushables as $flushable) {
			$flushable->flush();
		}
	}
}
