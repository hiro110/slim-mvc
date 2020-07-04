<?php
namespace CamtemSlim\MVC\middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

use CamtemSlim\MVC\daos\UserDAO;

class LoggedInCheck implements MiddlewareInterface
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

        $db = $this->container->get("db");
        $userDao = new UserDAO($db);
        $user = $userDao->findByPk($_SESSION['user']['id']);

        if($user){
            $response = $handler->handle($request);
        } else {
            $response = $handler->handle($request);
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        return $response;
    }
}
