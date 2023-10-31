<?php
    session_start();
    include('dbconfig.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $player2_username = $_POST['player2_username'];
        $player2_password = $_POST['player2_password'];

        // Fetch hashed password for Player 2 from database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $player2_username);
        $stmt->execute();
        $result = $stmt->get_result();
        $player2 = $result->fetch_assoc();

        // Verify Player 2's password
        if (password_verify($player2_password, $player2['password'])) {
            unset($player2['password']);
            $_SESSION['player2'] = $player2;
            header('Location: index.php');
        } else {
            echo "Invalid credentials for Player 2";
        }
    }
?>