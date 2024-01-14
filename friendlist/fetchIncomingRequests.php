<?php
session_start();
require_once "../dbconfig.php";

$userId = $_SESSION['user']['id'];

// Query to fetch incoming friend requests
$query = "SELECT f.id as requestId, u.id as userId, u.username 
          FROM friends f
          JOIN users u ON f.user_id = u.id
          WHERE f.friend_id = ? AND f.status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$incomingRequests = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($incomingRequests);
?>