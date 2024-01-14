<?php
session_start();
require_once "../dbconfig.php";

$requestId = $_POST['requestId'] ?? 0;

if ($requestId) {
    $stmt = $conn->prepare("DELETE FROM friends WHERE id = ?");
    $stmt->bind_param("i", $requestId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Request cancelled"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to cancel request"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>