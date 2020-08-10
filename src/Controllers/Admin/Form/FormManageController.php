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
													['itemtypes' => FormDAO::ITEM_TYPE, 'valid_types' => FormDAO::VALIDATE_TYPE,]);
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
					'input_type' => isset($params["input_type"]) ? intVal($params["input_type"]): 0,
					'placeholder' => isset($params["placeholder"]) ? $params["placeholder"]: '',
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
															'itemtypes' => FormDAO::ITEM_TYPE,
															'valid_types' => FormDAO::VALIDATE_TYPE,
													]);
						return $response;
				}

				$params = $request->getParsedBody();
				$form_group =[
					'id' => $fg_id,
					'name' => isset($params["group_name"]) ? $params["group_name"]: '',
					'base_uri' => isset($params["group_name"]) ? $params["group_name"]: '',
				];

				$form_items = [];
				for($i = 0; $i < count($params["label_name"]); $i++) {
					$form_items[] = [
						'label_name' => isset($params["label_name"][$i]) ? $params["label_name"][$i]: '',
						'schema_name' => isset($params["schema_name"][$i]) ? $params["schema_name"][$i]: '',
						'input_type' => isset($params["input_type"][$i]) ? $params["input_type"][$i]: '',
						'placeholder' => isset($params["placeholder"][$i]) ? $params["placeholder"][$i]: '',
						'is_required' => (intVal($params["is_required"][$i]) == 1) ? 1: 0,
						'choice_value' => isset($params["choice_value"][$i]) ? $params["choice_value"][$i] : '',
						'validate' => isset($params["validate"][$i]) ? $params["validate"][$i]: '',
					];
				}

				try {
						$formDao = new FormDAO($this->db);
						$res = $formDao->updateForm($form_group, $form_items);

						if (!$res) {
								$msg = "Failed update form";
						}
				} catch(PDOException $ex) {
						var_dump($ex->getMessage());
				} finally {
						// DB切断。
						$this->db = null;
				}

				return $response->withHeader('Location', '/admin/forms')->withStatus(302);

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
