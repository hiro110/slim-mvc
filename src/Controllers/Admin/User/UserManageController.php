<?php
namespace App\Controllers\Admin\User;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use App\Entities;
use App\Daos\UserDAO;

class UserManageController
{
		private $container;

	// コンストラクタ
	public function __construct(ContainerInterface $container)
	{
				$this->container = $container;
	}

		public function getUsers(Request $request, Response $response, array $args): Response
		{
				$view = $this->container->get("view");
				$db = $this->container->get("db");

				$userDao = new UserDAO($db);
				$users = $userDao->findAllUsers();

				$response = $view->render($response, "admin/users/index.html",['users' => $users, 'session_role' => $_SESSION['user']['role']]);
				return $response;

		}

		public function mapUsersAdd(Request $request, Response $response, array $args): Response
		{
				$view = $this->container->get("view");

				if ($request->getMethod() == "GET") {
						$response = $view->render($response, "admin/users/add.html",['users' => $users]);
						return $response;
				}

				$params = $request->getParsedBody();
				$username = isset($params["username"]) ? $params["username"]: null;
				$password = isset($params["password"]) ? $params["password"]: null;
				$role = isset($params["role"]) ? intVal($params["role"]): null;

				$msg = "";
				try {
						$db = $this->container->get("db");
						$userDao = new UserDAO($db);
						$res = $userDao->addUser($username, $password, $role);

						if (!$res) {
								$msg = "Failes add user";
						}

				} catch(PDOException $ex) {
						var_dump($ex->getMessage());
				} finally {
						// DB切断。
						$db = null;
				}

				$response = $view->render($response, "admin/users/add.html",['msg' => $msg]);
				return $response;

		}

		public function mapUsersId(Request $request, Response $response, array $args): Response
		{
				$view = $this->container->get("view");
				switch ($request->getMethod()) {
						case 'GET':
							break;

						case 'POST':
							# code...
							break;

						case 'PUT':
							# code...
							break;

						case 'DELETE':
							# code...
							break;

						default:
							# code...
							break;
				}
		}
}
