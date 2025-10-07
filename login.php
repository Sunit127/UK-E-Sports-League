<?php
require_once 'dbconnect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    
    // Validate input
    if (empty($username) || empty($password)) {
        header('Location: admin_login.html?error=1');
        exit();
    }
    
    // Query database for user
    $sql = "SELECT * FROM user WHERE username = '$username' AND password = '$password' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        // Login successful
        $user = mysqli_fetch_assoc($result);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_id'] = $user['id'];
        
        // Redirect to admin menu
        header('Location: admin_menu.php');
        exit();
    } else {
        // Login failed
        header('Location: admin_login.html?error=1');
        exit();
    }
} else {
    // If not POST request, redirect to login page
    header('Location: admin_login.html');
    exit();
}

mysqli_close($conn);
?>