<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Psr\Log\LoggerInterface;

function helloWorld() {
    var_dump('HELLO');
}

return function (App $app) {
    $container = $app->getContainer();
    $app->options('/{routes:.*}', function (Request $request, Response $response) use ($container) {
        // CORS Pre-Flight OPTIONS Request Handler
        $logger = $container->get(LoggerInterface::class);
        $logger->info('options');    
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) use ($container) {
	$logger = $container->get(LoggerInterface::class);
	$logger->info('get');
        helloWorld();
        $response->getBody()->write('Hello world Sumo!');
        return $response;
    });

    $app->group('/users', function (Group $group) use ($container) {
        $logger = $container->get(LoggerInterface::class);
        $logger->info('group');
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
