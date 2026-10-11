<?php
include('dbcon.php');

$error = "";
$is_edit = false;
$edit_id = "";
$edit_name = "";
$edit_email = "";

if (isset($_POST['update_user'])) {
    $edit_id = intval($_POST['edit_id']);
    $edit_name = trim($_POST['name']);
    $edit_email = trim($_POST['email']);

    if ($edit_id > 0 && $edit_name !== "" && $edit_email !== "") {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $edit_name, $edit_email, $edit_id);

        if ($stmt->execute()) {
            $message = "<p style='color:blue;'>User updated successfully!</p>";
            $is_edit = false;
            $edit_id = "";
            $edit_name = "";
            $edit_email = "";
        } else {
            $message = "<p style='color:red;'>Error updating user!</p>";
            $is_edit = true;
        }
        $stmt->close();
    } else {
        $message = "<p style='color:red;'>Please fill out this fields.</p>";
        $is_edit = true;
    }
}

if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    if ($edit_id > 0) {
        $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user) {
            $is_edit = true;
            $edit_name = $user['name'];
            $edit_email = $user['email'];
        }
    }
}

// magiinsert or magaadd ng bagong user sa db
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        
        if ($stmt->execute()) {
            $message = "<p style='color:blue;'>User added successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Error adding user!</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color:red;'>Please fill out this fields.</p>";
    }
}

// Kukunin ang lahat ng user mula sa db
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        form { margin-bottom: 25px; padding: 15px; border: 1px solid #ffffff; width: 100%; box-sizing: border-box; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; margin: 5px 0 12px; box-sizing: border-box; }
        input[type="submit"] { background: #3b50d8; color: white; border: none; padding: 10px 15px; cursor: pointer; }
        table { border-collapse: collapse; width: 100%; max-width: 700px; }
        th, td { border: 1px solid #ffffff; padding: 10px; text-align: left; }
        th { background-color: #ffffff; }
        .btn-edit { color: blue; text-decoration: none; margin-right: 10px; }
        .btn-delete { color: red; text-decoration: none; }
        .page-title { text-align: center; }
        .dashboard-layout { display: grid; grid-template-columns: minmax(250px, 300px) minmax(0, 1fr); gap: 32px; align-items: start; }
        .dashboard-layout table { max-width: none; }
        @media (max-width: 760px) {
            body { margin: 16px; }
            .dashboard-layout { grid-template-columns: 1fr; }
            .dashboard-layout table { display: block; overflow-x: auto; white-space: nowrap; }
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="page-title">User Management System</h2>
</div>
<main class="dashboard-layout">
    <section>
    <h2><?php echo $is_edit ? 'Edit User' : 'Add New User'; ?></h2>
    <?php if (isset($message)): ?>
        <?php echo $message; ?>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <?php if ($is_edit): ?>
            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
        <?php endif; ?>
        <label>Full Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($edit_name); ?>" required>
        
        <label>Email Address:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($edit_email); ?>" required>
        
        <input type="submit" name="<?php echo $is_edit ? 'update_user' : 'add_user'; ?>" value="<?php echo $is_edit ? 'Update User' : 'Add User'; ?>">
        <?php if ($is_edit): ?>
            <a href="index.php">Cancel</a>
        <?php endif; ?>
    </form>
    </section>

    <section>
    <h2>Registered Users</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <a class="btn-edit" href="index.php?edit=<?php echo $row['id']; ?>">Edit</a>
                            <a class="btn-delete" href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-dark py-4">No data found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </section>
</main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>