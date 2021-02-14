<?php
require_once 'session.php';

$_SESSION['username'] = null;
session_destroy();

header("Location: http://192.168.100.111/signin.php");
?>

