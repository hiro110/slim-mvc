<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use App\Entities\Admin;
use App\Daos\Admin\FormDAO;

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

						$items = $formDao->findItemByGid($gid);
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
														"form/form.html",
														[
																'uri' => $uri,
																'form_items' => $items,
																'msg' => $msg,
														]);
				return $response;
		}

		public function getConfirm(Request $request, Response $response, array $args): Response
    {

			$response = $this->view->render($response, "form/index.html");
			return $response;
		}

		public function getComplete(Request $request, Response $response, array $args): Response
    {

			$response = $this->view->render($response, "form/index.html");
			return $response;
    }
}
