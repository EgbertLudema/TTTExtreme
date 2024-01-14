<?php
session_start();
require_once "../dbconfig.php"; // Adjust this path to your database configuration file

$userId = $_SESSION['user']['id'];

// Query to fetch pending requests
$query = "SELECT f.id as requestId, u.username FROM friends f
          JOIN users u ON f.friend_id = u.id
          WHERE f.user_id = ? AND f.status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$pendingRequests = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($pendingRequests);
?>