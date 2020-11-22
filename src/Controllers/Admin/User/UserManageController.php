<?php
namespace App\Controllers\Admin\User;

use PDOException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\Exception\NotFoundException;
use \Illuminate\Database\Capsule\Manager as DB;

use App\Controllers\BaseController;

use App\Models\User;

class UserManageController extends BaseController
{
    public function getUsers(Request $request, Response $response, array $args): Response
    {
        $users = User::all();

        $response = $this->view->render($response,
                "admin/users/index.html",
                [
                    'users' => $users,
                    'roles' => User::ROLES,
                    'session_role' => $_SESSION['user']['role']
                ]);
        return $response;
    }

    public function mapUsersAdd(Request $request, Response $response, array $args): Response
    {
        if ($request->getMethod() == "GET") {
            $response = $this->view->render($response,
                    "admin/users/edit.html",
                    ['roles' => User::ROLES]);
            return $response;
        }

        $params = $request->getParsedBody();
        $username = isset($params["username"]) ? $params["username"]: '';
        $password = isset($params["password"]) ? $params["password"]: '';
        $role = isset($params["role"]) ? intVal($params["role"]): 2;

        $msg = "";
        if ($username == '' || $password == '') {
            $msg = "invalid param";
            $response = $this->view->render($response,
                    "admin/users/edit.html",
                    [
                        'msg' => $msg,
                        'roles' => User::ROLES
                    ]);
            return $response;
        }

        try {
            $res = User::firstOrCreate([
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'is_active' => 1
            ]);
            if (!$res) {
                $msg = "Failed add user";
            }
        } catch(PDOException $ex) {
            var_dump($ex->getMessage());
        }

        $response = $this->view->render($response, "admin/users/edit.html",['msg' => $msg]);
        return $response;
    }

    public function mapUsersId(Request $request, Response $response, array $args): Response
    {
        $user_id = intVal($args['id']);
        if ($request->getMethod() == "GET") {
            $msg = "";
            try {
                $user = User::find($user_id);

                if (!$user) {
                    $msg = "Not found user";
                }
            } catch(PDOException $ex) {
                var_dump($ex->getMessage());
            }
            $response = $this->view->render($response, "admin/users/edit.html",
                    [
                        'user' => $user,
                        'msg' => $msg,
                        'roles' => User::ROLES
                    ]);
            return $response;
        }

        $params = $request->getParsedBody();
        $username = isset($params["username"]) ? $params["username"]: '';
        $password = isset($params["password"]) ? $params["password"]: '';
        $role = isset($params["role"]) ? intVal($params["role"]): 2;

        $msg = "";
        if (empty($username)) {
            return $response->withHeader('Location', sprintf('/admin/users/%s', $user_id))->withStatus(302);
        }

        try {
            if ($username && $password) {
                $user = User::find($user_id)
                    ->update([
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => $role,
                    ]);
                if (!$user) {
                        $msg = "Failed update user";
                }
            }
        } catch(PDOException $ex) {
            var_dump($ex->getMessage());
        }

        $response = $this->view->render($response, "admin/users/edit.html",
                [
                    'user' => [
                        'username' => $username,
                        'role' => $role,
                    ],
                    'msg' => $msg,
                    'roles' => User::ROLES
                ]);
        return $response;
    }
}
