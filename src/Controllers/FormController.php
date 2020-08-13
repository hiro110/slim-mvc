<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use App\Entities\Admin;
use App\Daos\Admin\FormDAO;
use App\Daos\Form\SubmitDAO;

class FormController
{
		private $view;
		private $db;

		// コンストラクタ
		public function __construct(ContainerInterface $container)
		{
				$this->view = $container->get("view");
				$this->db = $container->get("db");
		}

    public function getIndex(Request $request, Response $response, array $args): Response
    {

			$response = $this->view->render($response, "form/index.html");
			return $response;
		}

		public function getForm(Request $request, Response $response, array $args): Response
    {
				$uri = $args['uri'];
				$items = "";
				$msg = "";

				try {
						$formDao = new FormDAO($this->db);
						$gid = $formDao->findByUri($uri);
						if (!$gid) {
							$msg = "Not found form";
							return $response->withHeader('Location', '/')->withStatus(302);
						}

						$tmps = $formDao->findItemByGid($gid);
						if (!$items) {
							$msg = "Not found form items";
						}

						$items = [];
						foreach ($tmps as $tmp) {
								$items[] = [
									'id' => $tmp->getId(),
									'labelname' => $tmp->getLabelName(),
									'schemaname' => $tmp->getSchemaName(),
									'inputtype' => $tmp->getInputType(),
									'placeholder' => $tmp->getPlaceholder(),
									'isrequired' => $tmp->getIsRequired(),
									'choicevalue' => explode("\n", str_replace(array("\r\n","\r","\n"), "\n", $tmp->getChoiceValue())),
									'validate' => $tmp->getValidate(),
								];
						}

				} catch(PDOException $ex) {
						var_dump($ex->getMessage());
				} finally {
						// DB切断。
						$this->db = null;
				}

				$response = $this->view->render($response,
														"form/form.html",
														[
																'uri' => $uri,
																'form_items' => $items,
																'msg' => $msg,
														]);
				return $response;
		}

		public function postConfirm(Request $request, Response $response, array $args): Response
    {
				$uri = $args['uri'];
				$items = "";
				$msg = "";
				$params = $request->getParsedBody();

				try {
						$formDao = new FormDAO($this->db);
						$gid = $formDao->findByUri($uri);
						if (!$gid) {
							$msg = "Not found form";
							return $response->withHeader('Location', '/')->withStatus(302);
						}

						$tmps = $formDao->findItemByGid($gid);
						if (!$items) {
							$msg = "Not found form items";
						}

						$items = [];
						foreach ($tmps as $tmp) {
								$items[] = [
									'id' => $tmp->getId(),
									'labelname' => $tmp->getLabelName(),
									'schemaname' => $tmp->getSchemaName(),
									'inputtype' => $tmp->getInputType(),
									'placeholder' => $tmp->getPlaceholder(),
									'isrequired' => $tmp->getIsRequired(),
									'choicevalue' => explode("\n", str_replace(array("\r\n","\r","\n"), "\n", $tmp->getChoiceValue())),
									'validate' => $tmp->getValidate(),
								];

								$_SESSION['form'][$tmp->getSchemaName()] = $params[$tmp->getSchemaName()];
						}
				} catch(PDOException $ex) {
						var_dump($ex->getMessage());
				} finally {
						// DB切断。
						$this->db = null;
				}

				$response = $this->view->render($response,
														"form/confirm.html",
														[
																'uri' => $uri,
																'form_items' => $items,
																'msg' => $msg,
														]);
				return $response;
		}

		public function postComplete(Request $request, Response $response, array $args): Response
    {
				$uri = $args['uri'];
				$params = $request->getParsedBody();
				// var_dump($params);

				try {
						$formDao = new FormDAO($this->db);
						$gid = $formDao->findByUri($uri);
						if (!$gid) {
							$msg = "Not found form";
							return $response->withHeader('Location', '/')->withStatus(302);
						}

						$submitDao = new SubmitDAO($this->db);
						$res = $submitDao->addSubmit($gid, $params);

						if (!$res) {
							$msg = "Failed entry";
							return $response->withHeader('Location', '/')->withStatus(302);
						}
				} catch(PDOException $ex) {
						var_dump($ex->getMessage());
				} finally {
						// DB切断。
						$this->db = null;
				}

				unset($_SESSION['form']);
				$response = $this->view->render($response, "form/complete.html");
				return $response;
    }
}
