<?php
declare(strict_types=1);

use App\Service\Log\IRequestLogger;
use App\Common\ITimeMeasuring;
use App\Configuration\IEnvironmentType;
use App\Controller\StatusController;
use App\Controller\V1\Search\CoordinatesController;
use App\DI\ContainerFactory;
use App\DI\Definitions;
use App\Error\ErrorTrigger;
use App\Middleware\QueryMiddleware;
use App\Middleware\ReadHeaderParamsMiddleware;
use App\Middleware\RequestStoreMiddleware;
use App\Middleware\UncatchedExceptionHandler;
use App\Service\Flusher\IFlusher;
use App\Service\Log\IEventLog;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\AppFactory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;

require __DIR__ . '/Dumper.php';

$container = null;
/** @var IEnvironmentType|null $environmentType */
$environmentType = null;
$psr17Factory = new Psr17Factory();

// Create container & app
try {
	$container = (new ContainerFactory())->create();
	$environmentType = $container->get(IEnvironmentType::class);
} catch (Throwable $e) {
	ErrorTrigger::fire($e);
}

$app = AppFactory::create($psr17Factory, $container);

/** @see StatusController::__invoke() */
$app->get('/status', StatusController::class);

$app->get('/v1/search/coordinates', CoordinatesController::class)
	->add(QueryMiddleware::class)
	->add(ReadHeaderParamsMiddleware::class);

assert($environmentType instanceof IEnvironmentType || $environmentType === null);
assert_options(ASSERT_ACTIVE, ($environmentType?->isProd() ?? false) ? 0 : 1); // disable on production
$errorMiddleware = $app->addErrorMiddleware($environmentType?->isDev() ?? false, true, true);
$errorMiddleware->setDefaultErrorHandler(UncatchedExceptionHandler::class);

$app->add(RequestStoreMiddleware::class); // Should be the last processed middleware

$app->addRoutingMiddleware();

/** @var IEventLog $eventLog */
$eventLog = null;
/** @var ITimeMeasuring $timeMeasuring */
$timeMeasuring = null;
/** @var IRequestLogger $requestLogger */
$requestLogger = null;
/** @var IFlusher $flusher */
$flusher = null;

try {
	assert($container instanceof Container);
	$eventLog = $container->get(IEventLog::class);
	$timeMeasuring = $container->get(Definitions::WORKER_PROCESSING_DURATION);
	$requestLogger = $container->get(IRequestLogger::class);
	$flusher = $container->get(IFlusher::class);
} catch (DependencyException|NotFoundException $e) {
	ErrorTrigger::fire($e);
}

assert($eventLog instanceof IEventLog);
assert($timeMeasuring instanceof ITimeMeasuring);
assert($requestLogger instanceof IRequestLogger);
assert($flusher instanceof IFlusher);

$worker = new PSR7Worker(Worker::create(), $psr17Factory, $psr17Factory, $psr17Factory);

while ($request = $worker->waitRequest()) {
	$timeMeasuring->start();
	$response = null;

	try {
		$response = $app->handle($request);
		$worker->respond($response);
	} catch (Throwable $e) {
		try {
			$responseFactory = $container->get(ResponseFactoryInterface::class);
			assert($responseFactory instanceof ResponseFactoryInterface);
			// try to respond in a standardized format
			$handler = new UncatchedExceptionHandler($responseFactory, $eventLog);

			$response = $handler($request, $e, (bool) $environmentType?->isDev(), true, true);
			$worker->respond($response);
		} catch (Throwable $ex) {
			$worker->getWorker()->error((string) $e);
		}
	} finally {
		$requestLogger->log($response, $timeMeasuring->finish());
		$flusher->flush();

		gc_collect_cycles();
	}
}
