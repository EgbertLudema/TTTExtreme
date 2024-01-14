<?php
    session_start();  
    require_once "./dbconfig.php";

    // Redirect to login page if not logged in
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }

    // Fetch the game_id for the current user
    $user = $_SESSION['user'];
    $user_id = $_SESSION['user']['id'];
    $sql = "SELECT id FROM matches WHERE (player1_id = ? OR player2_id = ?) AND status = 'in-progress' ORDER BY updated_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $game_id = $row['id'] ?? null;

    if (!$game_id) {
        // Redirect to some page or show an error that no ongoing game was found
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php
    require_once "./components/head.php";
?>
</head>
<body class="oldSchool-mode">
    <?php
    require_once "./components/menu.php";
    ?>
    <div id="waiting-overlay">
        <div id="waiting-timer"></div>
        <div id="waiting-count"></div>
        <button id="leave-queue">Leave Queue</button>
    </div>

    <!-- Display current players -->
    <div class="current-players">
        <div class="player">
            <p>Player</p><img class="player-img" src="images/X.png"><p>: <?php echo $user['username']; ?></p>
        </div>
        <?php
        if(isset($opponent)){
        ?>
            <div class="player">
                <p>Player <img class="player-img" src="images/O.png">: <?php echo $opponent['username']; ?></p>
            </div>
        <?php
        }
        ?>
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

    <script src="js/online_ttt.js"></script>
    <script>
        const gameId = <?php echo json_encode($game_id); ?>;
    </script>
</body>
</html>
