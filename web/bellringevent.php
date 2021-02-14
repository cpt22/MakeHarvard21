<?php
$filename = $_POST['filename'];
$device_id = $_POST['device_id'];
$transcribed_text = $_POST['transcribed_text'];

$uploadpath = 'upload/';      // directory to store the uploaded files
$max_size = 30000;          // maximum file size, in KiloBytes
$alwidth = 900;            // maximum allowed width, in pixels
$alheight = 800;           // maximum allowed height, in pixels
$allowtype = array('wav', 'wave', 'mp3');        // allowed extensions

if(isset($_FILES['recording']) && strlen($_FILES['recording']['name']) > 1) {
    $uploadpath = $uploadpath . basename( $_FILES['recording']['name']);       // gets the file name
    $sepext = explode('.', strtolower($_FILES['recording']['name']));
    $type = end($sepext);       // gets extension
    var_dump($type);
    list($width, $height) = getimagesize($_FILES['recording']['tmp_name']);     // gets image width and height
    $err = '';         // to store the errors

    // Checks if the file has allowed type, size, width and height (for images)
    if(!in_array($type, $allowtype)) $err .= 'The file: <b>'. $_FILES['recording']['name']. '</b> not has the allowed extension type.';
    if($_FILES['recording']['size'] > $max_size*1000) $err .= '<br/>Maximum file size must be: '. $max_size. ' KB.';
    if(isset($width) && isset($height) && ($width >= $alwidth || $height >= $alheight)) $err .= '<br/>The maximum Width x Height must be: '. $alwidth. ' x '. $alheight;

    // If no errors, upload the image, else, output the errors
    if($err == '') {

        if(move_uploaded_file($_FILES['recording']['tmp_name'], __DIR__ . '/../' . $uploadpath)) {
            echo 'File: <b>'. basename( $_FILES['recording']['name']). '</b> successfully uploaded:';
            echo '<br/>File type: <b>'. $_FILES['recording']['type'] .'</b>';
            echo '<br />Size: <b>'. number_format($_FILES['recording']['size']/1024, 3, '.', '') .'</b> KB';
            if(isset($width) && isset($height)) echo '<br/>Image Width x Height: '. $width. ' x '. $height;
            echo '<br/><br/>Image address: <b>http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['REQUEST_URI']), '\\\\/').'/'.$uploadpath.'</b>';
        }
        else echo '<b>Unable to upload the file.</b>';
    }
    else echo $err;
}
?>

