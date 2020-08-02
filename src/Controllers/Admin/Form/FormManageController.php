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
				try {
						$formDao = new FormDAO($this->db);
						$forms = $formDao->findAllForms();

						if (!$forms) {
								$msg = "Failed add form";
						}
				} catch(PDOException $ex) {
						var_dump($ex->getMessage());
				} finally {
						// DB切断。
						$this->db = null;
				}
				var_dump($forms);
				$response = $this->view->render($response,
											"admin/forms/index.html",
										[
											'forms' => $forms,
										]);

				return $response;
		}

		public function mapformsAdd(Request $request, Response $response, array $args): Response
		{

				if ($request->getMethod() == "GET") {
						$response = $this->view->render($response,
													"admin/forms/edit.html",
													['itemtypes' => FormDAO::ITEM_TYPE]);
						return $response;
				}

				$params = $request->getParsedBody();
				$form_group =[
					'name' => isset($params["group_name"]) ? $params["group_name"]: '',
					'base_uri' => isset($params["group_name"]) ? $params["group_name"]: '',
				];

				$form_item =[
					'label_name' => isset($params["label_name"]) ? $params["label_name"]: '',
					'schema_name' => isset($params["schema_name"]) ? $params["schema_name"]: '',
					'input_type' => isset($params["itemtype"]) ? intVal($params["itemtype"]): 0,
					'is_required' => (intVal($params["is_required"]) == 1) ? 1: 0,
					'choice_value' => isset($params["choice_value"]) ? $params["choice_value"]: '',
					'validate' => isset($params["validate"]) ? $params["validate"]: '',
				];

				try {
						$formDao = new FormDAO($this->db);
						$forms = $formDao->addForm($form_group, $form_item);

						if (!$forms) {
								$msg = "Failed add form";
						}
				} catch(PDOException $ex) {
						var_dump($ex->getMessage());
				} finally {
						// DB切断。
						$this->db = null;
				}

				$response = $this->view->render($response, "admin/forms/edit.html",['msg' => $msg]);
				return $response;

		}

		public function mapformsId(Request $request, Response $response, array $args): Response
		{
				$fg_id = intVal($args['id']);
				if ($request->getMethod() == "GET") {
						$msg = "";
						try {
								$formDao = new FormDAO($this->db);

								$group = $formDao->findGroupByPk($fg_id);
								if (!$group) {
									$msg = "Not found form";
								}

								$items = $formDao->findItemByGid($fg_id);
								if (!$items) {
									$msg = "Not found form items";
								}

						} catch(PDOException $ex) {
								var_dump($ex->getMessage());
						} finally {
								// DB切断。
								$this->db = null;
						}

						$response = $this->view->render($response,
													"admin/forms/edit.html",
													[
															'form_group' => $group,
															'form_items' => $items,
															'msg' => $msg,
															'itemtypes' => FormDAO::ITEM_TYPE
													]);
						return $response;
				}

				// $response = $this->view->render($response, "admin/forms/edit.html",
				// 						[
				// 							'user' => [
				// 								'username' => $username,
				// 								'role' => $role,
				// 							],
				// 							'msg' => $msg,
				// 							'roles' => UserDAO::ROLES
				// 						]);
				// return $response;
		}

}
