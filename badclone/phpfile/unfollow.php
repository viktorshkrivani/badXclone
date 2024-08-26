<?php
session_start();
if (isset($_POST['user_to_unfollow'])) {
    $user_to_unfollow = $_POST['user_to_unfollow'];
    $current_user_id = $_SESSION['user_id'];

    // Delete from following table
    $sql = "DELETE FROM following WHERE followed_by_user_id = '$current_user_id' AND following_user_id = '$user_to_unfollow'";
    $conn->query($sql);

    // Redirect back to profile page
    header("Location: showprofile.php?user=" . $user_to_unfollow);
    exit;
}
?>