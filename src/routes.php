<?php
use CamtemSlim\MVC\controllers\FormController;
use CamtemSlim\MVC\controllers\AdminController;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', FormController::class.":getIndex");

$app->group('/form', function (RouteCollectorProxy $group){
  $app->map(["GET"], "[/]", FormController::class.":getIndex");
  $app->map(["GET","POST"],'/confirm', FormController::class.":getConfirm");
  $app->map(["GET","POST"],'/complete', FormController::class.":getComplete");
});

$app->group('/admin', function (RouteCollectorProxy $group){
  $group->map(["GET"], "[/]", AdminController::class.":getIndex");
  $group->map(['GET','POST'], '/login', AdminController::class.':mapLogin');
  $group->map(['GET','POST'], '/logout', AdminController::class.':mapLogout');
});
