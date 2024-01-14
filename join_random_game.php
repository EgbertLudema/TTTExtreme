<?php
    session_start();
    require_once "./dbconfig.php";

    $response = array("status" => "failed");

    if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user']['id'];

        // Check if the user is already in the queue
        $sql = "SELECT * FROM waiting_players WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insert the user into the waiting_players table
            $sql = "INSERT INTO waiting_players (user_id) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);

            if ($stmt->execute()) {
                $response["status"] = "joined";
            } else {
                // Handle the error
            }
        } else {
            $response["status"] = "already_in_queue";
        }
    }

    echo json_encode($response);
?>
