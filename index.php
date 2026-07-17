<?php
require_once 'core/Router.php';
require_once 'models/ScheduleModel.php';
require_once 'controllers/ApiController.php';
require_once 'controllers/HomeController.php';

$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

$router = new Router();

$router->add('', 'HomeController', 'index');
$router->add('add', 'HomeController', 'add');
$router->add('edit', 'HomeController', 'edit');
$router->add('delete', 'HomeController', 'delete');
$router->add('api/schedules', 'ApiController', 'getSchedules');
$router->add('api/upload', 'ApiController', 'uploadSchedules');
$router->add('api/add-log', 'ApiController', 'addLog');

$router->dispatch($url);
