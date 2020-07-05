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

		public function mapUsers(Request $request, Response $response, array $args): Response
		{
				$view = $this->container->get("view");
				if($request->getMethod() == "GET") {
						$db = $this->container->get("db");
						$userDao = new UserDAO($db);
						$users = $userDao->findAllUsers();

						$response = $view->render($response, "admin/users/index.html",['users' => $users]);
						return $response;
				}
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
