<?php
use App\controllers\FormController;
use App\controllers\AdminController;
use App\middlewares;
use Slim\Routing\RouteCollectorProxy;

use Psr\Container\ContainerInterface;

$app->get('/', FormController::class.":getIndex");

$app->group('/form', function (RouteCollectorProxy $group){
  $group->map(["GET"], "[/]", FormController::class.":getIndex");
  $group->map(["GET","POST"],'/confirm', FormController::class.":getConfirm");
  $group->map(["GET","POST"],'/complete', FormController::class.":getComplete");
});

$app->map(["GET"], "/admin[/]", AdminController::class.":getIndex")->add(new middlewares\LoggedInCheckMiddleware($container));
$app->map(["GET", "POST"],"/admin/users", AdminController::class.":mapUsers")->add(new middlewares\LoggedInCheckMiddleware($container));
$app->map(["GET", "POST", "PUT", "DELETE"], "/admin/users/{:id}", AdminController::class.":mapUsersId")->add(new middlewares\LoggedInCheckMiddleware($container));


$app->map(['GET','POST'], '/admin/login', AdminController::class.':mapLogin');
$app->get( '/admin/logout', AdminController::class.':getLogout');


// $app->group('/admin', function (RouteCollectorProxy $group, ContainerInterface $container){
//   // $group->get(["GET"], "[/]", AdminController::class.":getIndex")->add(new middlewares\LoggedInCheckMiddleware($container));
//   $group->map(['GET','POST'], '/login', AdminController::class.':mapLogin');
//   $group->get('/logout', AdminController::class.':getLogout');
// });
