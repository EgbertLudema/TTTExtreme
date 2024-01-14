<?php
session_start();
require_once "./dbconfig.php";

function handleError($conn) {
    echo "An error occurred: " . $conn->error;
    exit;
}

function executeUpdate($conn, $sql, $types, ...$params) {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) handleError($conn);

    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) handleError($conn);
}

function executeUpdateOrInsert($conn, $userId, $column) {
    // Check if user exists in the scoreboard
    $checkSql = "SELECT * FROM scoreboard WHERE user_id = ?";
    $stmt = $conn->prepare($checkSql);
    if ($stmt === false) handleError($conn);

    $stmt->bind_param("s", $userId);
    if (!$stmt->execute()) handleError($conn);

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        // User not found, insert new record
        $insertSql = "INSERT INTO scoreboard (user_id, wins, losses, draws) VALUES (?, ?, ?, ?)";
        $wins = $column == 'wins' ? 1 : 0;
        $losses = $column == 'losses' ? 1 : 0;
        $draws = $column == 'draws' ? 1 : 0;
        executeUpdate($conn, $insertSql, "siii", $userId, $wins, $losses, $draws);
    } else {
        // User found, update record
        $updateSql = "UPDATE scoreboard SET $column = $column + 1 WHERE user_id = ?";
        executeUpdate($conn, $updateSql, "s", $userId);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['winner']) && isset($_POST['loser'])) {
        $winner = $_POST['winner'];
        $loser = $_POST['loser'];

        // Update or insert for winner
        executeUpdateOrInsert($conn, $winner, 'wins');

        // Update or insert for loser
        executeUpdateOrInsert($conn, $loser, 'losses');

    } elseif (isset($_POST['draw1']) && isset($_POST['draw2'])) {
        $draw1 = $_POST['draw1'];
        $draw2 = $_POST['draw2'];

        // Update or insert for both players
        executeUpdateOrInsert($conn, $draw1, 'draws');
        executeUpdateOrInsert($conn, $draw2, 'draws');
    }
}
?>