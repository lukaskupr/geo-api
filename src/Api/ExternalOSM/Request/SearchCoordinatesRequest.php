<?php
declare(strict_types=1);

namespace App\Api\ExternalOSM\Request;

use App\Api\IRequest;

class SearchCoordinatesRequest implements IRequest
{
	private const URL = '/search.php?addressdetails=1&format=json&limit=1&q=%s';

	public function __construct(private string $query)
	{
	}

	public function getUrl(): string
	{
		return sprintf(self::URL, $this->query);
	}

	public function getBody(): array
	{
		return [];
	}

	public function getHeaders(): array
	{
		return [];
	}
}
