<?php
session_start();
if ($_POST['logout_player'] == 1) {
    unset($_SESSION['username']);
} elseif ($_POST['logout_player'] == 2) {
    unset($_SESSION['player2_username']);
}
header('Location: index.php');
?>