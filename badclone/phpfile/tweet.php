<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twitter_clone";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$Post_Text = "";
$Image_Path = null;

$user_id = $_SESSION['user_id'];

// Handles the image selected
if (isset($_FILES['post_image'])) {
    $Post_Text = $_POST['post_text'];

    // Handles the image uploaded
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
        $target_dir = "uploads/temp/";  // Temporary preview
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);  //Check if it does not exist
        }
        $target_file = $target_dir . basename($_FILES["post_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Is an image?
        $check = getimagesize($_FILES["post_image"]["tmp_name"]);
        if ($check !== false) {
            // Adds it on temporary directory
            if (move_uploaded_file($_FILES["post_image"]["tmp_name"], $target_file)) {
                $Image_Path = $target_file;  
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    }
}

if (isset($_POST['btn_add_post'])) {
    $Post_Text = $_POST['post_text'];
    $Image_Path = isset($_POST['image_path']) ? $_POST['image_path'] : null;  

    if ($Image_Path) {
        $final_dir = "uploads/";
        $final_file = $final_dir . basename($Image_Path);
        if (file_exists($Image_Path)) {
            rename($Image_Path, $final_file);
        } else {
            echo "Error: File not found.";
        }
        $Image_Path = $final_file;  
    }

    if ($Post_Text != "") {
    $query = "SELECT * FROM user WHERE id = '$user_id'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $sql = "INSERT INTO tweet (text, user_id, image_path, timestamp) VALUES ('$Post_Text', '$user_id', '$Image_Path', NOW())";
        $result = mysqli_query($conn, $sql);
        
        // Add this line here
        if ($result) {
             echo "<script>window.location.href = 'userhome.php';</script>";
            exit();
        } else {
            echo "Error posting tweet.";
        }
    } else {
        echo "Error: User ID does not exist.";
    }
}

$Post_Text = "";
$Image_Path = null;
}


$query = "SELECT t.*, u.username 
          FROM tweet t 
          JOIN user u ON t.user_id = u.id 
          LEFT JOIN following f ON t.user_id = f.following_user_id 
          WHERE (f.followed_by_user_id = ? OR t.user_id = ?) 
          ORDER BY t.id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$data = $stmt->get_result();

?>


    <title>Experimenting</title>
    <link rel="stylesheet" type="text/css" href="http://localhost/badclone/cssfile/tweet.css">

    <div class="header-foryou">
        <h1>For you</h1>
    </div>
    <div class="tweetarea">   
        
<form method="post" enctype="multipart/form-data">
  <div class="textarea-container">
    <div style="display: flex; flex-direction: row;">
      <div class="profile-box1">
        <img src="[user-image-url]" alt="profile">
      </div>
      <div style="display: flex; flex-direction: column;">
        <textarea name="post_text" cols="50" rows="5" placeholder="What's happening?"><?php echo htmlspecialchars($Post_Text); ?></textarea>
        <div class="image-preview-container">
          <?php if ($Image_Path): ?>
            <img src="<?php echo $Image_Path; ?>" alt="Preview Image" class="embedded-image">
            <input type="hidden" name="image_path" value="<?php echo $Image_Path; ?>">
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="image-upload-container">
    <label for="post_image" class="svg-label">
      <svg viewBox="0 0 24 24" aria-hidden="true" class="small-svg" style="fill: #3399ff;">
        <g>
          <path d="M3 5.5C3 4.119 4.119 3 5.5 3h13C19.881 3 21 4.119 21 5.5v13c0 1.381-1.119 2.5-2.5 2.5h-13C4.119 21 3 19.881 3 18.5v-13zM5.5 5c-.276 0-.5.224-.5.5v9.086l3-3 3 3 5-5 3 3V5.5c0-.276-.224-.5-.5-.5h-13zM19 15.414l-3-3-5 5-3-3-3 3V18.5c0 .276.224.5.5.5h13c.276 0 .5-.224.5-.5v-3.086zM9.75 7C8.784 7 8 7.784 8 8.75s.784 1.75 1.75 1.75 1.75-.784 1.75-1.75S10.716 7 9.75 7z"></path>
        </g>
      </svg>
      <input type="file" id="post_image" name="post_image" onchange="this.form.submit();" style="display: none;">
    </label>
    <div class="tweet-button-container">
      <button type="submit" name="btn_add_post" class="post-button">Post</button>
    </div>
  </div>
</form>

    
  <?php while ($row = mysqli_fetch_assoc($data)) { ?>
    </div>
    <div class="tweet">
        <div class="tweet-header">
            <div class="profile-box2">
                <img src="[profile-picture-url]" alt="profile">
            </div>
            <p><strong><?php echo $row['username']; ?></strong></p>
        </div>
        <p><?php echo $row['text']; ?></p>
        <?php if ($row['image_path']) { ?>
            <img src="<?php echo $row['image_path']; ?>" alt="Tweet Image">
        <?php } ?>
        <p><small>Posted on: <?php echo $row['timestamp']; ?></small></p>
        <?php
        $is_liked = false;
        $sql = "SELECT * FROM likes WHERE user_id = '$user_id' AND tweet_id = '$row[id]'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $is_liked = true;
        }
        
        // Get the likes count
        $sql = "SELECT COUNT(*) as likes_count FROM likes WHERE tweet_id = '$row[id]'";
        $result = $conn->query($sql);
        $likes_count = $result->fetch_assoc()['likes_count'];
        ?>
        <p>
        <form action="likes.php" method="post">
                <input type="hidden" name="tweet_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="is_liked" value="<?php echo $is_liked ? 1 : 0; ?>">
                <button type="button" class="like-btn <?php echo $is_liked ? 'unlike-btn' : 'like-btn'; ?>" data-tweet-id="<?php echo $row['id']; ?>">
                    <?php echo $is_liked ? 'Unlike' : 'Like'; ?>
                </button>
                <span class="likes-count">(<?php echo $likes_count; ?>)</span>
            </form>
        </p>
    </div>
    

<?php } ?>

