<?php
declare(strict_types=1);

namespace App\DI\Factory;

use App\Service\Flusher\IFlusher;
use App\Service\ServerRequest\ServerRequestHandler;

class ServerRequestHandlerFactory
{
	public function create(IFlusher $flusher): ServerRequestHandler
	{
		$handler = new ServerRequestHandler();

		$flusher->register($handler);

		return $handler;
	}
}
