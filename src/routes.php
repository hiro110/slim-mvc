<?php
use CamtemSlim\MVC\controllers\FormController;
use CamtemSlim\MVC\controllers\AdminController;
use CamtemSlim\MVC\middlewares;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', FormController::class.":getIndex");

$app->group('/form', function (RouteCollectorProxy $group){
  $group->map(["GET"], "[/]", FormController::class.":getIndex");
  $group->map(["GET","POST"],'/confirm', FormController::class.":getConfirm");
  $group->map(["GET","POST"],'/complete', FormController::class.":getComplete");
});

$app->map(["GET"], "/admin[/]", AdminController::class.":getIndex")->add(new middlewares\LoggedInCheck($container));
$app->group('/admin', function (RouteCollectorProxy $group){
  $group->map(['GET','POST'], '/login', AdminController::class.':mapLogin');
  $group->map(['GET','POST'], '/logout', AdminController::class.':mapLogout');
});
