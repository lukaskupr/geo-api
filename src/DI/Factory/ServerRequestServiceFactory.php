<?php
declare(strict_types=1);

namespace App\DI\Factory;

use App\Service\Flusher\IFlusher;
use App\Service\ServerRequest\ServerRequestService;

class ServerRequestServiceFactory
{
	public function create(IFlusher $flusher): ServerRequestService
	{
		$serverRequestService = new ServerRequestService();

		$flusher->register($serverRequestService);

		return $serverRequestService;
	}
}
