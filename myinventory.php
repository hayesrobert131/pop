<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user information from the session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Database connection parameters
$host = "localhost";
$dbUsername = "root";
$password = "";
$database = "poke";

// Create a MySQLi connection
$dbConn = new mysqli($host, $dbUsername, $password, $database);

// Check for connection errors
if ($dbConn->connect_error) {
    die("Failed to connect to the database: " . $dbConn->connect_error);
}

// Fetch user's inventory from the database
$inventory = fetchInventory($user_id);

// Function to fetch user's inventory from the database
function fetchInventory($user_id)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT series, name, version FROM inventory WHERE user_id = ?";
    
    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $inventory = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $inventory;
    } else {
        echo "Error: Unable to prepare SQL statement.";
        return [];
    }
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    // Process the form submission and insert data into the database
    $series = $_POST['series'];
    $name = $_POST['name'];
    $version = $_POST['version'];

    // Validate input if needed

    // Insert data into the database
    insertInventoryItem($user_id, $series, $name, $version);
}

// Check if search is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchType = $_POST['searchType'];
    $searchTerm = $_POST['searchTerm'];

    // Fetch filtered inventory based on search type and term
    $inventory = fetchFilteredInventory($user_id, $searchType, $searchTerm);
}

// Function to insert data into the inventory
function insertInventoryItem($user_id, $series, $name, $version)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO inventory (user_id, series, name, version) VALUES (?, ?, ?, ?)";
    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("isss", $user_id, $series, $name, $version);
        $stmt->execute();

        // Check for successful insertion
        if ($stmt->affected_rows > 0) {
            // Insertion successful
            // Optionally, you can redirect to prevent form resubmission
            // header('Location: myinventory.php');
            // exit();
        } else {
            // Insertion failed
            echo "Error: Unable to add item to inventory.";
        }

        $stmt->close();
    } else {
        echo "Error: Unable to prepare SQL statement.";
    }
}

// Function to fetch filtered inventory based on search type and term
function fetchFilteredInventory($user_id, $searchType, $searchTerm)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT series, name, version FROM inventory WHERE user_id = ? AND $searchType LIKE ?";
    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $searchTerm = "%$searchTerm%"; // Add wildcard characters for a partial match
        $stmt->bind_param("is", $user_id, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $inventory = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $inventory;
    } else {
        echo "Error: Unable to prepare SQL statement.";
        return [];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inventory</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Add your additional styles here */
        #addItemForm,
        #searchFormByName,
        #searchFormBySeries {
            display: none;
            margin-top: 20px;
        }

        #addItemForm label,
        #searchFormByName label,
        #searchFormBySeries label {
            margin-bottom: 8px;
        }

        #addItemForm input[type="text"],
        #addItemForm input[type="file"],
        #searchFormByName input[type="text"],
        #searchFormBySeries input[type="text"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #addItemForm button[type="submit"],
        #searchFormByName button[type="submit"],
        #searchFormBySeries button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #addItemForm button[type="submit"]:hover,
        #searchFormByName button[type="submit"]:hover,
        #searchFormBySeries button[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Styles for the current inventory display */
        #currentInventory {
            margin-top: 20px;
        }

        #currentInventory table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        #currentInventory th,
        #currentInventory td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        #currentInventory th {
            background-color: #4CAF50;
            color: white;
        }

        /* Styles for the buttons */
        #addItemBtn,
        #showInventoryBtn,
        #searchByNameBtn,
        #searchBySeriesBtn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #addItemBtn:hover,
        #showInventoryBtn:hover,
        #searchByNameBtn:hover,
        #searchBySeriesBtn:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <div class="dashboard-container">
        <div class="navigation-bar">
            <h2>Welcome, <?php echo $username; ?>!</h2>
            <ul>
                <li><a class="navigation-link" href="dashboard.php">Home</a></li>
                <li><a class="navigation-link" href="myinventory.php">My Inventory</a></li>
                <li><a class="navigation-link" href="mywishlist.php">Wishlist</a></li>
                <li><a class="navigation-link" href="othersinventory.php">Others Inventory</a></li>
                <li><a class="navigation-link" href="otherswishlist.php">Others Wishlist</a></li>
                <li><a class="navigation-link" href="settings.php">Settings</a></li>
                <li><a class="navigation-link" href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h2>My Inventory</h2>

            <!-- Button to toggle the visibility of the add item form -->
            <button id="addItemBtn">Add Item</button>

            <!-- Form to add a new item -->
            <div id="addItemForm">
                <form action="myinventory.php" method="post" enctype="multipart/form-data">
                    <label for="series">Series:</label>
                    <input type="text" id="series" name="series" required><br>

                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required><br>

                    <label for="version">Version:</label>
                    <input type="text" id="version" name="version" required><br>

                    <button type="submit" name="add_item">Add</button>
                </form>
            </div>

            <!-- Button to toggle the visibility of the user's current inventory -->
            <button id="showInventoryBtn">Show Inventory</button>

            <!-- Display the user's current inventory (initially hidden) -->
            <div id="currentInventory" style="display: none;">
                <!-- Search buttons -->
                <button id="searchByNameBtn">Search by Name</button>
                <button id="searchBySeriesBtn">Search by Series</button>

                <!-- Form to search for items by name -->
                <div id="searchFormByName">
                    <form action="myinventory.php" method="post">
                        <label for="searchTermByName">Search Term:</label>
                        <input type="text" id="searchTermByName" name="searchTerm" required><br>
                        <button type="submit" name="search" value="name">Search</button>
                    </form>
                </div>

                <!-- Form to search for items by series -->
                <div id="searchFormBySeries">
                    <form action="myinventory.php" method="post">
                        <label for="searchTermBySeries">Search Term:</label>
                        <input type="text" id="searchTermBySeries" name="searchTerm" required><br>
                        <button type="submit" name="search" value="series">Search</button>
                    </form>
                </div>

                <?php if (!empty($inventory)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Series</th>
                                <th>Name</th>
                                <th>Version</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory as $item): ?>
                                <tr>
                                    <td><?php echo $item['series']; ?></td>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['version']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Your inventory is empty.</p>
                <?php endif; ?>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // JavaScript to toggle the visibility of the add item form
                    document.getElementById('addItemBtn').addEventListener('click', function () {
                        var form = document.getElementById('addItemForm');
                        form.style.display = form.style.display === 'none' ? 'block' : 'none';
                    });

                    // JavaScript to toggle the visibility of the user's current inventory
                    document.getElementById('showInventoryBtn').addEventListener('click', function () {
                        var inventory = document.getElementById('currentInventory');
                        inventory.style.display = inventory.style.display === 'none' ? 'block' : 'none';
                    });

                    // JavaScript to toggle the visibility of the search form by name
                    document.getElementById('searchByNameBtn').addEventListener('click', function () {
                        var formByName = document.getElementById('searchFormByName');
                        var formBySeries = document.getElementById('searchFormBySeries');

                        formByName.style.display = formByName.style.display === 'none' ? 'block' : 'none';
                        formBySeries.style.display = 'none'; // Hide other search form
                    });

                    // JavaScript to toggle the visibility of the search form by series
                    document.getElementById('searchBySeriesBtn').addEventListener('click', function () {
                        var formBySeries = document.getElementById('searchFormBySeries');
                        var formByName = document.getElementById('searchFormByName');

                        formBySeries.style.display = formBySeries.style.display === 'none' ? 'block' : 'none';
                        formByName.style.display = 'none'; // Hide other search form
                    });
                });
            </script>
        </div>
    </div>
</body>

</html>
