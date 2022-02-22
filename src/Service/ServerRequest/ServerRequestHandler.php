<?php
declare(strict_types=1);

namespace App\Service\ServerRequest;

use App\Service\Flusher\IFlushable;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestHandler implements IServerRequestHandler, IFlushable
{
	private ?ServerRequestInterface $serverRequest = null;

	public function getRequest(): ?ServerRequestInterface
	{
		return $this->serverRequest;
	}

	public function setRequest(ServerRequestInterface $serverRequest): void
	{
		$this->serverRequest = $serverRequest;
	}

	public function flush(): void
	{
		$this->serverRequest = null;
	}
}
