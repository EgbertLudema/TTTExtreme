<?php
    session_start();
    require_once "./dbconfig.php";

    // Remove the player from the waiting_players table
    $sql = "DELETE FROM waiting_players WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();

    json_encode(['status' => 'left']);

    header('Location: index.php');

    $conn->close();
?>
