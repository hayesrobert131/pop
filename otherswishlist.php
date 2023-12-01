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

// Initialize variables for the search functionality
$searchUsername = '';
$foundWishlist = [];

// Check if the search form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    // Get the username entered in the search form
    $searchUsername = trim($_POST['search_username']);

    // TODO: Implement the logic to fetch wishlist for the specified user from the database
    // For now, let's assume you have a function to fetch wishlist by username
    // Replace 'getWishlistByUsername' with your actual function
    $foundWishlist = getWishlistByUsername($searchUsername);
}

// TODO: Implement the function to fetch wishlist by username from the database
function getWishlistByUsername($username)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT w.series, w.name, w.version 
            FROM wishlist w
            JOIN users u ON w.user_id = u.id
            WHERE u.username = ?";

    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $foundWishlist = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $foundWishlist;
    } else {
        echo "Error: Unable to prepare SQL statement.";
    }
}

// Function to check if a user exists
function userExists($username)
{
    global $dbConn;

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT COUNT(*) as userCount FROM users WHERE username = ?";
    
    $stmt = $dbConn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return ($row['userCount'] > 0);
    } else {
        echo "Error: Unable to prepare SQL statement.";
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Others Wishlist</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Add your additional styles here */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        /* Add styles for the search forms */
        #searchFormByName,
        #searchFormBySeries {
            display: none;
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
        <h2>Others Wishlist</h2>

        <!-- Search Form -->
        <form method="post" action="otherswishlist.php">
            <label for="search_username">Search by Username:</label>
            <input type="text" id="search_username" name="search_username" value="<?php echo $searchUsername; ?>" required>
            <button type="submit" name="search">Search</button>
        </form>

        <!-- Buttons to toggle search forms -->
        <button id="searchByNameBtn">Search by Name</button>
        <button id="searchBySeriesBtn">Search by Series</button>

        <!-- Form to search for items by name -->
        <div id="searchFormByName">
            <form action="otherswishlist.php" method="post">
                <label for="searchTermByName">Search Term:</label>
                <input type="text" id="searchTermByName" name="searchTerm" required><br>
                <button type="submit" name="search" value="name">Search</button>
            </form>
        </div>

        <!-- Form to search for items by series -->
        <div id="searchFormBySeries">
            <form action="otherswishlist.php" method="post">
                <label for="searchTermBySeries">Search Term:</label>
                <input type="text" id="searchTermBySeries" name="searchTerm" required><br>
                <button type="submit" name="search" value="series">Search</button>
            </form>
        </div>

        <!-- Display Found Wishlist -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])): ?>
            <?php if (!empty($foundWishlist)): ?>
                <h3>Wishlist for <?php echo $searchUsername; ?>:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Series</th>
                            <th>Name</th>
                            <th>Version</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($foundWishlist as $item): ?>
                            <tr>
                                <td><?php echo $item['series']; ?></td>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $item['version']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php if (userExists($searchUsername)): ?>
                    <p>No wishlist found for <?php echo $searchUsername; ?></p>
                <?php else: ?>
                    <p>User <?php echo $searchUsername; ?> doesn't exist.</p>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
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
</script>

</body>
</html>
