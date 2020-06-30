<?php
namespace Slim\MVC\controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\MVC\entities;
use Slim\MVC\daos;

class AdminController
{
		private $container;

	// コンストラクタ
	public function __construct(ContainerInterface $container)
	{
			$this->container = $container;
	}

    public function getIndex(Request $request, Response $response, array $args): Response
    {
			$view = $this->container->get("view");
			$response = $view->render($response, "admin/index.html");
			return $response;
    }

    public function mapLogin(Request $request, Response $response, array $args): Response
    {
			if($request->getMethod() == "GET") {
				$cname = $request->getAttribute('csrf_name');
				$cvalue = $request->getAttribute('csrf_value');

				$view = $this->container->get("view");
				$response = $view->render($response, "admin/login.html", ['csrf_name' => $cname, 'csrf_value' =>$cvalue]);
				return $response;
			}
			$postParam = $request->getParsedBody();
			$username = $postParam["username"];
			$password = $postParam["password"];

			try {
				$db = $this->container->get("db");
				$userDao = new UserDAO($db);
				$res = $userDao->findByUser($username, $password);
			}
			catch(PDOException $ex) {
				var_dump($ex);
			}
			finally {
				$db = null;
			}

			if ($res["result"]) {

				return $response->withHeader('Location', '/admin')->withStatus(302);
			}

    }
}
