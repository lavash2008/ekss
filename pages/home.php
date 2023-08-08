<?php
session_start();

require_once '../vendor/autoload.php';
require_once '../config/db.php';

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, [
    // 'cache' => 'cache',
]);

//получение всех учеников школы
$resultStudents= $mysqli->query(sprintf("SELECT `SLastName` as 'lastName',`SFirstName` as 'name',`SMindName` as 'mindName',`SBirthDate` as 'birthDate', CONCAT(`classes`.`CLevel`,`classes`.`Cletter`) as 'class' FROM `students`,`classes` WHERE `students`.`CId`=`classes`.`CId` order by `SBirthDate` DESC"));

//загрузка шаблона
echo $twig->render('home.html', ['title'=>'Главная', 'style'=>'home', 'result'=>$resultStudents]);
?>