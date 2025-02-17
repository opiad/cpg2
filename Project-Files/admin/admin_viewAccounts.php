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
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
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
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin: 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
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
        <h1>Banking Accounts</h1>
        <div class="dashboard-info">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="search-form">
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
                    echo "<div class='alert alert-success'>Account details updated successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Failed to update account details.</div>";
                }
            }

            if (isset($_POST['search'])) {
                $searchTerm = $_POST['search'];
                $searchTerm = $conn->real_escape_string($searchTerm);
                $sql = "SELECT * FROM Accounts WHERE AccountNumber LIKE '%$searchTerm%'";
            } else {
                $sql = "SELECT * FROM Accounts";
            }

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Account Number</th><th>Pin</th><th>First Name</th><th>Last Name</th><th>Middle Name</th><th>Email</th><th>Balance</th><th>Action</th></tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='post'>";
                    echo "<tr>";
                    echo "<input type='hidden' name='accountNumber' value='" . $row['AccountNumber'] . "'>";
                    echo "<td>" . $row['AccountNumber'] . "</td>";
                    echo "<td><input type='text' name='pin' value='" . $row['Pin'] . "'></td>";
                    echo "<td><input type='text' name='first_name' value='" . $row['FirstName'] . "'></td>";
                    echo "<td><input type='text' name='last_name' value='" . $row['LastName'] . "'></td>";
                    echo "<td><input type='text' name='middle_name' value='" . $row['MiddleName'] . "'></td>";
                    echo "<td><input type='text' name='email' value='" . $row['Email'] . "'></td>";
                    echo "<td><input type='number' name='balance' value='" . $row['Balance'] . "'></td>";
                    echo "<td><input type='submit' name='edit_account' value='Edit'></td>";
                    echo "</tr>";
                    echo "</form>";
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
