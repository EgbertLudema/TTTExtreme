<?php
session_start();
if ($_POST['logout_player'] == 1) {
    unset($_SESSION['user']);
    unset($_SESSION['player2']);
} elseif ($_POST['logout_player'] == 2) {
    unset($_SESSION['player2']);
}
header('Location: index.php');
?>