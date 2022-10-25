<?php
declare(strict_types=1);

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\SDK\Trace\Tracer;
use OpenTelemetry\SDK\Trace\TracerProviderFactory;
use OpenTelemetry\SDK\Common\Util\ShutdownHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Routing\RouteContext;
use Psr\Log\LoggerInterface;
use OpenTelemetry\Contrib\Jaeger\Exporter as JaegerExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

$exporter = JaegerExporter::fromConnectionString('http://localhost:9412/api/v2/spans', 'QuoteService AutoInstrumentation');
$tracerProvider = new TracerProvider(
    new SimpleSpanProcessor($exporter)
);

// $tracerProvider =  new TracerProvider(
//     new SimpleSpanProcessor(
// 	new LoggerExporter("hooker", $logger)
//     )
// );

$scope = \OpenTelemetry\API\Common\Instrumentation\Configurator::create()
    ->withTracerProvider($tracerProvider)
    ->activate();

// Add OTel
//$tracerProvider = (new TracerProviderFactory('quoteservice'))->create();
$exporter = JaegerExporter::fromConnectionString('http://localhost:9412/api/v2/spans', 'QuoteService automatically instrumented');
$tracerProvider = new TracerProvider(
    new SimpleSpanProcessor($exporter)
);

//$containerBuilder->addDefinitions([
//    Tracer::class => $tracer
//]);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = Bridge::create($container);
// Register middleware
//middleware starts root span based on route pattern, sets status from http code
$app->add(function (Request $request, RequestHandler $handler) use ($container) {
    $logger = $container->get(LoggerInterface::class);
    $logger->info('add');

    try {
        $response = $handler->handle($request);
    } finally {
    }

    return $response;
});
$app->addRoutingMiddleware();

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Run App
$app->run();
$scope->detach();
$tracerProvider->shutdown();

