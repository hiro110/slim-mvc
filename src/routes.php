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
  $group->map(["GET"], "/{uri}", FormController::class.":getForm");
  $group->map(["GET","POST"],'/{uri}/confirm', FormController::class.":getConfirm");
  $group->map(["GET","POST"],'/{uri}/complete', FormController::class.":getComplete");
});

// 管理サイト
$app->get("/admin[/]", AuthController::class.":getIndex")->add(new LoggedInCheckMiddleware($container));
$app->get("/admin/users", UserManageController::class.":getUsers")->add(new LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"],"/admin/users/add", UserManageController::class.":mapUsersAdd")->add(new LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"], "/admin/users/{id}", UserManageController::class.":mapUsersId")->add(new LoggedInCheckMiddleware($container));

$app->get("/admin/forms", FormManageController::class.":getForms")->add(new LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"],"/admin/forms/add", FormManageController::class.":mapFormsAdd")->add(new LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"], "/admin/forms/{id}", FormManageController::class.":mapFormsId")->add(new LoggedInCheckMiddleware($container));

$app->map(['GET','POST'], '/admin/login', AuthController::class.':mapLogin');
$app->get( '/admin/logout', AuthController::class.':getLogout');
