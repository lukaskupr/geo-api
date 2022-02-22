<?php
declare(strict_types=1);

namespace App\Service\Log;

use Psr\Http\Message\ResponseInterface;

interface IRequestLogger
{
	public function log(?ResponseInterface $response, float $workerDuration): void;
}
