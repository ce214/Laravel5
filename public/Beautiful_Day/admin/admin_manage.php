<?php
session_start();

// If the session vars aren't set, try to set them with a cookie
if (!isset($_SESSION['user'])) {
    if (isset($_COOKIE['user'])) {
        $_SESSION['user'] = $_COOKIE['user'];
    }
}
?>


<html xmlns="" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Beautiful Day - FileManage page</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<h3>Beautiful Day - FileManage page</h3>

<?php
require( __DIR__ . '/../etc/DB_config.php');
require( __DIR__ . '/../etc/global_defines.php');

// IF log in success,Generate the navigation menu
if (isset($_SESSION['user'])) {
    echo '&#10084; <a href="Logout.php">Log Out (' . $_SESSION['user'] . ')</a>';
}
else {
    //if not login,return to index.php
    $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
    header('Location: ' . $home_url);
}

// Grab the data from the POST
if (isset($_POST['submit'])) {
    // Grab the score data from the POST
    $date = $_POST['date'];
    $text = $_POST['text'];
    $image = $_FILES['image']['name'];
    $image_type = $_FILES['image']['type'];
    $image_size = $_FILES['image']['size'];
    $music = $_FILES['music']['name'];
    $music_type = $_FILES['music']['type'];
    $music_size = $_FILES['music']['size'];

    $format = '/\(?20\d{2}[-]?\d{2}[-]?\d{2}/';

    if ((preg_match($format, $date)) && (preg_match($format, $image)) && (preg_match($format, $music)) && (strlen($date)==10) && (strlen($image)==14) && (strlen($music)==14) ) {

        if ((($image_type == 'image/gif') || ($image_type == 'image/jpeg') || ($image_type == 'image/pjpeg') || ($image_type == 'image/png'))
            && ($image_size > 0) && ($image_size <= IMG_MAXFILESIZE)) {
            if (($_FILES['image']['error'] == 0)&&($_FILES['music']['error'] == 0)) {
                // Move the file to the target upload folder
                $target_img = __IMAGE_DIR ."/". $image;  
                $target_music = __MUSIC_DIR."/" . $music;
                $image_url = __IMG_URLPATH ."/". $image;
                $music_url = __MUSIC_URLPATH ."/". $music;

                if ((move_uploaded_file($_FILES['image']['tmp_name'], $target_img))&&(move_uploaded_file($_FILES['music']['tmp_name'], $target_music))) {
                    // Connect to the database
                    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

                    // Write the data to the database
                    $query = "INSERT INTO data VALUES (0, '$date', '$text', '$image_url', '$music_url')";
                    mysqli_query($dbc, $query);

                    // Confirm success with the user
                    echo '<p>Thanks! Added success!</p>';
                    echo '<p><strong>Date:</strong> ' . $date . '<br />';
                    echo '<strong>Text:</strong> ' . $text . '<br />';
                    echo '<img src="' . '/../../'.IMG_UPLOADPATH . $image .'"alt="image" /></p>';


                    // Clear the score data to clear the form
                    $name = "";
                    $score = "";
                    $image = "";
		    $music = "";

                    mysqli_close($dbc);
                }
                else {
                    echo '<p class="error">Sorry, there was a problem uploading your image.</p>';
                }
            }
        }
        else {
            echo '<p class="error">The image must be a GIF, JPEG, or PNG image file no greater than ' . (IMG_MAXFILESIZE / 1024) . ' KB in size.</p>';
        }

        // Try to delete the temporary  file
        @unlink($_FILES['image']['tmp_name']);
        @unlink($_FILES['music']['tmp_name']);
    }
    else {
        echo '<p class="error">Please enter all of the information to add your messages.</p>';
    }
}

?>

<hr />

<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo IMG_MAXFILESIZE; ?>" />
    <label for="date">Date:</label>
    <input type="text" id="date" name="date" value="<?php if (!empty($date)) echo $date; ?>" /><br />
    <hr />
    <label for="text">Text for the day:</label>
    <input type="text" id="text" name="text" value="<?php if (!empty($text)) echo $text; ?>" /><br />
    <hr />
    <label for="image">Image for the day:</label>
    <input type="file" id="img" name="image" />
    <hr />
    <label for="music">Music for the day:</label>
    <input type="file" id="music" name="music" />
    <hr />
    <input type="submit" value="上传" name="submit" />
</form>

</body>
</html>
