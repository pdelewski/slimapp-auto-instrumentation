<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Psr\Log\LoggerInterface;

function calculateQuote($jsonObject): float
{
    $quote = 0.0;
    try {
        $numberOfItems = 5; //intval($jsonObject['numberOfItems']);
        $quote = 8.90 * $numberOfItems;

    } catch (\Exception $exception) {
    } finally {
        return $quote;
    }
}
class Processor {
    public static function processRequest(Request $request, Response $response) {
        $body = $request->getBody()->getContents();
        $jsonObject = json_decode($body, true);

        // $data = calculateQuote($jsonObject);
	    $data = 44;
        $payload = json_encode($data);
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
return function (App $app) {
    $container = $app->getContainer();
    $app->get('/getquote', ['Processor','processRequest']);
};
