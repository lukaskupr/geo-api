<?php
declare(strict_types=1);

namespace App\Common;

use Psr\Http\Message\ServerRequestInterface as Request;

interface IHeader
{
	const CONTENT_TYPE = 'Content-Type';

	const CONTENT_TYPE_JSON = 'application/json';

	const USER_AGENT = 'user-agent';

	public function getValue(Request $request, string $key, bool $required = true): string;
}
