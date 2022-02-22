<?php
declare(strict_types=1);

namespace App\DI\Factory;

use App\Api\ExternalOSM\ExternalOSMRepository;
use App\Repository\ICoordinatesRepository;
use App\DB\Nominatim\NominatimRepository;
use App\Service\CoordinatesService;

class CoordinatesServiceFactory
{
	public function create(
		ExternalOSMRepository $externalOSMRepository,
		NominatimRepository $nominatimDbRepository
	): CoordinatesService {
		assert($externalOSMRepository instanceof ICoordinatesRepository);
		assert($nominatimDbRepository instanceof ICoordinatesRepository);

		return new CoordinatesService(
			[
				$nominatimDbRepository,
				$externalOSMRepository,
			],
		);
	}
}
