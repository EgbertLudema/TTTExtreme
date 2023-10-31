<?php
    header('Content-Type: application/json');

    require_once "dbconfig.php";

    $sql = "SELECT player_name, wins, losses, draws FROM scoreboard ORDER BY wins DESC";
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