<?php
session_start();
require_once 'connect.php';
require_once 'User.php';

$user = null;

if (isset($_SESSION['username'])) {
    initializeUser();
} else {
    unset($_SESSION['username']);
}

function initializeSession($username)
{
    global $_SESSION;
    $_SESSION['username'] = $username;
    initializeUser();
}

function initializeUser()
{
    global $user;
    $user = new User($_SESSION['username']);
}

function isUserLoggedIn()
{
    return isset($_SESSION['username']);
}