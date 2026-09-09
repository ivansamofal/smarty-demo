<?php

declare(strict_types=1);

use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Controller\PostController;
use App\Database\Connection;
use App\Exception\CategoryNotFoundException;
use App\Exception\PostNotFoundException;
use App\Exception\RouteNotFoundException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\CategoryService;
use App\Service\PostService;
use App\View\SmartyFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

$view = SmartyFactory::create();

$connection = Connection::get();
$categoryRepository = new CategoryRepository($connection);
$postRepository = new PostRepository($connection);

$categoryService = new CategoryService($categoryRepository, $postRepository);
$postService = new PostService($postRepository, $categoryRepository);

$homeController = new HomeController($view, $categoryService);
$categoryController = new CategoryController($view, $categoryService);
$postController = new PostController($view, $postService);

$router = new Router();
$router->get('/', static fn (array $params, Request $request): Response => $homeController->index());
$router->get(
    '/category/{slug}',
    static fn (array $params, Request $request): Response => $categoryController->show($params['slug'], $request),
);
$router->get(
    '/post/{slug}',
    static fn (array $params, Request $request): Response => $postController->show($params['slug']),
);

$request = Request::fromGlobals();

try {
    $response = $router->dispatch($request);
} catch (RouteNotFoundException|CategoryNotFoundException|PostNotFoundException $exception) {
    $view->assign('message', $exception->getMessage());
    $response = new Response($view->fetch('error.tpl'), 404);
} catch (Throwable) {
    $view->assign('message', 'Внутренняя ошибка сервера.');
    $response = new Response($view->fetch('error.tpl'), 500);
}

$response->send();
