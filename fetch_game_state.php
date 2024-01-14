<?php
    session_start();
    require_once "./dbconfig.php";

    $gameId = $_GET['gameId'];

    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['id'];
        
        $sql = "SELECT game_state FROM matches WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $gameId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $gameState = $row['game_state'];
            echo $gameState;
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to fetch game state"]);
        }
    }
?>