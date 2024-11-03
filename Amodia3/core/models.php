<?php
// Fetch user by ID
function fetchUserById($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE User_ID = :id");
    $stmt->execute(['id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Login function
function loginUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && isset($user['Password']) && password_verify($password, $user['Password'])) {
        return $user;
    }
    return false;
}

// Register user
function registerUser($pdo, $username, $password) {
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE Username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        return false; // Username already exists
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert the new user
    $stmt = $pdo->prepare("INSERT INTO Users (Username, Password) VALUES (:username, :password)");
    return $stmt->execute(['username' => $username, 'password' => $hashedPassword]);
}

// Fetch all Baristas
function fetchAllBaristas($pdo) {
    $stmt = $pdo->query("SELECT * FROM Barista");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch all Coffees
function fetchAllCoffees($pdo) {
    $stmt = $pdo->query("SELECT c.Coffee_ID, c.Coffee_Menu, c.Coffee_Cost, b.Barista_Name, c.date_added, c.last_updated, u.Username AS Added_By, c.updated_by
                         FROM Coffee c
                         LEFT JOIN Barista b ON c.Barista_ID = b.Barista_ID
                         LEFT JOIN Users u ON c.added_by = u.User_ID");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch Coffee by ID
function fetchCoffeeById($pdo, $coffeeId) {
    $stmt = $pdo->prepare("SELECT * FROM Coffee WHERE Coffee_ID = :coffeeId");
    $stmt->execute(['coffeeId' => $coffeeId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update Coffee
function updateCoffee($pdo, $coffeeId, $menu, $cost, $userId) {
    $stmt = $pdo->prepare("UPDATE Coffee SET Coffee_Menu = :menu, Coffee_Cost = :cost, updated_by = :updatedBy, last_updated = CURRENT_TIMESTAMP WHERE Coffee_ID = :coffeeId");
    return $stmt->execute(['menu' => $menu, 'cost' => $cost, 'updatedBy' => $userId, 'coffeeId' => $coffeeId]);
}

// Insert Barista
function insertBarista($pdo, $name, $specialty, $userId) {
    // Check if the user ID exists before inserting the barista
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE User_ID = :userId");
    $stmt->execute(['userId' => $userId]);
    if (!$stmt->fetch()) {
        throw new Exception("User ID does not exist.");
    }

    $stmt = $pdo->prepare("INSERT INTO Barista (Barista_Name, Barista_Specialty, added_by) VALUES (:name, :specialty, :added_by)");
    return $stmt->execute(['name' => $name, 'specialty' => $specialty, 'added_by' => $userId]);
}

// Insert Coffee
function insertCoffee($pdo, $menu, $baristaId, $cost, $userId) {
    $stmt = $pdo->prepare("INSERT INTO Coffee (Coffee_Menu, Barista_ID, Coffee_Cost, added_by) VALUES (:menu, :baristaId, :cost, :added_by)");
    return $stmt->execute(['menu' => $menu, 'baristaId' => $baristaId, 'cost' => $cost, 'added_by' => $userId]);
}

// Update Barista
function updateBarista($pdo, $name, $specialty, $baristaId, $userId) {
    $stmt = $pdo->prepare("UPDATE Barista SET Barista_Name = :name, Barista_Specialty = :specialty, last_updated = CURRENT_TIMESTAMP, updated_by = :updatedBy WHERE Barista_ID = :baristaId");
    return $stmt->execute(['name' => $name, 'specialty' => $specialty, 'baristaId' => $baristaId, 'updatedBy' => $userId]);
}

// Delete Barista
function deleteBarista($pdo, $baristaId) {
    // First, delete related coffee records
    $stmt = $pdo->prepare("DELETE FROM Coffee WHERE Barista_ID = :baristaId");
    $stmt->execute(['baristaId' => $baristaId]);

    // Then, delete the barista record
    $stmt = $pdo->prepare("DELETE FROM Barista WHERE Barista_ID = :baristaId");
    return $stmt->execute(['baristaId' => $baristaId]);
}

// Delete Coffee
function deleteCoffee($pdo, $coffeeId) {
    $stmt = $pdo->prepare("DELETE FROM Coffee WHERE Coffee_ID = :coffeeId");
    return $stmt->execute(['coffeeId' => $coffeeId]);
}
?>
