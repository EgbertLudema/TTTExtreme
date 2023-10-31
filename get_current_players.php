<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['player1']) && isset($_SESSION['player2'])) {
    echo json_encode(array('player1' => $_SESSION['player1'], 'player2' => $_SESSION['player2']));
} else {
    echo json_encode(array('error' => 'No players set'));
}
?>