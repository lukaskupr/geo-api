<?php
declare(strict_types=1);

namespace App\Controller;

use App\Response\OutputProcessor\StatusOutput;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface as Response;

class StatusController extends BaseController implements Invokable
{
	public function __invoke(ServerRequest $request, Response $response): Response
	{
		$output = new StatusOutput;

		return $this->sendJson($response, $output);
	}
}
