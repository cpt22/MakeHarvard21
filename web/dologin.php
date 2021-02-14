<?php
require_once 'connect.php';
require_once 'session.php';

$username = $password = "";
$params = array();

$errors = array();
$vals = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    if (isset($_POST['username'])) {
        $vals['username'] = $username = strtolower($_POST['username']);
    } else {
        $errors['username'] = "Missing username";
    }

    if (isset($_POST['password'])) {
        $vals['password'] = $password = $_POST['password'];
    } else {
        $errors['password'] = "Missing password";
    }

    if (empty($errors)) {
        doLogin($username, $password);
    } else {
        var_dump($errors);
    }
}

function doLogin($username, $password)
{
    global $conn, $errors, $redirectURL;
    $sql = "SELECT username,password FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 1) {
        $result = $result->fetch_assoc();

        $username = $result['username'];
        $passwordHash = $result['password'];

        if (password_verify($password, $passwordHash)) {
            initializeSession($username, $redirectURL);
            return;
        }
    }
    $errors['loginAttempt'] = false;
}


?>