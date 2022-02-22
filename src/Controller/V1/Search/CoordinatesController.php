<?php
declare(strict_types=1);

namespace App\Controller\V1\Search;

use App\Controller\BaseController;
use App\Controller\Invokable;
use App\Middleware\QueryMiddleware;
use App\Response\OutputProcessor\V1\CoordinatesOutput;
use App\Service\ICoordinatesService;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class CoordinatesController extends BaseController implements Invokable
{
	public function __construct(private ICoordinatesService $coordinatesService)
	{

	}

	public function __invoke(ServerRequest $request, ResponseInterface $response): ResponseInterface
	{
		$query = strval($request->getAttribute(QueryMiddleware::PARAM_QUERY));

		return $this->sendJson(
			$response,
			new CoordinatesOutput($this->coordinatesService->getCoordinatesByQuery($query))
		);
	}
}
