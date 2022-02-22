<?php
declare(strict_types=1);

namespace App\Api;

/**
 * All possible configurations of API client
 */
interface IApiClientConfig
{
	/**
	 * Base URL without trailing slash.
	 */
	public function getBaseUrl(): string;

	public function getAuthToken(): ?string;

	/**
	 * Timeout limit in milliseconds.
	 */
	public function getTimeout(): ?float;
}
