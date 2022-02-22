<?php
declare(strict_types=1);

namespace App\Api;

interface IApiClient
{
	public function get(IRequest $request): IResponse;

	public function post(IRequest $request): IResponse;

	public function put(IRequest $request): IResponse;

	public function delete(IRequest $request): IResponse;
}
