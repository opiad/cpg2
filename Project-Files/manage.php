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
        <h2>User Details</h2>
        <div class="dashboard-info">
            <?php
            if(isset($_SESSION['user']['email'])) {
                $email = $_SESSION['user']['email'];

                $loggedInEmail = $conn->real_escape_string($email);

                $sql = "SELECT LastName, FirstName, MiddleName FROM Accounts WHERE Email = '$loggedInEmail'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $firstName = $row["FirstName"];
                    $middleName = $row["MiddleName"];
                    $lastName = $row["LastName"];
                } else {
                    echo "<div class='alert alert-danger'>User not found or database error.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Email not provided.</div>";
            }

            function updateUser($conn, $field, $value, $accountNumber) {
                $sql = "UPDATE Accounts SET $field = '$value' WHERE AccountNumber = '$accountNumber'";
                if ($conn->query($sql) === TRUE) {
                    return true;
                } else {
                    return false;
                }
            }
            
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $accountNumber = $_SESSION["user"]["AccountNumber"];
                $action = $_POST["action"];
            
                switch ($action) {
                    case 'change_firstname':
                        $newFirstName = $_POST["new_firstname"];
                        if (!empty($newFirstName)) {
                            if (updateUser($conn, "FirstName", $newFirstName, $accountNumber)) {
                                echo "<div class='alert alert-success'>First name changed successfully.</div>";
                                echo "<meta http-equiv='refresh' content='0'>";
                            } else {
                                echo "<div class='alert alert-danger'>Failed to change first name. Please try again later.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>New first name is required.</div>";
                        }
                        break;
            
                    case 'change_lastname':
                        $newLastName = $_POST["new_lastname"];
                        if (!empty($newLastName)) {
                            if (updateUser($conn, "LastName", $newLastName, $accountNumber)) {
                                echo "<div class='alert alert-success'>Last name changed successfully.</div>";
                                echo "<meta http-equiv='refresh' content='0'>";
                            } else {
                                echo "<div class='alert alert-danger'>Failed to change last name. Please try again later.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>New last name is required.</div>";
                        }
                        break;
            
                    case 'change_middlename':
                        $newMiddleName = $_POST["new_middlename"];
                        if (!empty($newMiddleName)) {
                            if (updateUser($conn, "MiddleName", $newMiddleName, $accountNumber)) {
                                echo "<div class='alert alert-success'>Middle name changed successfully.</div>";
                                echo "<meta http-equiv='refresh' content='0'>";
                            } else {
                                echo "<div class='alert alert-danger'>Failed to change middle name. Please try again later.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>New middle name is required.</div>";
                        }
                        break;
            
                    default:
                        echo "<div class='alert alert-danger'>Invalid action.</div>";
                        break;
                }
            }

            ?>
            
        </div>

        <div class="dashboard-info">
    <form id="updateForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="action" id="action">
        <input type="hidden" name="new_firstname" id="new_firstname">
        <input type="hidden" name="new_lastname" id="new_lastname">
        <input type="hidden" name="new_middlename" id="new_middlename">
        <h4>First Name</h4>
        <div class="form-group">
            <input type="text" id="firstname" name="firstname" placeholder="<?php echo htmlspecialchars($firstName); ?>">
        </div>
        <h4>Last Name</h4>
        <div class="form-group">
            <input type="text" id="lastname" name="lastname" placeholder="<?php echo htmlspecialchars($lastName); ?>">
        </div>
        <h4>Middle Name</h4>
        <div class="form-group">
            <input type="text" id="middlename" name="middlename" placeholder="<?php echo htmlspecialchars($middleName); ?>">
        </div>
        <div class="form-group">
            <input type="button" value="Save Changes" class="btn-primary" onclick="saveChanges()">
        </div>
    </form>
</div>

<script>
    function toggleDropdown(dropdownId) {
        var dropdown = document.getElementById(dropdownId + 'Dropdown');
        dropdown.classList.toggle('active');
    }
    function saveChanges() {
        var firstName = document.getElementById("firstname").value.trim();
        var lastName = document.getElementById("lastname").value.trim();
        var middleName = document.getElementById("middlename").value.trim();

        if (firstName === "" && lastName === "" && middleName === "") {
            alert("No changes detected.");
            return;
        }

        document.getElementById("new_firstname").value = firstName;
        document.getElementById("new_lastname").value = lastName;
        document.getElementById("new_middlename").value = middleName;

        if (firstName !== "<?php echo htmlspecialchars($firstName); ?>") {
            document.getElementById("action").value = "change_firstname";
        } else if (lastName !== "<?php echo htmlspecialchars($lastName); ?>") {
            document.getElementById("action").value = "change_lastname";
        } else if (middleName !== "<?php echo htmlspecialchars($middleName); ?>") {
            document.getElementById("action").value = "change_middlename";
        }

        document.getElementById("updateForm").submit();
    }
</script>
</body>
</html>