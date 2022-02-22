<?php
declare(strict_types=1);

namespace App\Api;

/**
 * Responses from API client are stored in instances of this interface.
 */
interface IResponse
{
	public function getHttpCode(): int;

	public function getContentType(): string;

	public function getBody(): ?array;
}
