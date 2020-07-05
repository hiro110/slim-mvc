<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

class SampleController
{
		private $container;

	// コンストラクタ
	public function __construct(ContainerInterface $container)
	{
			$this->container = $container;
	}

    public function mapIndex(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
			$twig = $this->container->get("view");
			$response = $twig->render($response, "sample/index.html");
			return $response;
    }

    public function mapHello(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
			$content = "HelloWorld!";
			$responseBody = $response->getBody();
			$responseBody->write($content);
			return $response;
    }
}
