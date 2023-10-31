<?php
    header('Content-Type: application/json');

    require_once "dbconfig.php";

    // Use INNER JOIN to get the related username from the users table
    $sql = "SELECT scoreboard.id, scoreboard.wins, scoreboard.losses, scoreboard.draws, users.username 
            FROM scoreboard 
            INNER JOIN users ON scoreboard.user_id = users.id 
            ORDER BY scoreboard.wins DESC";
    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($data, $row);
        }
    }

    echo json_encode($data);

    $conn->close();
?>