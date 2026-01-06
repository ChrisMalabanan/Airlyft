<?php
session_start();
require_once("config.php");

// Redirect if admin is not logged in
if (!isset($_SESSION['adminlogin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$id = intval($_GET['id']);

// Fetch user data
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phonenumber = trim($_POST['phonenumber']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = ?, email = ?, phonenumber = ?, password = ? WHERE id = ?";
        $params = [$username, $email, $phonenumber, $password, $id];
    } else {
        $sql = "UPDATE users SET username = ?, email = ?, phonenumber = ? WHERE id = ?";
        $params = [$username, $email, $phonenumber, $id];
    }

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        header("Location: admin_users.php"); // Redirect back to user list
        exit;
    } catch (PDOException $e) {
        echo "Update failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4>Edit User</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phonenumber" class="form-control" value="<?= htmlspecialchars($user['phonenumber']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="New Password (optional)">
                    </div>
                    <button type="submit" class="btn btn-success">Update User</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
