<?php
session_start();
require_once "../dbconfig.php";

$userId = $_SESSION['user']["id"];
$searchTerm = $_POST['searchTerm'] ?? '';

$response = [];

if ($searchTerm) {
    $stmt = $conn->prepare("
        SELECT 
            u.id, 
            u.username, 
            CASE 
                WHEN f1.status = 'pending' THEN 'pending'
                WHEN f2.status = 'accepted' THEN 'friend'
                ELSE 'none' 
            END as relationshipStatus
        FROM users u
        LEFT JOIN friends f1 ON u.id = f1.friend_id AND f1.user_id = ? AND f1.status = 'pending'
        LEFT JOIN friends f2 ON u.id = f2.friend_id AND f2.user_id = ? AND f2.status = 'accepted'
        WHERE u.username LIKE CONCAT('%', ?, '%') AND u.id != ?
    ");
    $stmt->bind_param("iisi", $userId, $userId, $searchTerm, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $response = $result->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($response);
?>