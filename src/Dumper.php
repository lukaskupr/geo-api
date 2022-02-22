<?php
declare(strict_types=1);

use Spiral\Debug\Dumper;

function debug(mixed $val, int $target = Dumper::ERROR_LOG): void
{
	(new Dumper())->dump($val, $target);
}
