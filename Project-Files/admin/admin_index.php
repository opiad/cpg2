<?php
session_start();
if (!isset($_SESSION["user"])) {
   header("Location: admin_index.php");
}

require_once "database.php";

?>

<!DOCTYPE html>
<html>
<head>
    <title>CPG2 Admin Panel</title>
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
    <h2 style="text-align: center;"><a href="admin_index.php" style="color: #fff; text-decoration: none;">Home</a></h2>
    <div class="dropdown" id="accountsDropdown">
        <a href="#" onclick="toggleDropdown('accounts')" class="dropdown-toggle">Accounts</a>
        <div class="dropdown-content" id="transactionsContent">
            <a href="admin_viewAccounts.php">View All Accounts</a>
            <a href="admin_deleteAccounts.php">Delete Accounts</a>
        </div>
    </div>
    <a href="admin_transactions.php">View Transactions</a>
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
        <h2>Welcome Admin</h2>
        <div class="dashboard-info">
            <?php
            if(isset($_SESSION['user']['Username'])) {
                $username = $_SESSION['user']['Username'];

                $loggedInUsername = $conn->real_escape_string($username);

                $sql = "SELECT * FROM users WHERE Username = '$loggedInUsername'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $fullName = $row["FirstName"] . " " . $row["LastName"];

                    echo "<div class='alert alert-success'><h4><strong>Admin Name:</strong> $fullName</div>";
                } else {
                    echo "<div class='alert alert-danger'>User not found or database error.";
                }
            } else {
                echo "<div class='alert alert-danger'>Username not provided.";
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
