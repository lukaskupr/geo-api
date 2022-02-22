<?php
declare(strict_types=1);

namespace App\Validator;

class BaseValidator
{
	public function __construct(protected mixed $value)
	{
	}
}
