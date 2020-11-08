<?php
namespace App\Middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Container\ContainerInterface;
use Slim\Routing\RouteContext;

use App\Models\FormGroup;

class CheckedUriMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $uri = $routeContext->getRoute()->getArgument('uri');
        $msg = "";

        $now = date("Y-m-d H:i:s");
        $fg = FormGroup::where('base_uri', $uri)
                ->where('is_active', 1)
                ->where('publishing_start', '<=', $now)
                ->where('publishing_end', '>=', $now)
                ->first();

        if($fg){
            $request = $request->withAttribute('form_group', $fg);
            $response = $handler->handle($request);
        } else {
            $response = $handler->handle($request);
            $msg = "Not found form";
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $response;
    }
}
