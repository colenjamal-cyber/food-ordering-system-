<?php
include 'config.php';

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];

// Simple validation
if (empty($email) || empty($password)) {
    header('Location: login.php?error=empty');
    exit();
}

// Check user in database
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verify password (in real project, use password_verify())
    if ($password == $user['password']) {
        // Store user info in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect based on role
        if ($user['role'] == 1) {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: dashboard.php');
        }
        exit();
    }
}

// If login fails
header('Location: login.php?error=invalid');
exit();
?>