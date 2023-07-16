<?php
session_start();
$_SESSION['rollUser']=1;

require_once('..\config\db.php');
//получение результата
$result=$mysqli->query(sprintf("SELECT `APassword`,`ARoll` FROM `auth` WHERE `ALogin`='".$_POST['email']."'"));
$row=$result->fetch_assoc();

if($row){
    //если пользователь был найден
    if( password_verify($_POST['pass'],$row['APassword'])){
        //присваивание роил пользователя
        $_SESSION['rollUser']=$row['ARoll'];

        echo 1;
    }
    else{
        //если неверен пароль
        echo "Пароль введен неверно.";
    }

}
else{
    //если никого нет
    echo "Аккаунта с данным логином не найден.";
}