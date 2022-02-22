<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Service\Log\IEventLog;
use App\Common\IHeader;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReadHeaderParamsMiddleware
{
	public function __construct(
		private IEventLog $eventLog,
		private IHeader $headerReader,
	) {
	}

	public function __invoke(Request $request, RequestHandler $requestHandler): Response
	{
		// user agent
		$userAgent = $this->headerReader->getValue($request, IHeader::USER_AGENT);
		$this->eventLog->addExtraValue('userAgent', $userAgent);

		return $requestHandler->handle($request);
	}
}
