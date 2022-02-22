<?php
declare(strict_types=1);

namespace App\Api;

/**
 * Generic interface to be used for all API client requests.
 */
interface IRequest
{
	public function getUrl(): string;

	public function getBody(): array;

	public function getHeaders(): array;
}
