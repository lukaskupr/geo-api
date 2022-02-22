<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Common\IHeader;

use App\Common\IHttpStatus;
use App\Exception\IErrorCode;
use App\Service\Log\IEventLog;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class UncatchedExceptionHandler implements ErrorHandlerInterface
{
	private const EVENT_NAME = 'uncatched_exception';

	public function __construct(private ResponseFactoryInterface $responseFactory, private IEventLog $eventLog)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function __invoke(
		ServerRequestInterface $request,
		Throwable $exception,
		bool $displayErrorDetails,
		bool $logErrors,
		bool $logErrorDetails
	): ResponseInterface {
		$responseData = [
			'message' => $exception->getMessage(),
			'code' => $exception->getCode(),
		];

		$logData = $this->buildLogData($request, $exception);
		$this->eventLog->critical(self::EVENT_NAME, $logData);

		$displayErrorDetails && $responseData = array_merge($responseData, $logData);

		$httpStatus = $exception->getCode() && isset(IErrorCode::CODE_TO_STATUS_MAPPING[$exception->getCode()])
			? IErrorCode::CODE_TO_STATUS_MAPPING[$exception->getCode()]
			: IHttpStatus::INTERNAL_SERVER_ERROR;

		$response = $this->responseFactory->createResponse($httpStatus);

		$response->getBody()->write((string) json_encode($responseData));

		return $response->withHeader(IHeader::CONTENT_TYPE, IHeader::CONTENT_TYPE_JSON);
	}

	private function buildLogData(ServerRequestInterface $request, Throwable $exception): array
	{
		$logData = [
			'message' => $exception->getMessage(),
			'code' => $exception->getCode(),
			'exceptionClass' => get_class($exception),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'stackTrace' => $exception->getTraceAsString(),
			'path' => $request->getUri()->getPath(),
		];

		// recursively add previous exceptions to log
		($previous = $exception->getPrevious()) instanceof Throwable
		&& $logData['previousException'] = $this->buildLogData($request, $previous);

		return $logData;
	}
}
