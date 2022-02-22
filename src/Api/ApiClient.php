<?php
declare(strict_types=1);

namespace App\Api;

use App\Common\IHeader;
use App\Common\IHttpMethod;
use App\Common\ITimeMeasuring;
use App\Configuration\IConfig;
use App\Exception\ApiConnectionException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiClient implements IApiClient
{
	private const DEFAULT_TIMEOUT = 3000;

	private array $headers = [];

	public function __construct(
		IConfig $appConfig,
		private IApiClientLogger $apiClientLogger,
		private GuzzleClient $client,
		private IApiClientConfig $config,
		private ITimeMeasuring $timeMeasuring,
	) {
		$this->headers[IHeader::USER_AGENT] = sprintf('%s/1.0.0', $appConfig->getProjectId());
	}

	public function get(IRequest $request): IResponse
	{
		return $this->call(IHttpMethod::METHOD_GET, $request);
	}

	public function post(IRequest $request): IResponse
	{
		return $this->call(IHttpMethod::METHOD_POST, $request);
	}

	public function put(IRequest $request): IResponse
	{
		return $this->call(IHttpMethod::METHOD_PUT, $request);
	}

	public function delete(IRequest $request): IResponse
	{
		return $this->call(IHttpMethod::METHOD_DELETE, $request);
	}

	private function call(string $method, IRequest $request): IResponse
	{
		$this->timeMeasuring->start();

		$baseUrl = $this->config->getBaseUrl();
		$url = $this->composeUrl($baseUrl, $request->getUrl());
		$request = new Request($method, $url, $this->buildHeaders($request), $this->buildBody($request));

		$options = $this->buildOptions();
		$response = null;
		try {
			$response = $this->callRequest($request, $options);
		} finally {
			$duration = $this->timeMeasuring->finish();
			$this->apiClientLogger->logResult(
				$this->config->getBaseUrl(),
				$this->decomposeUrl($this->config->getBaseUrl(), (string) $request->getUri()),
				$request->getMethod(),
				$duration,
				$options,
				(string) $request->getBody(),
				$response
			);
		}

		return new Response(
			$response->getStatusCode(),
			(string) $response->getBody(),
			$response->getHeaderLine(IHeader::CONTENT_TYPE)
		);
	}

	/**
	 * @throws ApiConnectionException
	 */
	private function callRequest(RequestInterface $request, array $options): ResponseInterface
	{
		try {
			return $this->client->send($request, $options);
		} catch (ConnectException $ex) { // networking error
			$this->apiClientLogger->logConnectionError($ex);
			throw new ApiConnectionException(
				sprintf('Unable to connect to API (%s, %s)', $this->config->getBaseUrl(), $ex->getMessage()),
				0,
				$ex
			);
		} catch (BadResponseException $ex) { // 4xx, 5xx responses
			$this->apiClientLogger->logRequestError($ex);

			return $ex->getResponse();
		} catch (RequestException $ex) { // connection timeout, DNS errors, ...
			$this->apiClientLogger->logRequestError($ex);
			throw new ApiConnectionException(
				sprintf('Unable to get data from API (%s, %s)', $this->config->getBaseUrl(), $ex->getMessage()),
				0,
				$ex
			);
		} catch (TransferException $ex) { // other errors
			$this->apiClientLogger->logTransferError($ex, (string) $request->getUri());
			throw new ApiConnectionException(
				sprintf('Unable to get data from API (%s, %s)', $this->config->getBaseUrl(), $ex->getMessage()),
				0,
				$ex
			);
		}
	}

	private function getTimeout(): float
	{
		// convert to seconds
		return ($this->config->getTimeout() ?: self::DEFAULT_TIMEOUT) / 1000.0;
	}

	private function composeUrl(string $baseUrl, string $path): string
	{
		return sprintf('%s%s', $baseUrl, $path);
	}

	private function buildHeaders(IRequest $request): array
	{
		return array_merge($this->headers, $request->getHeaders());
	}

	private function buildBody(IRequest $request): string
	{
		return Utils::jsonEncode($request->getBody());
	}

	private function buildOptions(): array
	{
		$options = [
			RequestOptions::TIMEOUT => $this->getTimeout(),
		];

		$this->config->getAuthToken() && $options[RequestOptions::HEADERS]['Authorization'] = sprintf(
			'Bearer %s',
			$this->config->getAuthToken()
		);

		return $options;
	}

	private function decomposeUrl(string $baseUrl, string $fullUrl): string
	{
		return str_replace($baseUrl, '', $fullUrl);
	}
}
