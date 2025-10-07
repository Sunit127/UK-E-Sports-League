<?php
require_once 'dbconnect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}

if (isset($_GET['id'], $_GET['field'], $_GET['action'])) {
    $id = intval($_GET['id']);
    $field = $_GET['field'];   // kills or deaths
    $action = $_GET['action']; // inc or dec

    if (!in_array($field, ['kills', 'deaths'])) {
        die("Invalid field");
    }

    if ($action === 'inc') {
        $sql = "UPDATE participant SET $field = $field + 1 WHERE id = $id";
    } elseif ($action === 'dec') {
        $sql = "UPDATE participant SET $field = GREATEST($field - 1, 0) WHERE id = $id";
    } else {
        die("Invalid action");
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: view_participants_edit_delete.php?success=Updated");
        exit();
    } else {
        die("Error updating record: " . mysqli_error($conn));
    }
} else {
    header("Location: view_participants_edit_delete.php");
    exit();
}
