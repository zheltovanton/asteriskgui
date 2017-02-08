<?php

/**
 * Asterisk GUI
 *
 * This is the Javascript / HTML / CSS / Ajax related to asterisk app. 
 * Only for viewing settings and conditions, not to configure. 
 * Screenshot from real system with Elastix in folder "screenshot". *
 * Using AMI for request info about channels and mysql for CDR stat. 
 * 
 * @author 	Zheltov Anton (anton.zheltov@gmail.com)
 * @license 	☺ License GNU3
 *
 */

$page = '';

require_once 'db/config.php';

$config = include("db/config.php");

session_start(); //Запускаем сессии

class AuthClass {
    private $_login = "demo"; //Устанавливаем логин
    private $_password = "demo"; //Устанавливаем пароль

    public function isAuth() {
        if (isset($_SESSION["is_auth"])) { //Если сессия существует
            return $_SESSION["is_auth"]; //Возвращаем значение переменной сессии is_auth (хранит true если авторизован, false если не авторизован)
        }
        else return false; //Пользователь не авторизован, т.к. переменная is_auth не создана
    }
    
    public function auth($login, $passwors) {
        if ($login == $this->_login && $passwors == $this->_password) { //Если логин и пароль введены правильно
            $_SESSION["is_auth"] = true; //Делаем пользователя авторизованным
            $_SESSION["login"] = $login; //Записываем в сессию логин пользователя
            return true;
        }
        else { //Логин и пароль не подошел
            $_SESSION["is_auth"] = false;
            return false; 
        }
    }
    
    public function getLogin() {
        if ($this->isAuth()) { //Если пользователь авторизован
            return $_SESSION["login"]; //Возвращаем логин, который записан в сессию
        }
    }
    
    
    public function out() {
        $_SESSION = array(); //Очищаем сессию
        session_destroy(); //Уничтожаем
    }
}

$auth = new AuthClass();

if (isset($_POST["login"]) && isset($_POST["password"])) { //Если логин и пароль были отправлены
    if (!$auth->auth($_POST["login"], $_POST["password"])) { //Если логин и пароль введен не правильно
        echo "<h2 style=\"color:red;\">Wrong auth data!</h2>";
    }
}

if (isset($_GET["is_exit"])) { //Если нажата кнопка выхода
    if ($_GET["is_exit"] == 1) {
        $auth->out(); //Выходим
        header("Location: ?is_exit=0"); //Редирект после выхода
    }
}

if (!$auth->isAuth()) {   
    die( '<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="public/css/login.css" />
</head>
<body>

  <form method="post" class="modal-content animate" action="">
    <div class="container">
      <label><b>Username</b></label>
      <input type="text" placeholder="Enter Username" name="login" required>

      <label><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="password" required>
        
      <button type="submit">Login</button>
    </div>

  </form>
</div>


</body>
</html>');
 } 



if (empty($_GET["p"])) {     // if some page requested, then show it, else show page CDR
	$page = 'rep.cdr';
} else {
  	$page = $_GET["p"];
}
		
echo '<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8" />
    <title>Asterisk Report</title>
    <link rel="stylesheet" type="text/css" href="public/css/index.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,600,400" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="navigation">
        <h1>Asterisk</h1>
        <ul>
            <li>Report 
		<ul>
		<li><a href="index.php?p=rep.cdr" >Call records</a></li>
		<li><a href="index.php?p=rep.cdr.ext" >Call records extended</a></li>
		<li><a href="index.php?p=rep.noanswer" >Not answered</a></li>
		<li><a href="index.php?p=rep.group.ext.cdr">Group by Extension</a></li>
		</ul>
	   </li>
            <li>Queue 
		<ul>
		<li><a href="index.php?p=queue">Show status</a></li>
		</ul>
	   </li>
            <li>Diagnostics 
		<ul>
		<li><a href="index.php?p=diag.total" >Total</a></li>
		<li><a href="index.php?p=sip.registry" >Sip Registry</a></li>
		<li><a href="index.php?p=sip.peers" >Sip Peers</a></li>
		<li><a href="index.php?p=sip.channels" >Channels</a></li>
		<li><a href="index.php?p=sip.channelstats" >Channel Stats</a></li>
		</ul>
	    </li>
            <li><a href="/?is_exit=1">Logout</a></li> 
	    </li>
        </ul>
    </div>
    <div class="index-frame">
        <iframe name="index" src="view/'.$page.'.php"></iframe>
    </div>
</body>
</html>';

