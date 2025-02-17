<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: login.php");
}

require_once "database.php";

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
    <h2 style="text-align: center;"><a href="index.php" style="color: #fff; text-decoration: none;">Home</a></h2>
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
        <h2>Welcome to CPG2 Banking</h2>
        <div class="dashboard-info">
            <?php
            if(isset($_SESSION['user']['email'])) {
                $email = $_SESSION['user']['email'];

                $loggedInEmail = $conn->real_escape_string($email);

                $sql = "SELECT AccountNumber, Balance, LastName, FirstName, MiddleName FROM Accounts WHERE Email = '$loggedInEmail'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $accountNumber = $row["AccountNumber"];
                    $currentBalance = $row["Balance"];
                    $fullName = $row["FirstName"] . " " . $row["MiddleName"] . " " . $row["LastName"];

                    echo "<div class='alert alert-success'><h4><strong>Account Name:</strong> $fullName</div>";
                    echo "<div class='alert alert-success'><h4><strong>Account Number:</strong> $accountNumber</div>";
                    echo "<div class='alert alert-success'><h4><strong>Current Balance:</strong> $currentBalance</div>";
                } else {
                    echo "<div class='alert alert-danger'>User not found or database error.";
                }
            } else {
                echo "<div class='alert alert-danger'>Email not provided.";
            }
            ?>
        </div>
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
