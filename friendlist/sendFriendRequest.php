<?php
session_start();
require_once "../dbconfig.php";

$userId = $_SESSION['user']["id"];
$friendId = $_POST['friendId'] ?? 0;

if ($friendId && $friendId != $userId) {
    $stmt = $conn->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ii", $userId, $friendId);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Friend request sent"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send friend request. Error: " . $stmt->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>