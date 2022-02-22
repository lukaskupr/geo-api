<?php
declare(strict_types=1);

namespace App\DB\Nominatim;

use App\Entity\Coordinates;
use App\Repository\ICoordinatesRepository;

class NominatimRepository implements ICoordinatesRepository
{
	public function getCoordinatesByQuery(string $query): ?Coordinates
	{
		// TODO implement logic to load data from internal database
		return null;
	}
}
