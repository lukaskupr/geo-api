<?php
declare(strict_types=1);

namespace App\Common;

interface IHttpMethod
{
	const METHOD_POST = 'POST';

	const METHOD_PUT = 'PUT';

	const METHOD_GET = 'GET';

	const METHOD_DELETE = 'DELETE';
}
