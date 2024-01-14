<?php
    session_start();
    require_once "./dbconfig.php";

    $input = json_decode(file_get_contents('php://input'), true);
    $gameId = $input['gameId'];
    $gameState = json_encode($input);

    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['id'];
        
        $sql = "UPDATE matches SET game_state = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $gameState, $gameId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update game state"]);
        }
    }
?>
