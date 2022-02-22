<?php
declare(strict_types=1);

namespace App\Configuration;

interface IConfig
{
	public function getEnvironmentType(): string;

	public function getProjectId(): string;

	public function getExternalOsmUrl(): string;

	public function getExternalOsmTimeout(): int;
}
