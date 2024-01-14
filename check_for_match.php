<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    session_start();
    require_once "./dbconfig.php";

    $response = array("status" => "waiting", "waitingCount" => 0);

    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['id'];

        // Count the number of waiting players
        $sql = "SELECT COUNT(*) as count FROM waiting_players";
        if ($result = $conn->query($sql)) {
            $row = $result->fetch_assoc();
            $waitingCount = $row['count'];
            $response["waitingCount"] = $waitingCount;
        } else {
            die("Error executing query: " . $conn->error);
        }

        // Check if a match is found for the user
        $sql = "SELECT * FROM waiting_players WHERE user_id != ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // A match is found
                $opponent = $result->fetch_assoc();
                $opponent_id = $opponent['user_id'];

                // Create a new match entry in the 'matches' table
                $initialGameState = json_encode(array_fill(0, 9, array_fill(0, 9, null))); // Initial empty game state
                $current_turn = 'X'; // The game starts with player X
                $status = 'in-progress';
                $sql = "INSERT INTO matches (player1_id, player2_id, game_state, current_turn, status) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iisss", $user_id, $opponent_id, $initialGameState, $current_turn, $status);
                $stmt->execute();

                // Set matched flag and timestamp for both players
                $sql = "UPDATE waiting_players SET matched = 1, matched_at = NOW() WHERE user_id IN (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $user_id, $opponent_id);
                $stmt->execute();

                $response["status"] = "match";
            }
        } else {
            die("Error executing prepared statement: " . $stmt->error);
        }
    }

    echo json_encode($response);
?>