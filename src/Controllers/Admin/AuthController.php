<?php
namespace App\Controllers\Admin;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use App\Entities\Admin;
use App\Daos\Admin\UserDAO;

class AuthController
{
		private $container;

	// コンストラクタ
	public function __construct(ContainerInterface $container)
	{
				$this->container = $container;
	}

    public function getIndex(Request $request, Response $response): Response
    {
				$view = $this->container->get("view");
				$response = $view->render($response, "admin/index.html");
				return $response;
    }

    public function mapLogin(Request $request, Response $response): Response
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
						$userDao = new UserDAO($db);
						$user = $userDao->findByUser($username, $password);

						if (!$user['result']) {
								$response = $view->render($response, "admin/login.html",['msg' => $user['msg']]);
								return $response;
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

		public function getLogout(Request $request, Response $response): Response
    {
				if (array_key_exists('user', $_SESSION)) {
						unset($_SESSION['user']);
				}
				session_destroy();
				return $response->withHeader('Location', '/admin/login')->withStatus(302);
		}
}
