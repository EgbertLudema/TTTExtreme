<?php
    session_start();  

    // Redirect to login page if not logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    require_once "./dbconfig.php";
?>
<!DOCTYPE html>
<html>
<?php
    require_once "./components/head.php";
?>
<body class="oldSchool-mode">
    <?php
    require_once "./components/menu.php";
    ?>

    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>

    <!-- Option 1: Login form for Player 2 and Registration Button -->
    <h2>Option 1: Invite a Friend to Play</h2>
    <form method="post" action="start_game.php">
        <input type="text" name="player2_username" placeholder="Player 2 Username" required>
        <input type="password" name="player2_password" placeholder="Player 2 Password" required>
        <input type="submit" value="Start Game">
    </form>
    <button onclick="window.location.href='register.php'">Register New Account</button>

    <!-- Display current players -->
    <div class="current-players">
        <div class="player">
            <p>Player</p><img class="player-img" src="images/X.png"><p>: <?php echo $_SESSION['username']; ?></p>
        </div>
        <div class="player">
            <p>Player <img class="player-img" src="images/O.png">: <?php echo $_SESSION['player2_username']; ?></p>
        </div>
    </div>

    <!-- Logout buttons for Player 1 and Player 2 -->
    <div class="logout-buttons">
        <form action="logout.php" method="post">
            <input type="hidden" name="logout_player" value="1">
            <button type="submit">Logout Player 1</button>
        </form>
        <form action="logout.php" method="post">
            <input type="hidden" name="logout_player" value="2">
            <button type="submit">Logout Player 2</button>
        </form>
    </div>


    <!-- Game board -->
    <div class="container">
        <div id="current-player-info">
            <div class="current-player-text">Current player:</div>
            <div class="half-width-container">
                <div id="current-player-img">
                    <!-- Displays current player image here using JS -->
                </div>
                <div id="current-player-name">
                    <!-- Displays current player name here using JS -->
                </div>
            </div>
        </div>
        <div id="big-board">
            <!-- 9 small boards will be here -->
        </div>
    </div>

    <script src="js/color_modes.js"></script>
    <script src="js/ttt.js"></script>
</body>
</html>
