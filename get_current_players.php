<?php
    session_start();
    header('Content-Type: application/json');
    require_once "./dbconfig.php";

    $response = array("error" => true);

    // Check for local game
    if (isset($_SESSION['user']) && isset($_SESSION['player2'])) {
        $response["player1"] = $_SESSION['user'];
        $response["player2"] = $_SESSION['player2'];
        $response["error"] = false;
    }
    // Check for online game
    elseif (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['id'];
        $sql = "SELECT player1_id, player2_id FROM matches WHERE player1_id = ? OR player2_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $player1_id = $row['player1_id'];
            $player2_id = $row['player2_id'];

            // Fetch usernames based on player ids
            $player1 = getPlayerUsername($player1_id, $conn);
            $player2 = getPlayerUsername($player2_id, $conn);

            $response["player1"] = array("username" => $player1);
            $response["player2"] = array("username" => $player2);
            $response["error"] = false;
        }
    }

    echo json_encode($response);

    function getPlayerUsername($player_id, $conn) {
        $sql = "SELECT username FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $player_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['username'];
    }
?>
