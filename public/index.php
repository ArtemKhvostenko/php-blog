<?php

use Blog\LatestPosts;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Blog\PostMapper;

require __DIR__ . '/../vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$view = new Environment($loader);

$conf = include '../config/db.php';
$dsn = $conf['dsn'];
$uname = $conf['username'];
$pwd = $conf['password'];

try {
    $connection = new PDO($dsn, $uname, $pwd);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response) use ($view, $connection) {
    $latestPosts = new LatestPosts($connection);
    $posts = $latestPosts->get(3);
    $body = $view->render('index.twig', ['posts' => $posts]);
    $response->getBody()->write($body);

    return $response;
});

$app->get('/about', function (Request $request, Response $response) use ($view) {
    $body = $view->render('about.twig', ['name' => 'John']);
    $response->getBody()->write($body);

    return $response;
});

$app->get('/{url_key}', function (Request $request, Response $response, $args) use ($view, $connection) {
    $postMapper = new PostMapper($connection);
    $post = $postMapper->getByUrlKey((string) $args['url_key']);
    $body = (!$post) ? $view->render('not-found.twig'): $view->render('post.twig', ['post' => $post]);
    $response->getBody()->write($body);

    return $response;
});

$app->run();