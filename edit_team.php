<?php
require_once 'dbconnect.php';

if (!isset($_GET['id'])) {
    die("Team ID not provided!");
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM team WHERE id = $id");
$team = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);

    $update = "UPDATE team SET name='$name', city='$city' WHERE id=$id";
    if (mysqli_query($conn, $update)) {
        header("Location: view_teams.php?msg=updated");
        exit();
    } else {
        echo "Error updating team: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Team</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<div class="container mt-5">
    <h2>Edit Team</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Team Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($team['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($team['city']) ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="view_teams.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
