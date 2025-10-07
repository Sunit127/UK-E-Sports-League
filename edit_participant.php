<?php
require_once 'dbconnect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and validate input
    $participant_id = intval($_POST['participant_id']);
    $kills = intval($_POST['kills']);
    $deaths = intval($_POST['deaths']);
    
    // Validate inputs
    if ($participant_id <= 0) {
        header('Location: view_participants_edit_delete.php?error=Invalid participant ID');
        exit();
    }
    
    if ($kills < 0 || $deaths < 0) {
        header('Location: edit_participant_form.php?id=' . $participant_id . '&error=Kills and deaths must be non-negative');
        exit();
    }
    
    // Update participant scores
    $sql = "UPDATE participant SET kills = $kills, deaths = $deaths WHERE id = $participant_id";
    
    if (mysqli_query($conn, $sql)) {
        // Update successful
        header('Location: view_participants_edit_delete.php?success=Participant scores updated successfully');
    } else {
        // Update failed
        header('Location: view_participants_edit_delete.php?error=Failed to update participant scores');
    }
} else {
    // If not POST request, redirect to participants page
    header('Location: view_participants_edit_delete.php');
}

mysqli_close($conn);
exit();
?>


