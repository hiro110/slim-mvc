<?php
use App\Controllers\FormController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\User\UserManageController;
use App\Controllers\Admin\Form\FormManageController;
use App\Middlewares\LoggedInCheckMiddleware;
use Slim\Routing\RouteCollectorProxy;

use Psr\Container\ContainerInterface;

$app->get('/', FormController::class.":getIndex");
// ユーザサイト
$app->group('/form', function (RouteCollectorProxy $group){
  $group->map(["GET"], "[/]", FormController::class.":getIndex");
  $group->map(["GET","POST"],'/confirm', FormController::class.":getConfirm");
  $group->map(["GET","POST"],'/complete', FormController::class.":getComplete");
});

// 管理サイト
$app->get("/admin[/]", AuthController::class.":getIndex")->add(new LoggedInCheckMiddleware($container));
$app->get("/admin/users", UserManageController::class.":getUsers")->add(new LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"],"/admin/users/add", UserManageController::class.":mapUsersAdd")->add(new LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"], "/admin/users/{id}", UserManageController::class.":mapUsersId")->add(new LoggedInCheckMiddleware($container));

$app->get("/admin/forms", UserManageController::class.":getForms")->add(new LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"],"/admin/forms/add", UserManageController::class.":mapFormssAdd")->add(new LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"], "/admin/forms/{id}", UserManageController::class.":mapFormsId")->add(new LoggedInCheckMiddleware($container));

$app->map(['GET','POST'], '/admin/login', AuthController::class.':mapLogin');
$app->get( '/admin/logout', AuthController::class.':getLogout');
