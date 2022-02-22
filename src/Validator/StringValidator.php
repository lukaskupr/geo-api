<?php
declare(strict_types=1);

namespace App\Validator;

use App\Exception\IErrorCode;
use App\Exception\ValidatorException;

class StringValidator extends BaseValidator
{
	public function validate(): void
	{
		if (is_string($this->value)) {
			return;
		}

		throw new ValidatorException('Invalid string value', IErrorCode::INVALID_STRING_VALUE);
	}
}
