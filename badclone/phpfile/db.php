
<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twitter_clone";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
echo "Connected successfully";
?>