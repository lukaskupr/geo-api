<?php
declare(strict_types=1);

namespace App\Service\ServerRequest;

use App\Service\Flusher\IFlushable;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteContext;

class ServerRequestService implements IServerRequestService, IFlushable
{
	private ?array $parsedRequestBody = null;

	public function getRoutePattern(ServerRequestInterface $serverRequest): ?string
	{
		$route = $this->getRoute($serverRequest);

		return $route?->getPattern();
	}

	public function getRouteArgument(ServerRequestInterface $serverRequest, string $name): ?string
	{
		$route = $this->getRoute($serverRequest);

		return $route?->getArgument($name);
	}

	private function getRoute(ServerRequestInterface $serverRequest): ?RouteInterface
	{
		$routeContext = RouteContext::fromRequest($serverRequest);

		return $routeContext->getRoute();
	}

	public function getBody(ServerRequestInterface $serverRequest): ?array
	{
		if ($this->parsedRequestBody !== null) {
			return $this->parsedRequestBody;
		}

		$body = (string) $serverRequest->getBody();
		$parsedRequestBody = $body !== '' ? json_decode($body, true) : null;

		$this->parsedRequestBody = is_array($parsedRequestBody) ? $parsedRequestBody : null;

		return $this->parsedRequestBody;
	}

	public function getArgument(ServerRequestInterface $serverRequest, string $name): mixed
	{
		$data = $this->getBody($serverRequest);

		return $data[$name] ?? null;
	}

	public function flush(): void
	{
		$this->parsedRequestBody = null;
	}
}
