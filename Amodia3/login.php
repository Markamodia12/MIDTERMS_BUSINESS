<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

session_start(); // Start the session at the very top

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['loginBtn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $user = loginUser($pdo, $username, $password); // Call your login function

        if ($user) {
            $_SESSION['user_id'] = $user['User_ID']; // Store user ID in session (ensure the correct column name)
            header('Location: index.php'); // Redirect to the main page upon successful login
            exit();
        } else {
            $errorMessage = "Invalid username or password."; // Error message for invalid login
        }
    } else {
        $errorMessage = "Please fill in both fields."; // Error message for empty fields
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Login</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="login-container">
        <h1>Welcome to Kapihan ni Mark</h1>
        <?php if ($errorMessage): ?>
            <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="loginBtn">Login</button>
        </form>
        
        <div class="register-container">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
