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

$sql = "SELECT username FROM user ORDER BY id DESC LIMIT 1";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $username = $row["username"];
    }
} else {
    $username = "Unknown";
}

$con->close();
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="http://localhost/badclone/cssfile/accexists.css">
    </head>
    <body>
        <div class="container">
        <form action="createdsuc.php" class="box" method="post">
            <div class="header">
                <div class="logo">
                    <svg viewBox="0 0 24 24" aria-hidden="true" class="logo-svg">
                    <g>
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path>
                    </g>
                    </svg>   
                </div>
            </div>            
            <div class="greeting">
                <h2>Hello, @<?php echo $username; ?>!</h2>
            </div>
            <div class="info">
                <h4>your account already exist.</h4>
            <div class="form-actions">
            <button type="button" onclick="location.href='http://localhost/badclone/index.php'">Go back</button>
            </div>
            <div class="info">                
            </div>            
        </form>
         </div>

    </body>
</html>
