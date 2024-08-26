
<?php

ob_start(); // Start output buffering

include 'navside.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twitter_clone";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['user'])) {
    die("User not specified");
}

$user_id = $_SESSION['user_id']; // Logged-in user ID
$profile_username = $_GET['user']; // Profile username

// Get profile user's ID
$query = "SELECT id FROM user WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $profile_username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $profile_user = $result->fetch_assoc();
    $profile_user_id = $profile_user['id'];
} else {
    die("User not found");
}

// Handle follow/unfollow action
if (isset($_POST['follow_action'])) {
    $action = $_POST['follow_action'];
    if ($action == 'follow') {
        $stmt = $conn->prepare("SELECT * FROM following WHERE followed_by_user_id = ? AND following_user_id = ?");
        $stmt->bind_param("ii", $user_id, $profile_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO following (followed_by_user_id, following_user_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $profile_user_id);
            $stmt->execute();
        }
    } elseif ($action == 'unfollow') {
        $stmt = $conn->prepare("SELECT * FROM following WHERE followed_by_user_id = ? AND following_user_id = ?");
        $stmt->bind_param("ii", $user_id, $profile_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM following WHERE followed_by_user_id = ? AND following_user_id = ?");
            $stmt->bind_param("ii", $user_id, $profile_user_id);
            $stmt->execute();
        }
    }

    header("Location: showprofile.php?user=" . urlencode($profile_username));
    exit();
}

// Initialize variables to avoid undefined variable warnings
$following_count = 0;
$followers_count = 0;

// Get following count
$query = "SELECT COUNT(*) as following_count FROM following WHERE followed_by_user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$following_result = $stmt->get_result();
if ($following_result->num_rows > 0) {
    $following_count = $following_result->fetch_assoc()['following_count'];
}

// Get followers count
$query = "SELECT COUNT(*) as followers_count FROM following WHERE following_user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$followers_result = $stmt->get_result();
if ($followers_result->num_rows > 0) {
    $followers_count = $followers_result->fetch_assoc()['followers_count'];
}

// Check if logged-in user is following the profile user
$query = "SELECT * FROM following WHERE followed_by_user_id = ? AND following_user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $profile_user_id);
$stmt->execute();
$result = $stmt->get_result();
$is_following = $result->num_rows > 0;

// Fetch the tweets and other profile information
$query = "SELECT t.*, u.username FROM tweet t JOIN user u ON t.user_id = u.id WHERE u.username = ? ORDER BY t.id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $profile_username);
$stmt->execute();
$data = $stmt->get_result();

ob_end_flush(); // End output buffering and send output
?>


<link rel="stylesheet" type="text/css" href="http://localhost/badclone/cssfile/showprofile.css">
<body>
    <div class="main-profile-page">
        <h1>Profile @ <?php echo htmlspecialchars($profile_username); ?></h1>
<?php if ($user_id != $profile_user_id): ?>
            <form method="post">
                <input type="hidden" name="follow_action" value="<?php echo $is_following ? 'unfollow' : 'follow'; ?>">
                <button type="submit">
    <?php echo $is_following ? 'Unfollow' : 'Follow'; ?>
                </button>
            </form>
        <?php endif; ?>
    </div>
    <div class="cover-picture">
        <img src="images/cover.jpg" alt="Cover Picture">
        <div class="profile-box-logout-mainprofile">
            <img src="images/profile.jpg" alt="Profile Picture">
        </div>
    </div>
    <div class="profile-info">
        <p>Joined <span>2022-01-01</span></p>
        <div class="follow-container">
            <h4>Following <span><?php echo $following_count; ?></span></h4>
            <h4>Followers <span><?php echo $followers_count; ?></span></h4>
        </div>
    </div>
    <div class="secondary-profile-header">
        <h1>Posts</h1>
    </div>

<?php while ($row = mysqli_fetch_assoc($data)) { ?>
        <div class="tweet">
            <div class="tweet-header">
                <div class="profile-box2">
                    <img src="<?php echo $row['image_path']; ?>" alt="Tweet Image">
                </div>
                <p><strong><?php echo $row['username']; ?></strong></p>
            </div>
            <p><?php echo $row['text']; ?></p>
    <?php if ($row['image_path']) { ?>
                <img src="<?php echo 'http://localhost/badclone/phpfile/' . $row['image_path']; ?>" alt="Tweet Image">
    <?php } ?>
            <p><small>Posted on: <?php echo $row['timestamp']; ?></small></p>

    <?php
    $is_liked = false;
    $sql = "SELECT * FROM likes WHERE user_id = '$user_id' AND tweet_id = '{$row['id']}'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $is_liked = true;
    }

    // Get the likes count
    $sql = "SELECT COUNT(*) as likes_count FROM likes WHERE tweet_id = '{$row['id']}'";
    $result = $conn->query($sql);
    $likes_count = $result->fetch_assoc()['likes_count'];
    ?>

            <form action="likes.php" method="post">
                <input type="hidden" name="tweet_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="is_liked" value="<?php echo $is_liked ? 1 : 0; ?>">
                <button type="button" class="like-btn <?php echo $is_liked ? 'unlike-btn' : 'like-btn'; ?>" data-tweet-id="<?php echo $row['id']; ?>">
            <?php echo $is_liked ? 'Unlike' : 'Like'; ?>
                </button>
                <span class="likes-count">(<?php echo $likes_count; ?>)</span>
            </form>
        </div>
<?php } ?>

</body>