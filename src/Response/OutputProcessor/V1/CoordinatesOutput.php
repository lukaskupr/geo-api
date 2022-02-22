<?php
declare(strict_types=1);

namespace App\Response\OutputProcessor\V1;

use App\Entity\Coordinates;
use JsonSerializable;

class CoordinatesOutput implements JsonSerializable
{
	public function __construct(private Coordinates $coordinates)
	{
	}

	public function jsonSerialize(): array
	{
		return [
			'latitude' => $this->coordinates->getLatitude(),
			'longitude' => $this->coordinates->getLongitude(),
		];
	}
}
