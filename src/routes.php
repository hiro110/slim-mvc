<?php
use App\Controllers\FormController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\User\UserManageController;
use App\Middlewares;
use Slim\Routing\RouteCollectorProxy;

use Psr\Container\ContainerInterface;

$app->get('/', FormController::class.":getIndex");

$app->group('/form', function (RouteCollectorProxy $group){
  $group->map(["GET"], "[/]", FormController::class.":getIndex");
  $group->map(["GET","POST"],'/confirm', FormController::class.":getConfirm");
  $group->map(["GET","POST"],'/complete', FormController::class.":getComplete");
});

$app->map(["GET"], "/admin[/]", AuthController::class.":getIndex")->add(new Middlewares\LoggedInCheckMiddleware($container));
$app->get("/admin/users", UserManageController::class.":getUsers")->add(new Middlewares\LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"],"/admin/users/add", UserManageController::class.":mapUsersAdd")->add(new Middlewares\LoggedInCheckMiddleware($container));
$app->map(["GET", "POST", "PUT", "DELETE"], "/admin/users/{:id}", UserManageController::class.":mapUsersId")->add(new Middlewares\LoggedInCheckMiddleware($container));


$app->map(['GET','POST'], '/admin/login', AuthController::class.':mapLogin');
$app->get( '/admin/logout', AuthController::class.':getLogout');


// $app->group('/admin', function (RouteCollectorProxy $group, ContainerInterface $container){
//   // $group->get(["GET"], "[/]", AdminController::class.":getIndex")->add(new middlewares\LoggedInCheckMiddleware($container));
//   $group->map(['GET','POST'], '/login', AdminController::class.':mapLogin');
//   $group->get('/logout', AdminController::class.':getLogout');
// });
