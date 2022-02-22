<?php
declare(strict_types=1);

namespace App\Service\ServerRequest;

use Psr\Http\Message\ServerRequestInterface;

interface IServerRequestService
{
	public function getRoutePattern(ServerRequestInterface $serverRequest): ?string;

	public function getRouteArgument(ServerRequestInterface $serverRequest, string $name): ?string;

	public function getBody(ServerRequestInterface $serverRequest): ?array;

	public function getArgument(ServerRequestInterface $serverRequest, string $name): mixed;
}
