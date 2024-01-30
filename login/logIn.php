<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission and print user data
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users[] = ['name' => $username, 'password' => $password];

    print_r($users);

    // Handle database operations
    $databaseFile = 'logIn.db';

    try {
        // Create or open the SQLite database file
        $pdo = new PDO("sqlite:$databaseFile");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create the 'users' table if not exists
        $createTableSql = "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                password TEXT NOT NULL
            )
        ";

        $pdo->exec($createTableSql);

        // Insert user data into the 'users' table
        $insertDataSql = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $insertStmt = $pdo->prepare($insertDataSql);
        $insertStmt->bindParam(':username', $username);
        $insertStmt->bindParam(':password', $password);
        $insertStmt->execute();

        // Handle Update Operation
        if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id'])) {
            $userIdToUpdate = $_GET['id'];
            $newUsername = isset($_POST['new_username']) ? $_POST['new_username'] : '';
            $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';

            $updateDataSql = "UPDATE users SET username = :new_username, password = :new_password WHERE id = :id";
            $updateStmt = $pdo->prepare($updateDataSql);
            $updateStmt->bindParam(':new_username', $newUsername);
            $updateStmt->bindParam(':new_password', $newPassword);
            $updateStmt->bindParam(':id', $userIdToUpdate);
            $updateStmt->execute();

            echo "Record with ID $userIdToUpdate updated successfully.";
        }

        // Handle Delete Operation
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $userIdToDelete = $_GET['id'];

            $deleteDataSql = "DELETE FROM users WHERE id = :id";
            $deleteStmt = $pdo->prepare($deleteDataSql);
            $deleteStmt->bindParam(':id', $userIdToDelete);
            $deleteStmt->execute();

            echo "Record with ID $userIdToDelete deleted successfully.";
        }

        // Retrieve data from the 'users' table
        $selectDataSql = "SELECT * FROM users";
        $stmt = $pdo->query($selectDataSql);

        // Display the retrieved data
        echo "<h2>Users</h2>";
        echo "<ul>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>ID: {$row['id']}, Username: {$row['username']}, Password: {$row['password']} 
                <a href='?action=update&id={$row['id']}'>Update</a>
                <a href='?action=delete&id={$row['id']}'>Delete</a></li>";
        }
        echo "</ul>";

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    } finally {
        // Close the database connection
        $pdo = null;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Bootstrap CDN  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
        integrity="sha512-b2QcS5SsA8tZodcDtGRELiGv5SaKSk1vDHDaQRda0htPYWZ6046lr3kJ5bAAQdpV2mmA/4v0wQF9MyU6/pDIAg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <h2 class="mb-4">Log In</h2>
            <div class="col-md-6">
                <form action="" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" placeholder="Enter your username" name="username" id="username"
                            class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="text" placeholder="Enter your Password" name="password" id="password"
                            class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Log In</button>
                </form>
                
                <!-- Form for Update -->
                <form action="?action=update&id=<?php echo $userIdToUpdate; ?>" method="post" class="mt-4">
                    <div class="form-group">
                        <label for="new_username">New Username</label>
                        <input type="text" placeholder="Enter new username" name="new_username" id="new_username"
                            class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="text" placeholder="Enter new Password" name="new_password" id="new_password"
                            class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Update</button>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScripts  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.min.js"
        integrity="sha512-WW8/jxkELe2CA1LvQfwm1rajOS8PHasCCx+knHG0gBHt8EXxS6T6tJRTGuDQVnluuAvMxWF4j8SNFDKceLFg=="
        crossorigin="anonymous" referrerpolicy="no"></script>

</body>

</html>
