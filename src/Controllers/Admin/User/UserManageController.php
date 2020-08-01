<?php
namespace App\Controllers\Admin\User;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use App\Entities\Admin;
use App\Daos\Admin\UserDAO;

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
						$response = $view->render($response,
													"admin/users/edit.html",
													['roles' => UserDAO::ROLES]);
						return $response;
				}

				$params = $request->getParsedBody();
				$username = isset($params["username"]) ? $params["username"]: '';
				$password = isset($params["password"]) ? $params["password"]: '';
				$role = isset($params["role"]) ? intVal($params["role"]): 2;

				$msg = "";
				if ($username == '' || $password == '') {
						$msg = "invalid param";
						$response = $view->render($response,
													"admin/users/edit.html",
													[
															'msg' => $msg,
															'roles' => UserDAO::ROLES
													]);
						return $response;
				}

				try {
						$db = $this->container->get("db");
						$userDao = new UserDAO($db);
						$res = $userDao->addUser($username, $password, $role);

						if (!$res) {
								$msg = "Failed add user";
						}

				} catch(PDOException $ex) {
						var_dump($ex->getMessage());
				} finally {
						// DB切断。
						$db = null;
				}

				$response = $view->render($response, "admin/users/edit.html",['msg' => $msg]);
				return $response;

		}

		public function mapUsersId(Request $request, Response $response, array $args): Response
		{
				$view = $this->container->get("view");
				$user_id = intVal($args['id']);
				if ($request->getMethod() == "GET") {
						$msg = "";
						try {
								$db = $this->container->get("db");
								$userDao = new UserDAO($db);
								$user = $userDao->findByPk($user_id);
								// var_dump($user);
								if (!$user) {
										$msg = "Not found user";
								}
						} catch(PDOException $ex) {
								var_dump($ex->getMessage());
						} finally {
								// DB切断。
								$db = null;
						}
						$response = $view->render($response, "admin/users/edit.html",
										[
											'user' => $user,
											'msg' => $msg,
											// 'role' => $role,
											'roles' => UserDAO::ROLES
										]);
						return $response;
				}

				$params = $request->getParsedBody();
				$username = isset($params["username"]) ? $params["username"]: '';
				$password = isset($params["password"]) ? $params["password"]: '';
				$role = isset($params["role"]) ? intVal($params["role"]): 2;
				var_dump($username);

				$msg = "";
				if (empty($username)) {
					return $response->withHeader('Location', sprintf('/admin/users/%s', $user_id))->withStatus(302);
				}

				try {
						$db = $this->container->get("db");
						$userDao = new UserDAO($db);
						$res = $userDao->updateUser($user_id, $username, $password, $role);
						if (!$res) {
								$msg = "Failed update user";
						}
				} catch(PDOException $ex) {
						var_dump($ex->getMessage());
				} finally {
						// DB切断。
						$db = null;
				}

				$response = $view->render($response, "admin/users/edit.html",
										[
											'user' => [
												'username' => $username,
												'role' => $role,
											],
											'msg' => $msg,
											'roles' => UserDAO::ROLES
										]);
				return $response;
		}

}
