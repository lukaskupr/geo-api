<?php
declare(strict_types=1);

namespace App\Common;

use App\Service\Flusher\IFlushable;

class UuidProvider implements IUuidProvider, IFlushable
{
	private int $counter = 0;

	private ?string $uuid = null;

	private function getUuid(): string
	{
		!$this->uuid && $this->uuid = uniqid('', true);

		assert(is_string($this->uuid));

		return $this->uuid;
	}

	public function getUuidWithCount(): string
	{
		!$this->uuid && $this->uuid = uniqid('', true);

		return $this->getUuid() . '+' . ++$this->counter;
	}

	public function flush(): void
	{
		$this->counter = 0;
		$this->uuid = null;
	}
}
