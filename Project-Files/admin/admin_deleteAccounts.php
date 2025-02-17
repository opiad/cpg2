<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: admin_login.php");
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
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        input[type="text"] {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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
        <h1>Delete Accounts</h1>
        <div class="dashboard-info">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="text" name="search" placeholder="Search by Account Number">
                <input type="submit" value="Search">
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_account'])) {
                $accountNumber = $_POST['accountNumber'];
                $newPin = $_POST['pin'];
                $newFirstName = $_POST['first_name'];
                $newLastName = $_POST['last_name'];
                $newMiddleName = $_POST['middle_name'];
                $newEmail = $_POST['email'];
                $newBalance = $_POST['balance'];
            
                $updateQuery = "UPDATE Accounts SET Pin = ?, FirstName = ?, LastName = ?, MiddleName = ?, Email = ?, Balance = ? WHERE AccountNumber = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("ssssssd", $newPin, $newFirstName, $newLastName, $newMiddleName, $newEmail, $newBalance, $accountNumber);
            
                if ($stmt->execute()) {
                    $editSuccessMessage = "Account details updated successfully.";
                } else {
                    $editErrorMessage = "Failed to update account details.";
                }
            }
            
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
                $searchTerm = $_POST['search'];
                $searchTerm = $conn->real_escape_string($searchTerm);
                $sql = "SELECT * FROM Accounts WHERE AccountNumber LIKE '%$searchTerm%'";
            } else {
                $sql = "SELECT * FROM Accounts";
            }
            
            $result = $conn->query($sql);
            ?>
            <?php
            if (isset($editSuccessMessage)) {
                echo "<div class='alert alert-success'>$editSuccessMessage</div>";
            }
            if (isset($editErrorMessage)) {
                echo "<div class='alert alert-danger'>$editErrorMessage</div>";
            }
            if ($result->num_rows > 0) {
                echo "<table border='1'>";
                echo "<tr><th>Account Number</th><th>Pin</th><th>Full Name</th><th>Email</th><th>Date Created</th><th>Action</th></tr>";

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['AccountNumber'] . "</td>";
                    echo "<td>" . $row['Pin'] . "</td>";
                    echo "<td>" . $row['LastName'] . " " . $row['FirstName'] . " " . $row['MiddleName'] . "</td>";
                    echo "<td>" . $row['Email'] . "</td>";
                    echo "<td>" . $row['DateCreated'] . "</td>";
                    echo "<td><button type='submit' name='delete_account' value='" . $row['AccountNumber'] . "'>Delete</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "No accounts found.";
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
