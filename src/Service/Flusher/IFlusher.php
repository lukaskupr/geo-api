<?php
declare(strict_types=1);

namespace App\Service\Flusher;

/**
 * Cleaning up registered instances after each request.
 */
interface IFlusher
{
	public function register(IFlushable $flushable): void;

	public function flush(): void;
}
