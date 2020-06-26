<?php
use SocymSlim\MVC\controllers\SampleController;

$app->map(["GET","POST"], "/", SampleController::class.":mapIndex");
$app->map(['GET','POST'], '/hello', SampleController::class.':mapHello');
