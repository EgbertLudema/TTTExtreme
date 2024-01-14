<?php
session_start();
require_once "../dbconfig.php";

$friendId = $_POST['friendId'] ?? 0;
$userId = $_SESSION['user']['id'];

if ($friendId) {
    // Begin transaction
    $conn->begin_transaction();

    // Delete the friend relationship in both directions
    $stmt1 = $conn->prepare("DELETE FROM friends WHERE user_id = ? AND friend_id = ?");
    $stmt1->bind_param("ii", $userId, $friendId);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM friends WHERE user_id = ? AND friend_id = ?");
    $stmt2->bind_param("ii", $friendId, $userId);
    $stmt2->execute();

    if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Friendship ended"]);
    } else {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Failed to end friendship"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>