<?php
session_start();
$user_id = $_SESSION['user_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twitter_clone";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST['like-btn'])) {
    $tweet_id = $_POST['tweet_id'];
    $is_liked = $_POST['is_liked'];
    if ($is_liked) {
    // Unlike the tweet
    $sql = "DELETE FROM likes WHERE user_id = '$user_id' AND tweet_id = '$tweet_id'";
    $conn->query($sql);
    $sql = "UPDATE tweet SET likes = likes - 1 WHERE id = '$tweet_id'";
    $conn->query($sql); // Add this line
} else {
    // Like the tweet
    $sql = "INSERT INTO likes (user_id, tweet_id) VALUES ('$user_id', '$tweet_id')";
    $conn->query($sql);
    $sql = "UPDATE tweet SET likes = likes + 1 WHERE id = '$tweet_id'";
    $conn->query($sql); // Add this line
}
    $conn->query($sql);

    // Get the updated like count
    $sql = "SELECT COUNT(*) as likes_count FROM likes WHERE tweet_id = '$tweet_id'";
    $result = $conn->query($sql);
    $likes_count = $result->fetch_assoc()['likes_count'];

    // Return the new like status and count as JSON
    echo json_encode([
        'is_liked' => !$is_liked,
        'likes_count' => $likes_count
    ]);
    exit();
}
?>