<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: admin_index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPG2 Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>CPG2 Online Banking Admin</h1>
        <div class="form-container">
            <form action="admin_login.php" method="post">
                <?php
                if (isset($_POST["login"])) {
                   $username = $_POST["Username"];
                   $password = $_POST["Password"];
                    require_once "database.php";
                    $sql = "SELECT * FROM users WHERE Username = '$username' AND isAdmin = 1";
                    $result = mysqli_query($conn, $sql);
                    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    if ($user) {
                        if ($password == $user["Password"]) {
                            session_start();
                            $_SESSION["user"] = $user;
                            $_SESSION['user']['email'] = $email;
                            header("Location: admin_index.php");
                        } else {
                            echo "<div class='alert alert-danger'>Incorrect password</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Username not found</div>";
                    }
                }
                ?>
                <div class="form-group">
                    <input type="text" placeholder="Enter Username" name="Username">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Enter Password" name="Password">
                </div>
                <div class="form-group">
                    <input type="submit" value="Login" name="login" class="btn-primary">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
