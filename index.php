<?php

session_start();

require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => 'cache',
]);

echo $twig->render('index.html', ['title'=>'Авторизация', 'style'=>'authorization', ]);


?>