<?php
declare(strict_types=1);

namespace App\Controller;

use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;

interface Invokable
{
	public function __invoke(ServerRequest $request, Response $response): Response;
}
