<?php
// Define o timezone para evitar problemas de data
date_default_timezone_set('America/Sao_Paulo');
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
use App\Core\ExceptionHandler;
use App\Core\Router;

Env::load(__DIR__ . '/../.env');
ExceptionHandler::register();

// Inclui as rotas
require_once __DIR__ . '/../routes/api.php';

global $router;
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
