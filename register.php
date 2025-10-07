<?php
require_once 'dbconnect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $tshirt_size = mysqli_real_escape_string($conn, trim($_POST['tshirt_size']));
    
    // Server-side validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($phone) || !preg_match('/^[0-9]{10,11}$/', $phone)) {
        $errors[] = "Valid phone number is required";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    if (empty($tshirt_size)) {
        $errors[] = "T-shirt size is required";
    }
    
    // If no errors, insert into database
    if (empty($errors)) {
        $sql = "INSERT INTO merchandise (name, email, phone, address, tshirt_size) 
                VALUES ('$name', '$email', '$phone', '$address', '$tshirt_size')";
        
        if (mysqli_query($conn, $sql)) {
            // Registration successful
            header('Location: register_form.html?success=1');
            exit();
        } else {
            // Database error
            $error_message = "Registration failed. Please try again.";
            if (mysqli_errno($conn) == 1062) {
                $error_message = "This email is already registered.";
            }
            header('Location: register_form.html?error=' . urlencode($error_message));
            exit();
        }
    } else {
        // Validation errors
        $error_message = implode(", ", $errors);
        header('Location: register_form.html?error=' . urlencode($error_message));
        exit();
    }
} else {
    // If not POST request, redirect to form
    header('Location: register_form.html');
    exit();
}

mysqli_close($conn);
?>