<?php
session_start();
require_once "../dbconfig.php";

$requestId = $_POST['requestId'] ?? 0;

if ($requestId) {
    // Begin transaction
    $conn->begin_transaction();

    // Update the existing friend request to 'accepted'
    $stmt1 = $conn->prepare("UPDATE friends SET status = 'accepted' WHERE id = ?");
    $stmt1->bind_param("i", $requestId);
    $stmt1->execute();

    // Get the details of the request
    $stmt2 = $conn->prepare("SELECT user_id, friend_id FROM friends WHERE id = ?");
    $stmt2->bind_param("i", $requestId);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $request = $result->fetch_assoc();

    // Insert the reciprocal relationship
    $stmt3 = $conn->prepare("INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'accepted')");
    $stmt3->bind_param("ii", $request['friend_id'], $request['user_id']);
    $stmt3->execute();

    if ($stmt1->affected_rows > 0 && $stmt3->affected_rows > 0) {
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Friend request accepted"]);
    } else {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Failed to accept friend request"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
