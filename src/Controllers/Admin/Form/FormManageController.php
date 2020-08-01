<?php
namespace App\Controllers\Admin\Form;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use App\Entities\Admin;
use App\Daos\Admin\FormDAO;

class FormManageController
{
		private $view;
		private $db;

	// コンストラクタ
	public function __construct(ContainerInterface $container)
	{
				$this->view = $container->get("view");
				$this->db = $container->get("db");
	}

		public function getforms(Request $request, Response $response, array $args): Response
		{
				$forms = "";
				$response = $this->view->render($response, "admin/forms/index.html");
				return $response;
		}

		public function mapformsAdd(Request $request, Response $response, array $args): Response
		{

				$response = $this->view->render($response, "admin/forms/edit.html",['msg' => $msg]);
				return $response;

		}

		public function mapformsId(Request $request, Response $response, array $args): Response
		{

				$response = $this->view->render($response, "admin/forms/edit.html",
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
