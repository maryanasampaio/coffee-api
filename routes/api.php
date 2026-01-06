<?php

use App\Core\Router;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\DrinkController;

use App\Controllers\DrinkHistoryController;
use App\Controllers\RankingController;

$router = new Router();

$router->group('/api/v1', function($router) {
	$router->add('POST', '/users', UserController::class . '@create');
	$router->add('POST', '/login', AuthController::class . '@login');
	$router->add('GET', '/users', UserController::class . '@list', true);
	$router->add('GET', '/users/{iduser}', UserController::class . '@get', true);
	$router->add('PUT', '/users/{iduser}', UserController::class . '@update', true);
	$router->add('DELETE', '/users/{iduser}', UserController::class . '@delete', true);
	$router->add('POST', '/users/{iduser}/drink', DrinkController::class . '@increment', true);
	$router->add('GET', '/users/{iduser}/drink/history', DrinkHistoryController::class . '@history', true);
	$router->add('GET', '/ranking/last-days', RankingController::class . '@lastDays', true);
	$router->add('GET', '/ranking/day', RankingController::class . '@byDay', true);
});
