<?php
declare(strict_types=1);

namespace App\Api;

use App\Common\IHeader;
use App\Exception\IErrorCode;
use App\Exception\InvalidDataProvidedException;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Utils;

class Response implements IResponse
{
	private ?array $body = null;

	private string $contentType;

	public function __construct(
		private int $httpCode,
		?string $body,
		string $contentType,
	) {
		$this->setContentType($contentType);
		$this->setBody($body);
	}

	public function getHttpCode(): int
	{
		return $this->httpCode;
	}

	public function getContentType(): string
	{
		return $this->contentType;
	}

	public function getBody(): ?array
	{
		return $this->body;
	}

	private function setContentType(string $contentType): void
	{
		// explode in case of "application/json; charset=utf-8" provided
		$this->contentType = trim(explode(';', $contentType)[0]);
	}

	private function setBody(?string $body): void
	{
		if (empty($body)) {
			return;
		}

		if (strtolower($this->contentType) !== IHeader::CONTENT_TYPE_JSON) {
			throw new InvalidDataProvidedException(
				sprintf('Invalid API response content type (%s)', $this->contentType),
				IErrorCode::UNSUPPORTED_CONTENT_TYPE
			);
		}

		try {
			$this->body = (array) Utils::jsonDecode($body, true);
		} catch (InvalidArgumentException $ex) {
			throw new InvalidDataProvidedException(
				sprintf('Invalid JSON format (%s)', $body),
				IErrorCode::INVALID_JSON_RESPONSE,
				$ex
			);
		}
	}
}
