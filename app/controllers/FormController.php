<?php
namespace Slim\MVC\controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\MVC\entities;
use Slim\MVC\daos;

class FormController
{
		private $container;

	// コンストラクタ
	public function __construct(ContainerInterface $container)
	{
			$this->container = $container;
	}

    public function getIndex(Request $request, Response $response, array $args): Response
    {
			$view = $this->container->get("view");
			$response = $view->render($response, "form/index.html");
			return $response;
    }
}
