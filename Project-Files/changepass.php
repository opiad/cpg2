<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

$currentPasswordErr = $newPasswordErr = $confirmPasswordErr = "";
$currentPassword = $newPassword = $confirmPassword = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    $accountNumber = $_SESSION["user"]["AccountNumber"];

    $sql = "SELECT Password FROM Accounts WHERE AccountNumber = '$accountNumber'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($currentPassword !== $row["Password"]) {
            $currentPasswordErr = "Incorrect current password";
        }
    }

    if (empty($newPassword)) {
        $newPasswordErr = "New password is required";
    } elseif (strlen($newPassword) < 8) {
        $newPasswordErr = "Password must be at least 8 characters long";
    }

    if ($newPassword !== $confirmPassword) {
        $confirmPasswordErr = "Passwords do not match";
    }

    if (empty($currentPasswordErr) && empty($newPasswordErr) && empty($confirmPasswordErr)) {
        $sql = "UPDATE Accounts SET Password = '$newPassword' WHERE AccountNumber = '$accountNumber'";
        if ($conn->query($sql) === TRUE) {
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            $errorMessage = "Error updating password. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CPG2 Online Banking</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 200px;
            background-color: #007bff;
            overflow-y: auto;
            padding-top: 20px;
            z-index: 1000;
        }
        .main-content {
            margin-left: 200px;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <h2><a href="index.php">Home</a></h2>
    <div class="dropdown" id="transactionsDropdown">
        <a href="#" onclick="toggleDropdown('transactions')" class="dropdown-toggle">Transactions</a>
        <div class="dropdown-content" id="transactionsContent">
            <a href="transactions.php">View Transactions</a>
            <a href="deposit.php">Deposit</a>
            <a href="transfer.php">Bank Transfer</a>
            <a href="withdraw.php">Withdraw</a>
        </div>
    </div>
    <div class="dropdown" id="accountManagementDropdown">
        <a href="#" onclick="toggleDropdown('accountManagement')" class="dropdown-toggle">Account Management</a>
        <div class="dropdown-content" id="accountManagementContent">
            <a href="manage.php">Account Details</a>
            <a href="changepass.php">Change Password</a>
        </div>
    </div>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
<div class="container">
    <h2>Change Password</h2>
    <?php
    if (isset($errorMessage)) {
        echo "<div class='alert alert-danger'>$errorMessage</div>";
    }
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Enter Current Password" required>
            <span class="error"><?php echo $currentPasswordErr; ?></span>
        </div>
        <div class="form-group">
            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter New Password" required>
            <span class="error"><?php echo $newPasswordErr; ?></span>
        </div>
        <div class="form-group">
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm New Password" required>
            <span class="error"><?php echo $confirmPasswordErr; ?></span>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
    </form>
</div>
</div>

<script>
    function toggleDropdown(dropdownId) {
        var dropdown = document.getElementById(dropdownId + 'Dropdown');
        dropdown.classList.toggle('active');
    }
</script>

</body>
</html>