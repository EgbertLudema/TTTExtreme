<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['username']) && isset($_SESSION['player2_username'])) {
    echo json_encode(array('player1' => $_SESSION['username'], 'player2' => $_SESSION['player2_username']));
} else {
    echo json_encode(array('error' => 'No players set'));
}
?>