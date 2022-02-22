<?php
declare(strict_types=1);

namespace App\Service\ServerRequest;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Store and return server request. The main purpose is to use it for logging request's data.
 * Without this we are not able to read the current route pattern in a request logger class.
 */
interface IServerRequestHandler
{
	public function getRequest(): ?ServerRequestInterface;

	public function setRequest(ServerRequestInterface $serverRequest): void;
}
