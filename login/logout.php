<?php

session_start();

$unset = session_unset();
$distroy = session_destroy();
$reset = session_reset();
header("Location: /fatoora/index.php");
