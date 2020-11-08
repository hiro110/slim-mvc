<?php
namespace App\Middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

use App\Models\User;

class LoggedInCheckMiddleware implements MiddlewareInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!array_key_exists('user', $_SESSION)) {
            $response = $handler->handle($request);
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        $user = User::where('id', $_SESSION['user']['id'])
                        ->where('is_active', 1)
                        ->first();

        if($user){
            $response = $handler->handle($request);
        } else {
            $response = $handler->handle($request);
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        return $response;
    }
}
