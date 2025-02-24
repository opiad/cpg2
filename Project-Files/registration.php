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
        <h1>CPG2 Online Banking - Registration</h1>
        <div class="form-container">
            <form action="registration.php" method="post">
            <?php
        if (isset($_POST["submit"])) {
            $lastName = $_POST['lastname'];
            $firstName = $_POST['firstname'];
            $middleName = $_POST['middlename'];
            $password = $_POST['password'];
            $passwordRepeat = $_POST['repeat_password'];
            $email = $_POST['email'];
            $pinNumber = $_POST['pin'];

           $errors = array();
           
           if (empty($lastName) OR empty($firstName) OR empty($middleName) OR empty($email) OR empty($password) OR empty($passwordRepeat) OR empty($pinNumber)) {
            array_push($errors,"All fields are required");
           }
           if (strlen($password)<8) {
            array_push($errors,"Password must be at least 8 charactes long");
           }
           if ($password!==$passwordRepeat) {
            array_push($errors,"Password does not match");
           }
           require_once "database.php";
           $sql = "SELECT * FROM accounts WHERE email = '$email'";
           $result = mysqli_query($conn, $sql);
           $rowCount = mysqli_num_rows($result);
           if ($rowCount>0) {
            array_push($errors,"Email already exists!");
           }
           if (count($errors)>0) {
            foreach ($errors as  $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
           }else {
            
            $sql = "INSERT INTO Accounts (Pin, LastName, FirstName, MiddleName, Email, Password) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "ssssss", $pinNumber, $lastName, $firstName, $middleName, $email, $password);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>You are registered successfully.</div>";
            }else{
                die("Something went wrong");
            }
           }
        }
        ?>
                <div class="form-group">
                    <input type="text" placeholder="Last Name" name="lastname">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="First Name" name="firstname">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="Middle Name" name="middlename">
                </div>
                <div class="form-group">
                    <input type="email" placeholder="Email" name="email">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="Pin Number" name="pin">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Password" name="password">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Confirm Password" name="repeat_password">
                </div>
                <div class="form-group">
                    <input type="submit" value="Register" name="submit" class="btn-primary">
                </div>
            </form>
        </div>
        <div>
            <p>Already Registered? <a href="login.php">Login Here</a></p>
        </div>
    </div>
</body>
</html>
