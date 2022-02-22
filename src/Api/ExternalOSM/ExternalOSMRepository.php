<?php
declare(strict_types=1);

namespace App\Api\ExternalOSM;

use App\Api\ExternalOSM\Request\SearchCoordinatesRequest;
use App\Api\IApiClient;
use App\Entity\Coordinates;
use App\Exception\ApiConnectionException;
use App\Repository\ICoordinatesRepository;

class ExternalOSMRepository implements ICoordinatesRepository
{
	public function __construct(private IApiClient $apiClient)
	{
	}

	public function getCoordinatesByQuery(string $query): ?Coordinates
	{
		$apiRequest = new SearchCoordinatesRequest($query);

		try {
			$result = $this->apiClient->get($apiRequest)->getBody() ?? [];
			$location = $result[0] ?? null;

			return $location !== null && isset($location['lat']) && isset($location['lon'])
				? new Coordinates($location['lat'], $location['lon'])
				: null;
		} catch (ApiConnectionException) {
			// Error already logged, just return null

			return null;
		}
	}
}
