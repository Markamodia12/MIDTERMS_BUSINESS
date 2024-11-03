<?php   
require_once 'core/dbConfig.php';
require_once 'core/models.php';

session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$user = fetchUserById($pdo, $userId);
$baristas = fetchAllBaristas($pdo);
$coffees = fetchAllCoffees($pdo);

$successMessage = '';
$errorMessage = '';

// Handle Barista insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insertNewBaristaBtn'])) {
    $name = trim($_POST['baristaName']);
    $specialty = trim($_POST['baristaSpecialty']);
    
    if (!empty($name) && !empty($specialty)) {
        if (insertBarista($pdo, $name, $specialty, $userId)) {
            $successMessage = "Barista added successfully!";
            $baristas = fetchAllBaristas($pdo); // Refresh list
        } else {
            $errorMessage = "Failed to add barista.";
        }
    } else {
        $errorMessage = "Please fill in all fields.";
    }
}

// Handle Coffee insertion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['insertNewCoffeeBtn'])) {
    $menu = trim($_POST['coffeeMenu']);
    $baristaId = trim($_POST['baristaID']);
    $cost = (int)$_POST['coffeeCost'];
    
    if (!empty($menu) && !empty($baristaId) && $cost >= 0) {
        if (insertCoffee($pdo, $menu, $baristaId, $cost, $userId)) {
            $successMessage = "Coffee added successfully!";
            $coffees = fetchAllCoffees($pdo); // Refresh list
        } else {
            $errorMessage = "Failed to add coffee.";
        }
    } else {
        $errorMessage = "Please fill in all fields.";
    }
}

// Handle Coffee deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteCoffeeBtn'])) {
    $coffeeId = $_POST['coffee_id'];
    deleteCoffee($pdo, $coffeeId); // Using deleteCoffee function
    header('Location: index.php'); // Redirect after deletion
    exit();
}

// Handle Barista deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteBaristaBtn'])) {
    $baristaId = $_POST['barista_id'];
    deleteBarista($pdo, $baristaId); // Using deleteBarista function
    header('Location: index.php'); // Redirect after deletion
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($user['Username']); ?>!</h1>
        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit" name="logoutBtn" class="small-button">Logout</button>
        </form>
    </div>

    <div class="container">
        <?php if ($successMessage): ?>
            <div class="message success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="message error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <h2>Add New Barista</h2>
        <form action="" method="POST">
            <input type="text" name="baristaName" placeholder="Barista Name" required>
            <input type="text" name="baristaSpecialty" placeholder="Specialty" required>
            <button type="submit" name="insertNewBaristaBtn">Add Barista</button>
        </form>

        <h2>Barista List</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Specialty</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($baristas as $barista): ?>
                <tr>
                    <td><?php echo htmlspecialchars($barista['Barista_Name']); ?></td>
                    <td><?php echo htmlspecialchars($barista['Barista_Specialty']); ?></td>
                    <td>
                        <a href="editbarista.php?Barista_ID=<?php echo $barista['Barista_ID']; ?>">Edit</a>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="barista_id" value="<?php echo $barista['Barista_ID']; ?>">
                            <button type="submit" name="deleteBaristaBtn" onclick="return confirm('Are you sure you want to delete this barista?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Add New Coffee</h2>
        <form action="" method="POST">
            <input type="text" name="coffeeMenu" placeholder="Coffee Menu" required>
            <input type="number" name="coffeeCost" placeholder="Cost" required>
            <select name="baristaID" required>
                <option value="">Select Barista</option>
                <?php foreach ($baristas as $barista): ?>
                    <option value="<?php echo $barista['Barista_ID']; ?>"><?php echo htmlspecialchars($barista['Barista_Name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="insertNewCoffeeBtn">Add Coffee</button>
        </form>

        <h2>Coffee List</h2>
        <table>
            <tr>
                <th>Menu</th>
                <th>Cost</th>
                <th>Added By</th>
                <th>Last Updated By</th>
                <th>Last Updated At</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($coffees as $coffee): ?>
                <tr>
                    <td><?php echo htmlspecialchars($coffee['Coffee_Menu']); ?></td>
                    <td>$<?php echo htmlspecialchars($coffee['Coffee_Cost']); ?></td>
                    <td><?php echo htmlspecialchars($coffee['Added_By']); ?></td>
                    <td>
                        <?php 
                            // Fetch the username of the last updater
                            $lastUpdatedByUser = fetchUserById($pdo, $coffee['updated_by']);
                            echo htmlspecialchars($lastUpdatedByUser ? $lastUpdatedByUser['Username'] : 'Unknown');
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($coffee['last_updated'] ?? 'Not updated'); ?></td>
                    <td>
                        <a href="editcoffee.php?coffee_id=<?php echo $coffee['Coffee_ID']; ?>">Edit</a>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="coffee_id" value="<?php echo $coffee['Coffee_ID']; ?>">
                            <button type="submit" name="deleteCoffeeBtn" onclick="return confirm('Are you sure you want to delete this coffee?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
