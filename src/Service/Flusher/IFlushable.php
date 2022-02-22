<?php
declare(strict_types=1);

namespace App\Service\Flusher;

interface IFlushable
{
	public function flush(): void;
}
