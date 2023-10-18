<?php
session_start();  

function fetch_players($conn) {
    $sql = "SELECT player_name FROM Scoreboard ORDER BY player_name ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<option value=\"" . $row['player_name'] . "\">" . $row['player_name'] . "</option>";
        }
    }
}
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
        if (isset($_POST['change_players'])) {
            unset($_SESSION['player1']);
            unset($_SESSION['player2']);
        } else {
            // Handle Player 1
            $player1 = !empty($_POST['existing_player1']) ? $_POST['existing_player1'] : $_POST['new_player1'];
            if (!empty($_POST['new_player1'])) {
                $sql = "SELECT * FROM Scoreboard WHERE player_name = '$player1'";
                $result = $conn->query($sql);

                if ($result->num_rows == 0) {
                    $sql = "INSERT INTO Scoreboard (player_name, wins, losses, draws) VALUES ('$player1', 0, 0, 0)";
                    $conn->query($sql);
                }
            }

            // Handle Player 2
            $player2 = !empty($_POST['existing_player2']) ? $_POST['existing_player2'] : $_POST['new_player2'];
            if (!empty($_POST['new_player2'])) {
                $sql = "SELECT * FROM Scoreboard WHERE player_name = '$player2'";
                $result = $conn->query($sql);

                if ($result->num_rows == 0) {
                    $sql = "INSERT INTO Scoreboard (player_name, wins, losses, draws) VALUES ('$player2', 0, 0, 0)";
                    $conn->query($sql);
                }
            }

            $_SESSION['player1'] = $player1;
            $_SESSION['player2'] = $player2;
        }
    }
    ?>

    <?php if (!isset($_SESSION['player1']) || !isset($_SESSION['player2'])){ ?>
        <!-- Player selection form -->
        <div class="container">
            <form class="select_players_form" action="" method="post">
                <!-- Dropdown for Player 1 -->
                <label for="existing_player1">Select Existing Player 1:</label>
                <select name="existing_player1" id="existing_player1">
                    <option value="">Select a player...</option>
                    <?php fetch_players($conn); ?>
                </select>
                <!-- Or create new -->
                <label for="new_player1">Or Create New Player 1:</label>
                <input type="text" name="new_player1" id="new_player1">
                
                <!-- Dropdown for Player 2 -->
                <label for="existing_player2">Select Existing Player 2:</label>
                <select name="existing_player2" id="existing_player2">
                    <option value="">Select a player...</option>
                    <?php fetch_players($conn); ?>
                </select>
                <!-- Or create new -->
                <label for="new_player2">Or Create New Player 2:</label>
                <input type="text" name="new_player2" id="new_player2">

                <button type="submit">Start Game</button>
            </form>
        </div>
    <?php }else{ ?>
        <!-- Display current players -->
        <div class="current-players">
            <div class="player">
                <p>Player</p><img class="player-img" src="images/X.png"><p>: <?php echo $_SESSION['player1']; ?></p>
            </div>
            <div class="player">
                <p>Player <img class="player-img" src="images/O.png">: <?php echo $_SESSION['player2']; ?></p>
            </div>
        </div>

        <!-- Button to change players -->
        <div class="change-players">
            <form action="" method="post">
                <input type="hidden" name="change_players" value="true">
                <button type="submit"><img class="change_players_img" src="images/change_players.png"></button>
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

        <script src="js/ttt.js"></script>
        <?php } ?>
    <script src="js/color_modes.js"></script>
</body>
</html>
