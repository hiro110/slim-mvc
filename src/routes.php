<?php
use Slim\MVC\controllers\FormController;
use Slim\MVC\controllers\AdminController;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', FormController::class.":getIndex");

$app->group('/admin', function (RouteCollectorProxy $group){
  $group->map(["GET"], "[/]", AdminController::class.":getIndex");
  $group->map(['GET','POST'], '/login', AdminController::class.':mapLogin');
  $group->map(['GET','POST'], '/logout', AdminController::class.':mapLogout');
});
