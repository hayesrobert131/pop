<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="style.css">
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
        <h2>Settings</h2>
        <!-- Your specific content for Settings goes here -->
    </div>
</div>

</body>
</html>
