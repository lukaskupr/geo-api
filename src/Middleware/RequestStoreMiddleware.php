<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Service\ServerRequest\IServerRequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;

class RequestStoreMiddleware
{
	public function __construct(private IServerRequestHandler $serverRequestHandler)
	{
	}

	public function __invoke(Request $request, RequestHandler $requestHandler): Response
	{
		$response = $requestHandler->handle($request);
		$this->serverRequestHandler->setRequest($request);

		return $response;
	}
}
