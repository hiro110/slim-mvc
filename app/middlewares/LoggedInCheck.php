<?php
namespace CamtemSlim\MVC\middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use CamtemSlim\MVC\daos\UserDAO;

// class LoggedInCheckMiddleware implements MiddlewareInterface
class LoggedInCheck implements MiddlewareInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
      $this->container = $container;
    }

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
      if (!array_key_exists('user', $_SESSION)) {
            $response = $handler->handle($request);
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }
        $db = $this->container->get("db");
        $userDao = new UserDAO($db);
        $user = $userDao->findByPk($_SESSION['user']['id']);

        // $sql="select * from users where id = :id and is_active = :is_active";
        // $stmt = $this->db->prepare($sql);
        // $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        // $stmt->bindValue(":is_active", $is_active, PDO::PARAM_INT);
        // $user = $stmt->execute();

        if($user){
            $response = $handler->handle($request);
        } else {
            $response = $handler->handle($request);
            return $response->withHeader('Location', '/admin/login')->withStatus(302);
        }

        return $response;
    }

    // public function process(Request $request, RequestHandler $handler): Response
    // {
    //     if (!array_key_exists('user', $_SESSION)) {
    //         $response = $handler->handle($request);
    //         return $response->withHeader('Location', '/admin/login')->withStatus(302);
    //     }
    //     $db = $this->container->get("db");
    //     $userDao = new UserDAO($db);
    //     $user = $userDao->findByUser($_SESSION['user']['id']);

    //     // $sql="select * from users where id = :id and is_active = :is_active";
    //     // $stmt = $this->db->prepare($sql);
    //     // $stmt->bindValue(":username", $username, PDO::PARAM_STR);
    //     // $stmt->bindValue(":is_active", $is_active, PDO::PARAM_INT);
    //     // $user = $stmt->execute();

    //     if($user){
    //         $response = $handler->handle($request);
    //     } else {
    //         $response = $handler->handle($request);
    //         return $response->withHeader('Location', '/admin/login')->withStatus(302);
    //     }

    //     return $response;
    // }
}
