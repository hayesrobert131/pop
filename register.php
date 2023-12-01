<?php
// Include the database connection script
include('dbconn.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate input
    $errors = [];

    if (empty($username)) {
        $errors[] = 'Username is required';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Check if the username is already taken
        $checkUsernameQuery = "SELECT * FROM users WHERE username = ?";
        $checkUsernameStmt = $dbConn->prepare($checkUsernameQuery);
        $checkUsernameStmt->bind_param("s", $username);
        $checkUsernameStmt->execute();
        $checkUsernameResult = $checkUsernameStmt->get_result();

        if ($checkUsernameResult->num_rows > 0) {
            $errors[] = 'Username is already taken. Please choose another.';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database
            $insertUserQuery = "INSERT INTO users (username, password) VALUES (?, ?)";
            $insertUserStmt = $dbConn->prepare($insertUserQuery);
            $insertUserStmt->bind_param("ss", $username, $hashedPassword);

            if ($insertUserStmt->execute()) {
                $registrationMessage = 'Registration successful! You can now login.';

                // Redirect to the login page after successful registration
                echo '<script>window.location.href = "login.php";</script>';
                exit();
            } else {
                $errors[] = 'Error while registering. Please try again.';
            }
        }
    }
}

// Close the database connection
$dbConn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-container">
    <h2>Register</h2>

    <?php if (isset($registrationMessage)): ?>
        <p style="color: green;"><?php echo $registrationMessage; ?></p>
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form class="login-form" action="register.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit" class="login-button">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>
