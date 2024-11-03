<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../core/dbConfig.php'; // Adjusted path
require_once '../core/models.php'; // Adjusted path

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); // Redirect to login page
    exit();
}

$userId = $_SESSION['user_id'];

// Handle barista insertion
if (isset($_POST['insertBBtn'])) {
    $query = insertBarista($pdo, $_POST['Barista_Name'], $_POST['Barista_Specialty'], $userId);
    
    if ($query) {
        header("Location: ../index.php");
        exit();
    } else {
        echo "Insertion failed: " . implode(", ", $pdo->errorInfo());
    }
}

// Handle barista update
if (isset($_POST['editBBtn'])) {
    $query = updateBarista($pdo, $_POST['Barista_Name'], $_POST['Barista_Specialty'], $_GET['Barista_ID'], $userId);
    
    if ($query) {
        header("Location: ../index.php");
        exit();
    } else {
        echo "Edit failed: " . implode(", ", $pdo->errorInfo());
    }
}

// Handle barista deletion
if (isset($_POST['deleteBBtn'])) {
    $baristaId = $_POST['barista_id']; // Use POST for barista ID
    $query = deleteBarista($pdo, $baristaId);
    
    if ($query) {
        header("Location: ../index.php");
        exit();
    } else {
        echo "Deletion failed: " . implode(", ", $pdo->errorInfo());
    }
}

// Handle coffee insertion
if (isset($_POST['insertNewCBtn'])) {
    $query = insertCoffee($pdo, $_POST['Coffee_Menu'], $_POST['Barista_ID'], $_POST['Coffee_Cost'], $userId);
    
    if ($query) {
        header("Location: ../viewcoffee.php?Barista_ID=" . $_POST['Barista_ID']);
        exit();
    } else {
        echo "Insertion failed: " . implode(", ", $pdo->errorInfo());
    }
}

// Handle coffee update
if (isset($_POST['editCBtn'])) {
    $query = updateCoffee($pdo, $_POST['Coffee_Menu'], $_POST['Coffee_Cost'], $_GET['Coffee_ID'], $userId);
    
    if ($query) {
        header("Location: ../viewcoffee.php?Barista_ID=" . $_GET['Barista_ID']);
        exit();
    } else {
        echo "Update failed: " . implode(", ", $pdo->errorInfo());
    }
}

// Handle coffee deletion
if (isset($_POST['deleteCBtn'])) {
    $coffeeId = $_POST['Coffee_ID']; // Use POST for coffee ID
    $query = deleteCoffee($pdo, $coffeeId);
    
    if ($query) {
        header("Location: ../viewcoffee.php?Barista_ID=" . $_POST['Barista_ID']);
        exit();
    } else {
        echo "Deletion failed: " . implode(", ", $pdo->errorInfo());
    }
}
?>
