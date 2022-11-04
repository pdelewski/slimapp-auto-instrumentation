<?php
declare(strict_types=1);

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
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

$scope = \OpenTelemetry\API\Common\Instrumentation\Configurator::create()
     ->withTracerProvider($tracerProvider)
     ->activate();

function shutdown($scope, $tracerProvider) {
    $scope->detach();
    $tracerProvider->shutdown();
}

register_shutdown_function('shutdown', $scope, $tracerProvider);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = Bridge::create($container);

class Processor1 {
public static function processRequest(Request $request, RequestHandler $handler) {

    try {
        $response = $handler->handle($request);
    } finally {
    }

    return $response;
}
}
// Register middleware
//middleware starts root span based on route pattern, sets status from http code
$app->add(['Processor1','processRequest']);
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

