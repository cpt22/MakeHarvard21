<?php
require_once 'session.php';

if (!isUserLoggedIn()) {
    header("Location: http://192.168.100.111/signin.php");
}

if (isset($_GET['device'])) {
    $device_id = $_GET['device'];
    $stmt = $conn->prepare("SELECT * FROM device_associations WHERE username=? AND device_id=?");
    $stmt->bind_param("ss", $user->getUsername(), $device_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows == 1) {
        loadEvents($device_id);
    } else {
        header("Location: http://192.168.100.111/index.php");
    }
}

$events = array();

function loadEvents($device_id) {
    global $events;

    $stmt = $conn->prepare("SELECT * FROM events WHERE device_id=? ORDER BY date DESC");
    $stmt->bind_param("s", $device_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    while ($row = $result->fetch_assoc()) {
        array_push($events, array("time"=>$row['time'], "text"=>$row['text'], "filename"=>$row['filename']));
    }
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
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- ignore next two lines, thats fro the flexbox-->
        <ul class="navbar-nav mr-auto">
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <button class="btn btn-outline-success my-2 my-sm-0" type="button">My Devices</button>
        </form>
    </div>
</nav>

<div class="container">
    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">Time</th>
            <th scope="col">Message</th>
            <th scope="col">File</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($events as $event) {
            echo '<tr>
                    <td scope="row">' . $event['time'] . '</td>
                    <td>'. $event['text'] . '</td>
                    <td>'. $event['filename'] . '</td>
                 </tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>