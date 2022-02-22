<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Coordinates;

interface ICoordinatesRepository
{
	public function getCoordinatesByQuery(string $query): ?Coordinates;
}
