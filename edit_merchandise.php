<?php
require_once 'dbconnect.php';

if (!isset($_GET['id'])) {
    die("Merchandise ID not provided!");
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM merchandise WHERE id = $id");
$merch = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $tshirt_size = mysqli_real_escape_string($conn, $_POST['tshirt_size']);

    $update = "UPDATE merchandise 
               SET name='$name', email='$email', phone='$phone', address='$address', tshirt_size='$tshirt_size' 
               WHERE id=$id";

    if (mysqli_query($conn, $update)) {
        header("Location: view_merchandise.php?msg=updated");
        exit();
    } else {
        echo "Error updating merchandise: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Merchandise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<div class="container mt-5">
    <h2>Edit Merchandise Registration</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($merch['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($merch['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($merch['phone']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($merch['address']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">T-Shirt Size</label>
            <input type="text" name="tshirt_size" class="form-control" value="<?= htmlspecialchars($merch['tshirt_size']) ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="view_merchandise.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
