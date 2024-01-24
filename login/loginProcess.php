<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . '/fatoora/app/user/User.php';
require_once $ROOT . '/fatoora/login/utils.php';

$user = new User();

$username = $_POST['username'];
$password = $_POST['password'];

if (!isset($username) || !isset($password)) {
    $error_message = "Fill all the fields";
    header('Location: /fatoora/index.php');
    exit();
}

try {
    $check = $user->checkForPassword($username, $password);
} catch (Exception $e) {
    $error_message = "Login failed! wrong login or password";
    header('Location: /fatoora/index.php');
    exit();
}

if ($check) {
    session_config($username, $password);
    header('Location: /fatoora/dashboard-css.php');
} else {
    $error_message = "Login failed! wrong login or password";
    header('Location: /fatoora/index.php');
    exit();
}

?>
