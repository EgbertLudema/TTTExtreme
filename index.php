<?php
    session_start();  

    // Redirect to login page if not logged in
    if (!isset($_SESSION['user'])) {
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
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['user']['username']; if(isset($_SESSION['player2'])){ echo ' and '.$_SESSION['player2']['username'];} ?>!</h1>

        <!-- Option 1: Login form for Player 2 and Registration Button -->
        <?php
        if(!isset($_SESSION['player2'])){
        ?>
            <div class="add_player">
                <div class="open">
                    <h2>Add second local player to play a game</h2>
                </div>
                <div class="opening_content" style="display: none;">
                    <form class="form" method="post" action="local_player_2.php">
                        <input type="text" name="player2_username" placeholder="Player 2 Username" autocomplete="off" required>
                        <input type="password" name="player2_password" placeholder="Player 2 Password" required>
                        <button type="submit">Start game</button>
                        <p class="message">Not registered? <a href="register.php">Create an account</a></p>
                    </form>
                </div>
            </div>
        <?php
        }
        ?>

        <!-- Logout buttons for Player 1 and Player 2 -->
        <div class="logout-buttons">
            <form action="logout.php" method="post">
                <input type="hidden" name="logout_player" value="1">
                <button type="submit">Logout Player 1</button>
            </form>
            <?php
            if(isset($_SESSION['player2'])){
            ?>
                <form action="logout.php" method="post">
                    <input type="hidden" name="logout_player" value="2">
                    <button type="submit">Logout Player 2</button>
                </form>
            <?php
            }
            ?>
        </div>
    </div>
    <!-- Display current players -->
    <div class="current-players">
        <div class="player">
            <p>Player</p><img class="player-img" src="images/X.png"><p>: <?php echo $_SESSION['user']["username"]; ?></p>
        </div>
        <?php
        if(isset($_SESSION['player2'])){
        ?>
            <div class="player">
                <p>Player <img class="player-img" src="images/O.png">: <?php echo $_SESSION['player2']["username"]; ?></p>
            </div>
        <?php
        }
        ?>
    </div>

    <?php
    if (isset($_SESSION['user']) && isset($_SESSION['player2'])) { 
    ?>
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
    <?php
    }
    ?>
    <script src="js/color_modes.js"></script>
    <script src="js/ttt.js"></script>
    <script>
    $(function(){
        $('.open').click(function(){
            // Find the closest parent with class "add_player"
            // Then find the child with class "opening_content"
            var divToOpen = $(this).closest('.add_player').find('.opening_content');

            // Toggle the visibility of the div
            divToOpen.slideToggle();
        });
    });
    </script>
</body>
</html>
