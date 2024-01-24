<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/app/utils/encrypt.php';

function session_check()
{
    session_start();
    if (isset($_SESSION['username'])) {
        if (time() - $_SESSION['login_time_stamp'] > (60 * 60)) {
            session_unset();
            session_destroy();
            $_SESSION['username'] = '';
            $_SESSION['password'] = '';
            header("Location: /fatoora/dashboard-css.php");
        }
    } else {
        header("Location: /fatoora/index.php");
    }
}

function session_config($username, $password)
{
    session_start();
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;

    $_SESSION['login_time_stamp'] = time();
}

function session_get()
{
    session_start();
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    return ["username" => $username, "password" => $password];
}
