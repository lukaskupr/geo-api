<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Coordinates;

interface ICoordinatesService
{
	public function getCoordinatesByQuery(string $query): Coordinates;
}
