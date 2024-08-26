<?php

$servername = "localhost";
$username = "root";
$password = "";  
$dbname = "twitter_clone"; 


$con = new mysqli($servername, $username, $password, $dbname);


if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  
    $birth_month = $_POST['month'];
    $birth_day = $_POST['day'];
    $birth_year = $_POST['year'];

    
    $stmt = $con->prepare("INSERT INTO user (name, username, email, password, birth_month, birth_day, birth_year) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiii", $name, $username, $email, $password, $birth_month, $birth_day, $birth_year);

   
    
    try {
        $stmt->execute();
        header("Location: createdsuc.php");
        exit(); 
    } catch (mysqli_sql_exception $e) {
       
        if ($e->getCode() == 1062) {  //Duplicate Entry
            header("Location: accexists.php");
            exit(); 
        } else {
            echo "Error: " . $e->getMessage();
        }
    }

    
    $stmt->close();
}

$con->close();
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Create Account Form</title>
        
        <link rel="stylesheet" type="text/css" href="http://localhost/badclone/cssfile/newaccss.css">
    </head>
    <body>
        <div class="overlay"></div>
        <div class="container">
        <form action="newacc.php" class="box" method="post">
            <div class="header">
                <div class="close-btn">
                    <a href="http://localhost/badclone/index.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <title>i</title>
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
            <h2>Create your account</h2>
            </div>
            <div class="form-group">
            <input type="text" id="name" name="name" placeholder="Name" required>
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="dob">
                <div class="dob-text">
                <label>Date of birth</label>
                <p>This will not be shown publicly. Confirm your own age, even if this account is for a business, a pet, or something else.</p>
                </div>
            <div class="mdy">    
            <select id="month" name="month" required>
                <option value="">Month</option>
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
            <select id="day" name="day" required>
                <option value="">Day</option>
                <?php for ($i = 1; $i <= 31; $i++) { ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
            </select>
            <select id="year" name="year" required>
                <option value="">Year</option>
                <?php for ($i = date('Y'); $i >= 1900; $i--) { ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
            </select>
            </div>
            </div>
            <div class="form-actions">         
            <button type="submit">Create</button>
             </div>
            <div class="already-account">
                <p>Have an account already? <a href="signin.php">Log in</a></p>
            </div>
        </form>
        
        </div>
        <?php
        // put your code here
        ?>
    </body>
</html>
