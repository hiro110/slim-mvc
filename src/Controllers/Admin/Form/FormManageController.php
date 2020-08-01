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

class FormManageController
{
		private $container;

	// コンストラクタ
	public function __construct(ContainerInterface $container)
	{
				$this->container = $container;
	}

		public function getforms(Request $request, Response $response, array $args): Response
		{

				$response = $view->render($response, "admin/forms/index.html",['forms' => $forms, 'session_role' => $_SESSION['user']['role']]);
				return $response;

		}

		public function mapformsAdd(Request $request, Response $response, array $args): Response
		{

				$response = $view->render($response, "admin/forms/edit.html",['msg' => $msg]);
				return $response;

		}

		public function mapformsId(Request $request, Response $response, array $args): Response
		{

				$response = $view->render($response, "admin/forms/edit.html",
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
