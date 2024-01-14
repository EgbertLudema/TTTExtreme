<?php
// Connect to your database
include('dbconfig.php');

$token = $_GET['token'] ?? '';

if ($token) {
    $sql = "UPDATE users SET is_email_verified = 1 WHERE verification_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = "Email successfully verified!";
        echo "<script type='text/javascript'>alert('$message');</script>";
        header('Location: login.php');
    } else {
        $message = "Invalid or expired verification link.";
        echo "<script type='text/javascript'>alert('$message');</script>";
        header('Location: login.php');
    }
} else {
    $message = "No verification token provided.";
    echo "<script type='text/javascript'>alert('$message');</script>";
    header('Location: login.php');
}