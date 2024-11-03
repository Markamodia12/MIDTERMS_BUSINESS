<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Handle the update process
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateBaristaBtn'])) {
    $baristaId = $_POST['barista_id'];
    $name = trim($_POST['baristaName']);
    $specialty = trim($_POST['baristaSpecialty']);

    // Check if fields are not empty
    if (!empty($name) && !empty($specialty)) {
        // Update barista in the database
        $query = updateBarista($pdo, $name, $specialty, $baristaId, $userId);
        
        if ($query) {
            header('Location: index.php'); // Redirect after successful update
            exit();
        } else {
            echo "Update failed: " . implode(", ", $pdo->errorInfo());
        }
    } else {
        echo "Please fill in all fields.";
    }
}

// Fetch the barista to edit
$baristaId = $_GET['Barista_ID'] ?? null; // Ensure this matches the parameter used in the index.php
$stmt = $pdo->prepare("SELECT * FROM Barista WHERE Barista_ID = :baristaId");
$stmt->execute(['baristaId' => $baristaId]);
$barista = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if barista not found
if (!$barista) {
    header('Location: index.php'); // Redirect if not found
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barista</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Edit Barista</h2>
    <form action="" method="POST">
        <input type="hidden" name="barista_id" value="<?php echo htmlspecialchars($barista['Barista_ID']); ?>">
        <label for="baristaName">Barista Name:</label>
        <input type="text" name="baristaName" value="<?php echo htmlspecialchars($barista['Barista_Name']); ?>" required>
        <label for="baristaSpecialty">Specialty:</label>
        <input type="text" name="baristaSpecialty" value="<?php echo htmlspecialchars($barista['Barista_Specialty']); ?>" required>
        <input type="submit" name="updateBaristaBtn" value="Update Barista">
    </form>
    <p><a href="index.php">Back to Coffee Shop Management</a></p>
</body>
</html>
