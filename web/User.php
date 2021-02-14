<?php
require_once 'connect.php';

class User {
    private $username;
    private $phone;

    public function __construct($username) {
        $this->username = $username;
        $this->initialize();
    }

    private function initialize() {
        global $conn;
        $sql = "SELECT phone FROM users WHERE username=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 1) {
            $usr = $result->fetch_assoc();
            $this->phone = $usr['phone'];
        } else {
            return;
        }
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPhone() {
        return $this->phone;
    }
}