<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Exception\BadRequestException;
use App\Exception\IErrorCode;
use App\Exception\ValidatorException;
use App\Validator\StringValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class QueryMiddleware
{
	public const PARAM_QUERY = 'query';

	public function __invoke(
		ServerRequestInterface $request,
		RequestHandlerInterface $requestHandler
	): ResponseInterface {
		$query = $request->getQueryParams()[self::PARAM_QUERY] ?? null;

		try {
			(new StringValidator($query))->validate();
		} catch (ValidatorException $ex) {
			throw new BadRequestException(
				'Invalid query parameter: ' . $ex->getMessage(),
				IErrorCode::INVALID_QUERY_PARAMETER,
				$ex
			);
		}

		$request = $request->withAttribute(self::PARAM_QUERY, $query);

		return $requestHandler->handle($request);
	}
}
