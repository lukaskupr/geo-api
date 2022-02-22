<?php
declare(strict_types=1);

namespace App\Controller;

use App\Common\IHeader;
use App\Common\IHttpStatus;
use App\Exception\IErrorCode;
use App\Exception\InvalidStateException;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface as Response;

class BaseController
{
	public function sendJson(Response $resp, JsonSerializable $data, int $status = IHttpStatus::OK): Response
	{
		$encodedData = json_encode($data);
		$encodedData === '[]' && $encodedData = '{}'; // we do not want to respond empty array but object

		if ($encodedData === false) {
//			$this->log->error('invalid_json_encode_data', [
//				'data' => serialize($data),
//			]);

			throw new InvalidStateException(
				'Failed to "json_encode" data',
				IErrorCode::INVALID_JSON_ENCODE_FORMAT
			);
		}

		$body = $resp->getBody();
		$body->write($encodedData);

		return $resp
			->withHeader(IHeader::CONTENT_TYPE, IHeader::CONTENT_TYPE_JSON)
			->withStatus($status)
			->withBody($body);
	}
}
