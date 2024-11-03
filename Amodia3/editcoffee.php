<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateCoffeeBtn'])) {
    $coffeeId = $_POST['coffee_id'];
    $menu = trim($_POST['coffeeMenu']);
    $baristaId = $_POST['baristaID'];
    $cost = (int)$_POST['coffeeCost'];

    // Validate input
    if (!empty($menu) && !empty($baristaId) && $cost >= 0) {
        // Update coffee with user ID for tracking who updated it
        $stmt = $pdo->prepare("UPDATE Coffee SET Coffee_Menu = :menu, Barista_ID = :baristaId, Coffee_Cost = :cost, updated_by = :userId, last_updated = CURRENT_TIMESTAMP WHERE Coffee_ID = :coffeeId");
        if ($stmt->execute(['menu' => $menu, 'baristaId' => $baristaId, 'cost' => $cost, 'userId' => $userId, 'coffeeId' => $coffeeId])) {
            header('Location: index.php'); // Redirect after successful update
            exit();
        }
    }
}

// Fetch the coffee to edit using the fetchCoffeeById function
$coffeeId = $_GET['coffee_id'] ?? null;
$coffee = fetchCoffeeById($pdo, $coffeeId);

if (!$coffee) {
    header('Location: index.php'); // Redirect if not found
    exit();
}

$baristas = fetchAllBaristas($pdo); // Fetch all baristas for the dropdown
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coffee</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Edit Coffee</h2>
    <form action="" method="POST">
        <input type="hidden" name="coffee_id" value="<?php echo htmlspecialchars($coffee['Coffee_ID']); ?>">
        <label for="coffeeMenu">Coffee Menu:</label>
        <input type="text" name="coffeeMenu" value="<?php echo htmlspecialchars($coffee['Coffee_Menu']); ?>" required>
        <label for="baristaID">Barista:</label>
        <select name="baristaID" required>
            <option value="">Select a Barista</option>
            <?php foreach ($baristas as $barista): ?>
                <option value="<?php echo htmlspecialchars($barista['Barista_ID']); ?>" <?php echo $barista['Barista_ID'] == $coffee['Barista_ID'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($barista['Barista_Name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="coffeeCost">Coffee Cost:</label>
        <input type="number" name="coffeeCost" value="<?php echo htmlspecialchars($coffee['Coffee_Cost']); ?>" required>
        <input type="submit" name="updateCoffeeBtn" value="Update Coffee">
    </form>
    
    <h3>Last Updated By:</h3>
    <p>
        <?php 
            $lastUpdatedByUser = fetchUserById($pdo, $coffee['updated_by']);
            echo htmlspecialchars($lastUpdatedByUser ? $lastUpdatedByUser['Username'] : 'Unknown');
        ?>
    </p>
    
    <h3>Last Updated At:</h3>
    <p><?php echo htmlspecialchars($coffee['last_updated'] ?? 'Not updated'); ?></p>

    <p><a href="index.php">Back to Coffee Shop Management</a></p>
</body>
</html>
