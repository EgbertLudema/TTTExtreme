<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user']) && isset($_SESSION['player2'])) {
    echo json_encode(array('player1' => $_SESSION['user'], 'player2' => $_SESSION['player2']));
} else {
    echo json_encode(array('error' => 'No players set'));
}
?>