<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use \Illuminate\Database\Capsule\Manager as DB;

use App\Controllers\BaseController;

class SampleController extends BaseController
{

    public function mapIndex(Request $request, Response $response, array $args): Response
    {
        $response = $this->view->render($response, "sample/index.html");
        return $response;
    }

    public function mapHello(Request $request, Response $response, array $args): Response
    {
        $content = "HelloWorld!";
        $responseBody = $response->getBody();
        $responseBody->write($content);
        return $response;
    }
}
