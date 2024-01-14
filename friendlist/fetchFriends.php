<?php
session_start();
require_once "../dbconfig.php";

$userId = $_SESSION['user']['id'];

// Query to fetch friends
$query = "SELECT f.id as friendshipId, f.friend_id as friendId, u.username 
          FROM friends f
          JOIN users u ON f.friend_id = u.id
          WHERE f.user_id = ? AND f.status = 'accepted'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$friends = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($friends);
?>