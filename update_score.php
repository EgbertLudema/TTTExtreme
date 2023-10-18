<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tttextreme";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['winner']) && isset($_POST['loser'])) {
        $winner = $_POST['winner'];
        $loser = $_POST['loser'];
        
        // Update wins for winner
        $sql = "UPDATE Scoreboard SET wins = wins + 1 WHERE player_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $winner);
        $stmt->execute();
        
        // Update losses for loser
        $sql = "UPDATE Scoreboard SET losses = losses + 1 WHERE player_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $loser);
        $stmt->execute();
    } elseif (isset($_POST['draw1']) && isset($_POST['draw2'])) {
        $draw1 = $_POST['draw1'];
        $draw2 = $_POST['draw2'];
        
        // Update draws for both players
        $sql = "UPDATE Scoreboard SET draws = draws + 1 WHERE player_name = ? OR player_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $draw1, $draw2);
        $stmt->execute();
    }
}
?>
