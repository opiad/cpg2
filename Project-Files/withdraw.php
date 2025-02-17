<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
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
        <h1>Withdraw</h1>
        <div class="dashboard-info">
        <?php
            if(isset($_SESSION['user']['email'])) {
                $email = $_SESSION['user']['email'];

                $loggedInEmail = $conn->real_escape_string($email);

                $sql = "SELECT AccountNumber, Balance, LastName, FirstName, MiddleName FROM Accounts WHERE Email = '$loggedInEmail'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $currentBalance = $row["Balance"];

                    echo "<div class='alert alert-success'><h4><strong>Current Balance:</strong> $currentBalance</div>";
                } else {
                    echo "<div class='alert alert-danger'>User not found or database error.";
                }
            } else {
                echo "<div class='alert alert-danger'>Email not provided.";
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <input type="number" id="amount" name="amount" min="0" step="0.01" placeholder="Enter Amount">
                </div>
                <div class="form-group">
                    <input type="password" id="pin" name="pin" placeholder="Enter Your Pin Number">
                </div>
                <div class="form-group">
                    <input type="submit" value="Withdraw" class="btn-primary">
                </div>
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $accountNumber = $_SESSION["user"]["AccountNumber"];
                $amount = $_POST["amount"];
                $pinNumber = $_POST["pin"];
            
                if (empty($amount) || empty($pinNumber)) {
                    echo "<div class='alert alert-danger'>Amount and PIN are required</div>";
                } else {
                    $senderSql = "SELECT * FROM Accounts WHERE AccountNumber = '$accountNumber' AND Pin = '$pinNumber'";
                    $senderResult = $conn->query($senderSql);
                    if ($senderResult->num_rows > 0) {
                        $senderRow = $senderResult->fetch_assoc();
                        if ($senderRow["Balance"] >= $amount) {
                            $newBalance = $senderRow["Balance"] - $amount;
                            $updateSql = "UPDATE Accounts SET Balance = '$newBalance' WHERE AccountNumber = '$accountNumber'";
                            if ($conn->query($updateSql) === TRUE) {
                                $transactionType = 2;
                                $remarks = "Withdrawal of " . $amount;
                                $insertTransactionSql = "INSERT INTO Transactions (AccountID, Type, Amount, Remarks) VALUES ('$accountNumber', '$transactionType', '-$amount', '$remarks')";
                                if ($conn->query($insertTransactionSql) === TRUE) {
                                    echo "<div class='alert alert-success'>Withdrawal of $amount successfully made.</div>";
                                } else {
                                    echo "<div class='alert alert-danger'>Error inserting transaction record: " . $conn->error . "</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Error updating balance: " . $conn->error . "</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Insufficient balance</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Invalid PIN number</div>";
                    }
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
