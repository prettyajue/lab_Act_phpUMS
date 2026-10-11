<?php
include('dbcon.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = null;

// Kukunin yung data ng user
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

if (!$user) {
    die("User not found!");
}

// Mag-aupdate ng data sa db
$message = "";
if (isset($_POST['update_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php=edit");
            exit();
        } else {
            $message = "<p style='color:red;'>Error updating record!</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color:red;'>Please fill out this fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        form { width: 300px; padding: 15px; border: 1px solid #ccc; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; margin: 5px 0 12px; box-sizing: border-box; }
        input[type="submit"] { background: #007bff; color: white; border: none; padding: 10px 15px; cursor: pointer; }
        a { text-decoration: none; color: #555; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>

    <h2>Edit User</h2>
    <?php echo $message; ?>
    <form method="POST" action="edit.php?id=<?php echo $id; ?>">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        
        <input type="submit" name="update_user" value="Update User">
    </form>

    <a href="index.php">&larr; Back to List</a>

</body>
</html>