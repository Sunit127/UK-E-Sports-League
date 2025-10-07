<?php
require_once 'dbconnect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}

// Handle delete requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    // Determine type
    $type = isset($_POST['type']) ? $_POST['type'] : (isset($_GET['type']) ? $_GET['type'] : '');
    $id   = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

    if ($id <= 0 || $type === '') {
        header('Location: admin_menu.php?error=Invalid request');
        exit();
    }

    switch ($type) {
        case 'participant':
            $check_sql = "SELECT id FROM participant WHERE id = $id";
            $delete_sql = "DELETE FROM participant WHERE id = $id";
            $redirect = "view_participants_edit_delete.php";
            break;

        case 'team':
            $check_sql = "SELECT id FROM team WHERE id = $id";
            $delete_sql = "DELETE FROM team WHERE id = $id";
            $redirect = "view_teams.php";
            break;

        case 'merchandise':
            $check_sql = "SELECT id FROM merchandise WHERE id = $id";
            $delete_sql = "DELETE FROM merchandise WHERE id = $id";
            $redirect = "view_merchandise.php";
            break;

        default:
            header('Location: admin_menu.php?error=Invalid delete type');
            exit();
    }

    // Validate existence
    $check_result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_result) == 0) {
        header("Location: $redirect?error=Record not found");
        exit();
    }

    // Perform delete
    if (mysqli_query($conn, $delete_sql)) {
        header("Location: $redirect?success=Record deleted successfully");
    } else {
        header("Location: $redirect?error=Failed to delete record");
    }
} else {
    header('Location: admin_menu.php');
}

mysqli_close($conn);
exit();
?>
