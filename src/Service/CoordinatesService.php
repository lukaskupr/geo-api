<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Coordinates;
use App\Exception\IErrorCode;
use App\Exception\NotFoundException;
use App\Repository\ICoordinatesRepository;

class CoordinatesService implements ICoordinatesService
{
	public function __construct(private array $coordinatesRepositories)
	{
	}

	public function getCoordinatesByQuery(string $query): Coordinates
	{
		foreach ($this->coordinatesRepositories as $coordinatesRepository) {
			assert($coordinatesRepository instanceof ICoordinatesRepository);

			$result = $coordinatesRepository->getCoordinatesByQuery($query);

			if ($result instanceof Coordinates) {
				return $result;
			}
		}

		throw new NotFoundException(
			sprintf('Coordinates by query (%s) were not found', $query),
			IErrorCode::COORDINATES_WERE_NOT_FOUND
		);
	}
}
