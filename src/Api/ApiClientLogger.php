<?php
declare(strict_types=1);

namespace App\Api;

use App\Common\IHttpStatus;
use App\Service\Log\IEventLog;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiClientLogger implements IApiClientLogger
{
	const API_CLIENT_CALL_ERROR = 'api_client_call_error';

	const API_CLIENT_CALL_REQUEST = 'api_request_result';

	public function __construct(private IEventLog $eventLog)
	{
	}

	public function logResult(
		string $baseUrl,
		string $url,
		string $method,
		float $duration,
		array $requestOptions,
		?string $requestBody,
		?ResponseInterface $response,
	): void {
		unset($requestOptions[RequestOptions::HEADERS]['Authorization'], $requestOptions[RequestOptions::BODY]);

		$this->eventLog->info(
			self::API_CLIENT_CALL_REQUEST,
			[
				'method' => $method,
				'baseUrl' => $baseUrl,
				'path' => str_replace($baseUrl, '', $url),
				'duration' => $duration,
				'httpCode' => $response?->getStatusCode(),
				'requestOptions' => json_encode($requestOptions),
				'requestBody' => $requestBody,
				'responseBody' => $response?->getBody()->getContents(),
			]
		);
	}

	public function logConnectionError(ConnectException $ex): void
	{
		$this->logApiError($ex, (string) $ex->getRequest()->getUri(), $ex->getRequest());
	}

	public function logRequestError(RequestException $ex): void
	{
		$this->logApiError($ex, (string) $ex->getRequest()->getUri(), $ex->getRequest(), $ex->getResponse());
	}

	public function logTransferError(TransferException $ex, string $url): void
	{
		$this->logApiError($ex, $url);
	}

	private function logApiError(
		TransferException $ex,
		string $url,
		?RequestInterface $request = null,
		?ResponseInterface $response = null,
	): void {
		$data = [
			'message' => $ex->getMessage(),
			'exceptionClass' => get_class($ex),
			'httpCode' => $response?->getStatusCode(),
			'responseBody' => $response?->getBody()->getContents(),
			'method' => $request?->getMethod(),
			'url' => $url,
			'requestBody' => $request?->getBody()->getContents(),
		];

		$this->isWarning($response)
			? $this->eventLog->warning(self::API_CLIENT_CALL_ERROR, $data)
			: $this->eventLog->error(self::API_CLIENT_CALL_ERROR, $data);
	}

	private function isWarning(?ResponseInterface $response = null): bool
	{
		return $response !== null
			&& $response->getStatusCode() >= IHttpStatus::BAD_REQUEST
			&& $response->getStatusCode() < IHttpStatus::INTERNAL_SERVER_ERROR;
	}
}
