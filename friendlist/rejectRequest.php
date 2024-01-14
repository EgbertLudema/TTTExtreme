<?php
session_start();
require_once "../dbconfig.php";

$requestId = $_POST['requestId'] ?? 0;

if ($requestId) {
    // Delete the friend request
    $stmt = $conn->prepare("DELETE FROM friends WHERE id = ?");
    $stmt->bind_param("i", $requestId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Friend request rejected"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to reject friend request"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>