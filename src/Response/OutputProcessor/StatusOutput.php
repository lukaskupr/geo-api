<?php
declare(strict_types=1);

namespace App\Response\OutputProcessor;

use JsonSerializable;

class StatusOutput implements JsonSerializable
{
	public function jsonSerialize(): array
	{
		return ['status' => 'ok'];
	}
}
