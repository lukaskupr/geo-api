<?php
declare(strict_types=1);

namespace App\Service\Log;

use App\Common\IHeader;
use App\Service\ServerRequest\IServerRequestHandler;
use App\Service\ServerRequest\IServerRequestService;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestLogger implements IRequestLogger
{
	private const EVENT_NAME = 'request_log';

	public function __construct(
		private IEventLog $eventLog,
		private IServerRequestService $serverRequestService,
		private IServerRequestHandler $serverRequestHandler,
	) {
	}

	public function log(?ResponseInterface $response, float $workerDuration): void
	{
		$request = $this->serverRequestHandler->getRequest();
		$totalDuration = $this->getTotalDuration();
		$processingDuration = $workerDuration;
		$requestMethod = $request?->getMethod() ?? $_SERVER['REQUEST_METHOD'];
		$routePattern = $request ? $this->serverRequestService->getRoutePattern($request) : null;
		$userAgent = $request?->getHeader(IHeader::USER_AGENT)[0];
		$responseCode = $response?->getStatusCode();

		$this->logEvent(
			$requestMethod,
			$totalDuration,
			$processingDuration,
			$this->serverRequestHandler->getRequest(),
			$response,
			$responseCode,
			$routePattern,
			$userAgent
		);
	}

	private function logEvent(
		string $method,
		float $totalDuration,
		float $processingDuration,
		?ServerRequestInterface $request,
		?ResponseInterface $response,
		?int $responseCode,
		?string $routePattern,
		?string $userAgent,
	): void {
		$this->eventLog->info(
			self::EVENT_NAME,
			[
				'httpCode' => $responseCode,
				'totalTime' => $totalDuration,
				'processingTime' => $processingDuration,
				'path' => $request?->getUri()->getPath() ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
				'pattern' => $routePattern,
				'query' => $request?->getUri()->getQuery() ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY),
				'method' => $method,
				'userAgent' => $userAgent,
				'requestBody' => (string) $request?->getBody(),
				'responseBody' => (string) $response?->getBody(),
				'headers' => $request
					? Utils::jsonEncode($request->getHeaders())
					: null,
			]
		);
	}

	/**
	 * Total time
	 *  - client is waiting in the roadrunner's queue
	 *  - worker code processing
	 */
	private function getTotalDuration(): float
	{
		return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
	}
}
