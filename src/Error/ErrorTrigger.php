<?php
declare(strict_types=1);

namespace App\Error;

use Throwable;

class ErrorTrigger
{
	public static function fire(Throwable $e): void
	{
		$message = sprintf(
			'App Critical Error: Message: %s Trace: %s',
			$e->getMessage(),
			$e->getTraceAsString(),
		);

		trigger_error($message, E_USER_ERROR);
	}
}
