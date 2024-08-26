<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}



// Get the username from the URL parameter
$username = isset($_GET['user']) ? $_GET['user'] : $_SESSION['username'];

// Check if the username exists in the database
if (!check_user_exists($username)) {
    header("Location: http://localhost/badclone/phpfile/usererror.php?username=$username");
    
    exit();
}

// Determine which page to show based on the 'page' query parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'tweet';

function check_user_exists($username) {
    // Connect to your database
    $conn = new mysqli("localhost", "root", "", "twitter_clone");

    // Check if the username exists
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="userhomestyle.css">
    <title>Twitter Clone</title>
</head>
<body>
    <div class="grid-container">
        <?php include 'navside.php'; ?>
        <?php include 'followersside.php'; ?>
        
        <div class="main-content">
            <?php
            // Include the appropriate content based on the selected page
            if ($page === 'profile') {
                include 'showprofile.php';
            } else {
                include 'tweet.php'; // default is the tweet page
            }
            ?>
        </div>
    </div>
</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.like-btn').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var tweet_id = button.data('tweet-id');
        var is_liked = button.hasClass('unlike-btn');

        $.ajax({
            url: 'likes.php',
            type: 'POST',
            data: {
                'like-btn': 1,
                'tweet_id': tweet_id,
                'is_liked': is_liked ? 1 : 0
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.is_liked) {
                    button.removeClass('like-btn').addClass('unlike-btn');
                    button.text('Unlike');
                } else {
                    button.removeClass('unlike-btn').addClass('like-btn');
                    button.text('Like');
                }
                button.next('.likes-count').text('(' + data.likes_count + ')');
            }
        });
    });
});
</script>