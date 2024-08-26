<?php

session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twitter_clone";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$user_id = $_SESSION['user_id'];
$profile_user_id = $_POST['profile_user_id'];
$action = $_POST['follow_action'];

if ($action == 'follow') {
    $stmt = $conn->prepare("INSERT INTO following (followed_by_user_id, following_user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $profile_user_id);
} elseif ($action == 'unfollow') {
    $stmt = $conn->prepare("DELETE FROM following WHERE followed_by_user_id = ? AND following_user_id = ?");
    $stmt->bind_param("ii", $user_id, $profile_user_id);
} else {
    die("Invalid action");
}

if ($stmt->execute()) {
    // Redirect back to the profile page
    header("Location: showprofile.php?user=" . urlencode($_GET['user']));
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>