<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: admin_login.php");
    exit();
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
            <a href="admin_editAccounts.php">Edit Accounts</a>
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
        <h1>Bank Transfer</h1>
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
        <div class="dashboard-info">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <input type="text" id="recipient_account_number" name="recipient_account_number" placeholder="Recipient Account Number">
                </div>
                <div class="form-group">
                    <input type="text" id="recipient_last_name" name="recipient_last_name" placeholder="Recipient Last Name">
                </div>
                <div class="form-group">
                    <input type="text" id="recipient_first_name" name="recipient_first_name" placeholder="Recipient First Name">
                </div>
                <div class="form-group">
                    <input type="text" id="recipient_middle_name" name="recipient_middle_name" placeholder="Recipient Middle Name">
                </div>
                <div class="form-group">
                    <input type="number" id="amount" name="amount" min="0" step="0.01" placeholder="Enter Amount">
                </div>
                <div class="form-group">
                    <input type="password" id="pin" name="pin" placeholder="Enter Your Pin Number">
                </div>
                <div class="form-group">
                    <input type="submit" value="Transfer" class="btn-primary">
                </div>
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $senderAccountNumber = $_SESSION["user"]["AccountNumber"];
                $amount = $_POST["amount"];
                $recipientAccountNumber = $_POST["recipient_account_number"];
                $recipientLastName = $_POST["recipient_last_name"];
                $recipientFirstName = $_POST["recipient_first_name"];
                $recipientMiddleName = $_POST["recipient_middle_name"];
                $pinNumber = $_POST["pin"];
            
                if (empty($amount) || empty($recipientAccountNumber) || empty($recipientLastName) || empty($recipientFirstName) || empty($recipientMiddleName) || empty($pinNumber)) {
                    echo "<div class='alert alert-danger'>All fields are required</div>";
                } else {
                    $recipientSql = "SELECT * FROM Accounts WHERE AccountNumber = '$recipientAccountNumber' AND LastName = '$recipientLastName' AND FirstName = '$recipientFirstName' AND MiddleName = '$recipientMiddleName'";
                    $recipientResult = $conn->query($recipientSql);
                    if ($recipientResult->num_rows > 0) {
                        $senderSql = "SELECT * FROM Accounts WHERE AccountNumber = '$senderAccountNumber' AND Pin = '$pinNumber'";
                        $senderResult = $conn->query($senderSql);
                        if ($senderResult->num_rows > 0) {
                            $senderRow = $senderResult->fetch_assoc();
                            if ($senderRow["Balance"] >= $amount) {
                                $newSenderBalance = $senderRow["Balance"] - $amount;
                                $updateSenderSql = "UPDATE Accounts SET Balance = '$newSenderBalance' WHERE AccountNumber = '$senderAccountNumber'";
                                if ($conn->query($updateSenderSql) === TRUE) {
                                    $recipientRow = $recipientResult->fetch_assoc();
                                    $newRecipientBalance = $recipientRow["Balance"] + $amount;
                                    $updateRecipientSql = "UPDATE Accounts SET Balance = '$newRecipientBalance' WHERE AccountNumber = '$recipientAccountNumber'";
                                    if ($conn->query($updateRecipientSql) === TRUE) {
                                        $transactionType = 3;
                                        $senderRemarks = "Transfer of " . $amount . " to " . $recipientLastName . ", " . $recipientFirstName . " " . $recipientMiddleName;
                                        $recipientRemarks = "Transfer from " . $_SESSION["user"]["LastName"] . ", " . $_SESSION["user"]["FirstName"] . " " . $_SESSION["user"]["MiddleName"];
                                        $insertSenderTransactionSql = "INSERT INTO Transactions (AccountID, Type, Amount, Remarks) VALUES ('$senderAccountNumber', '$transactionType', '-$amount', '$senderRemarks')";
                                        $insertRecipientTransactionSql = "INSERT INTO Transactions (AccountID, Type, Amount, Remarks) VALUES ('$recipientAccountNumber', '$transactionType', '$amount', '$recipientRemarks')";
                                        if ($conn->query($insertSenderTransactionSql) === TRUE && $conn->query($insertRecipientTransactionSql) === TRUE) {
                                            echo "<div class='alert alert-success'>Transfer of $amount successfully made.</div>";
                                        } else {
                                            echo "<div class='alert alert-danger'>Error inserting transaction records: " . $conn->error . "</div>";
                                        }
                                    } else {
                                        echo "<div class='alert alert-danger'>Error updating recipient's balance: " . $conn->error . "</div>";
                                    }
                                } else {
                                    echo "<div class='alert alert-danger'>Error updating sender's balance: " . $conn->error . "</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger'>Insufficient balance</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Invalid PIN number</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Recipient account does not exist</div>";
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
