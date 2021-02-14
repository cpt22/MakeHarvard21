<?php
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
    return isset($_SESSION['username']) && isset($_SESSION['userID']);
}