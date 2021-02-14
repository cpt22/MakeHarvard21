<?php
require_once 'session.php';

if (!isUserLoggedIn()) {
    header("Location: http://192.168.100.111/signin.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_device'])) {
    $device_id = $_POST['device_id'];

    $stmt = $conn->prepare("INSERT INTO device_associations (device_id, username) VALUES (?, ?)");
    $stmt->bind_param("ss", $device_id, $user->getUsername());
    $stmt->execute();
    $stmt->close();
}

$devices = array();
$username = $user->getUsername();

$stmt = $conn->prepare("SELECT * FROM device_associations WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

while ($row = $result->fetch_assoc()) {
    array_push($events, array("id"=>$row['device_id'], "descriptor"=>$row['descriptor']));
}

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="signin.css">
    <title>Hello, world!</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">DormRoom Doorbell</a>
</nav>
<div class="container">
    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">Your Devices</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($devices as $device) {
            echo '<tr>
                    <td>' . $device['descriptor'] . '</td>
                    <td>'. $device['device_id'] . '</td>
                 </tr>';
        }
        ?>
        </tbody>
    </table>
    <p>Register New Device</p>
    <form action="" method="post" class="row g-3">
        <div class="col-auto">
            <input type="text" class="form-control" name="device_id" placeholder="Device_ID">
        </div>
        <div class="col-auto">
            <button type="submit" name="add_device" class="btn btn-primary mb-3">Register</button>
        </div>
    </form>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>