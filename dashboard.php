<?php
// Assume that the user is logged in and you have their information stored in a session
session_start();

// Check if the user is logged in (you might have more robust authentication logic)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Get user information from the session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        <h2>Main Content Area</h2>
        <p>This for seeing peoples pop vinyls inventory and wishlist.</p>
		<p>With the format being name of the series the pop is from such has pokemon.</p>
		<p>The pops name eg charmander and version such as normal mettalic glitter glow in the dark.</p>
    </div>
</div>

</body>
</html>
