<?php
namespace CamtemSlim\MVC\controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use CamtemSlim\MVC\entities;
use CamtemSlim\MVC\daos;

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

			$view = $this->container->get("view");
			if($request->getMethod() == "GET") {
				// $cname = $request->getAttribute('csrf_name');
				// $cvalue = $request->getAttribute('csrf_value');
				// $response = $view->render($response, "admin/login.html", ['csrf_name' => $cname, 'csrf_value' =>$cvalue]);
				$response = $view->render($response, "admin/login.html");
				return $response;
			}
			$params = $request->getParsedBody();
			$username = isset($params["username"]) ? $params["username"]: null;
			$password = isset($params["password"]) ? $params["password"]: null;

			if(!$username || !$password){
				$msg = "invalid username or password";
				$response = $view->render($response, "admin/login.html",['msg' => $msg]);
				return $response;
			}

			try {
				$db = $this->container->get("db");
				// $userDao = new UserDAO($db);
				// $res = $userDao->findByUser($username, $password);
				$sql="select * from users where username = :username and is_active = :is_active";
				$stmt = $db->prepare($sql);
				$stmt->bindValue(":username", $username, PDO::PARAM_STR);
				$stmt->bindValue(":is_active", 1, PDO::PARAM_INT);
				$res = $stmt->execute();

				if ($res && $row = $stmt->fetch()) {
					if (!password_verify($password, $row["password"])) {
						$msg = "invalid username or password";
						$response = $view->render($response, "admin/login.html",['msg' => $msg]);
						return $response;
					}

					$_SESSION['user'] = [
						'id' => $row["id"],
						'username' => $row["username"],
						'role' => $row["role"]
					];
    		}
			}
			catch(PDOException $ex) {
				var_dump($ex->getMessage());
			}
			finally {
				$db = null;
			}

			return $response->withHeader('Location', '/admin')->withStatus(302);
    }
}
