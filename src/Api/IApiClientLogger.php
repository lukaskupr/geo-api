<?php
declare(strict_types=1);

namespace App\Api;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\ResponseInterface;

interface IApiClientLogger
{
	public function logResult(
		string $baseUrl,
		string $url,
		string $method,
		float $duration,
		array $requestOptions,
		?string $requestBody,
		?ResponseInterface $response,
	): void;

	public function logConnectionError(ConnectException $ex): void;

	public function logRequestError(RequestException $ex): void;

	public function logTransferError(TransferException $ex, string $url): void;
}
