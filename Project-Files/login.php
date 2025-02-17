<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPG2 Online Banking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>CPG2 Online Banking</h1>
        <div class="form-container">
            <form action="login.php" method="post">
                <?php
                if (isset($_POST["login"])) {
                   $email = $_POST["Email"];
                   $password = $_POST["Password"];
                    require_once "database.php";
                    $sql = "SELECT * FROM accounts WHERE Email = '$email'";
                    $result = mysqli_query($conn, $sql);
                    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    if ($user) {
                        if ($password == $user["Password"]) {
                            session_start();
                            $_SESSION["user"] = $user;
                            $_SESSION['user']['email'] = $email;
                            header("Location: index.php");
                            exit();
                        } else {
                            echo "<div class='alert alert-danger'>Password does not match</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Email does not match</div>";
                    }
                }
                ?>
                <div class="form-group">
                    <input type="email" placeholder="Enter Email" name="Email">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Enter Password" name="Password">
                </div>
                <div class="form-group">
                    <input type="submit" value="Login" name="login" class="btn-primary">
                </div>
            </form>
        </div>
        <div>
            <p>Not registered yet? <a href="registration.php">Register Here</a></p>
        </div>
    </div>
</body>
</html>
