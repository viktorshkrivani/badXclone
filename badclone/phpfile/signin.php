<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";  
$dbname = "twitter_clone"; 


$con = new mysqli($servername, $username, $password, $dbname);


if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = '$username_or_email' OR email = '$username_or_email'";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id'] = $row['id']; //store the user ID 
            header("Location: userhome.php");
            exit();
        } else {
            header("Location: badcredentials.php");
            exit();
        }
    } else {
        header("Location: badcredentials.php");
        exit();
    }
}

$con->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sign In Form</title>
        
        <link rel="stylesheet" type="text/css" href="/badclone/cssfile/signincss.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" 
      integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" 
      crossorigin="anonymous" />
    </head>
      
    <body>
         <div class="overlay"></div>
         <div class="container">
        <form action="signin.php" class="box" method="post">
            <div class="header">
                <div class="close-btn">
                    <a href="http://localhost/badclone/index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <g id="Complete">
                    <g id="x">
                    <g>
                    <line x1="5" y1="4.8" x2="19" y2="19.2" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    <line x1="19" y1="4.8" x2="5" y2="19.2" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </g>
                    </g>
                    </g>
                    </svg>
                    </a>
                </div>
                <div class="logo">
                    <svg viewBox="0 0 24 24" aria-hidden="true" class="logo-svg">
                    <g>
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path>
                    </g>
                    </svg>   
                </div>
            </div>
            <div class="form-title">
            <h2>Sign in to X</h2>
            </div>
            <div class="social-buttons">
                <button class="google_btn"><i class="fab fa-google google-icon"></i> Sign up with Google</button>
                <button class="apple_btn"><i class="fab fa-apple apple-icon"></i> Sign up with Apple</button>
            </div>
            <div class="separator">
                <div class="line"></div>
                <h5>or</h5>
                <div class="line"></div>                        
            </div>
            <div class="form-group">
            <input type="text" id="username_or_email" name="username_or_email" placeholder="Username or Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-actions">
            <button type="submit">Sign in</button>
            </div>
            <div class="forgot-password">
                <button type="submit">Forgot password?</button>
            </div>
            <div class="create-account">
                <p>Don't have an account? <a href="newacc.php">Sign up</a></p>
            </div>
        </form>
         </div>
    
    
        <?php
        // put your code here
        ?>
    </body>
</html>
