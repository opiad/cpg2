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
        <h1>Transactions</h1>
        <div class="dashboard-info">
            <?php
            if(isset($_SESSION['user']['email'])) {
                $AccountID = $_SESSION['user']['AccountNumber'];

                $loggedInAccountID = $conn->real_escape_string($AccountID);

                $sql = "SELECT * FROM Transactions WHERE AccountID = '$loggedInAccountID'";
                $result = $conn->query($sql);

                if (mysqli_num_rows($result) > 0) {
                    echo "<table border='1'>";
                    echo "<tr><th>Transaction ID</th><th>Date</th><th>Amount</th><th>Remarks</th><th>Type</th></tr>";
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['TransactionID'] . "</td>";
                        echo "<td>" . $row['DateCreated'] . "</td>";
                        echo "<td>" . $row['Amount'] . "</td>";
                        echo "<td>" . $row['Remarks'] . "</td>";

                        $type = $row['Type'];
                        if ($type == 1) {
                            $transaction_type = "Deposit";
                        } elseif ($type == 2) {
                            $transaction_type = "Withdraw";
                        } elseif ($type == 3) {
                            $transaction_type = "Transfer";
                        } else {
                            $transaction_type = "Unknown";
                        }
                        echo "<td>" . $transaction_type . "</td>";
                        
                        echo "</tr>";
                    }
                    
                    echo "</table>";
                } else {
                    echo "No transactions found.";
                }
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
