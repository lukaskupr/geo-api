<?php
declare(strict_types=1);

namespace App\DI\Factory;

use App\Common\UuidProvider;
use App\Service\Flusher\IFlusher;

class UuidProviderFactory
{
	public function create(IFlusher $flusher): UuidProvider
	{
		$uuidProvider = new UuidProvider();
		$flusher->register($uuidProvider);

		return $uuidProvider;
	}
}
