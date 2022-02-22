<?php
declare(strict_types=1);

namespace App\Configuration;

interface IEnvironmentType
{
	public function isProd(): bool;

	public function isDev(): bool;
}
