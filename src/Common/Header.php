<?php
declare(strict_types=1);

namespace App\Common;

use App\Exception\BadRequestException;
use App\Exception\IErrorCode;
use Psr\Http\Message\ServerRequestInterface as Request;

class Header implements IHeader
{
	private const KEY_TO_ERROR_CODE_MAP = [
		IHeader::USER_AGENT => IErrorCode::MISSING_HEADER_USER_AGENT,
	];

	public function getValue(Request $request, string $key, bool $required = true): string
	{
		$value = $request->getHeader($key)[0] ?? null;

		$required && $value === null && throw new BadRequestException(
			sprintf('Missing required header %s', $key),
			self::KEY_TO_ERROR_CODE_MAP[$key] ?? 0
		);

		return (string) $value;
	}
}
