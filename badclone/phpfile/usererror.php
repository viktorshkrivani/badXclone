<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="http://localhost/badclone/cssfile/usererror.css">
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
                    <h1>User Not Found</h1>
                    <?php if (isset($_GET['username'])): ?>
                        <p>The user <?php echo $_GET['username']; ?> does not exist.</p>
                    <?php else: ?>
                        <p>The user does not exist.</p>
                    <?php endif; ?>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="location.href='http://localhost/badclone/index.php'">Sign Up</button>
                </div>
            </form>
        </div>
    </body>
</html>