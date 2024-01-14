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

        <?php
        if(!isset($_SESSION['player2'])){
        ?>
            <!-- Option 1: Login form for Player 2 and Registration Button -->
            <div class="add_player">
                <div id="add-local-overlay">
                    <h2>Add second local player to play a game</h2>
                    <svg id="add-local-arrow" class="arrow" xmlns="http://www.w3.org/2000/svg" viewBox="3 3 18 18" align="center" width="30" height="30" fill-rule="evenodd" clip-rule="evenodd"><path d="M16 15a1 1 0 0 1-.707-.293L12 11.414l-3.293 3.293a1 1 0 1 1-1.414-1.414l4-4a1 1 0 0 1 1.414 0l4 4A1 1 0 0 1 16 15z" style="fill:#ff8e31;"></path></svg>
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
            <!-- Option 2: Play against a random player -->
            <div class="join-online-queue">
                <button id="joinQueueBtn">Join Random Online Queue</button>
                <div id="queueStatus">Status: Not in queue</div>
                <div id="leave_queue" style="display: none;"><a href="leave_queue.php">Leave queue</a></div>
            </div>
            <!-- Option 3: Play against a friend -->
            <!-- Add code yes -->
        <?php
        }
        ?>

        <!-- Logout buttons for Player 1 and Player 2 -->
        <div class="logout-buttons">
            <form action="logout.php" method="post">
                <input type="hidden" name="logout_player" value="1">
                <button type="submit">Logout <?php echo $_SESSION['user']["username"]; ?></button>
            </form>
            <?php
            if(isset($_SESSION['player2'])){
            ?>
                <form action="logout.php" method="post">
                    <input type="hidden" name="logout_player" value="2">
                    <button type="submit">Logout <?php echo $_SESSION['player2']["username"]; ?></button>
                </form>
            <?php
            }
            ?>
        </div>
    </div>

    <?php
    if (isset($_SESSION['user']) && isset($_SESSION['player2'])) { 
    ?>
        <!-- Overlay for game result -->
        <div id="game-overlay" style="display: none;">
            <div class="game-overlay-container">
                <h1>TTT Extreme!!!</h1>
                <div id="game-result-message"></div>
                <button id="close-overlay-btn" class="btn">Close</button>
            </div>
        </div>
        <!-- Game board -->
        <div class="container">
            <!-- Display current players -->
            <div class="current-players">
                <div class="player">
                    <p><?php echo $_SESSION['user']["username"]; ?> : </p><img class="player-img" src="images/X.png">
                </div>
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
                <div class="player">
                    <p><?php echo $_SESSION['player2']["username"]; ?> : </p><img class="player-img" src="images/O.png">
                </div>
            </div>
            <div id="big-board">
                <!-- 9 small boards will be here -->
            </div>
        </div>
    <?php
    }
    require_once "./friendlist/friendlist.php";
    ?>
    <!-- <iframe src="https://discord.com/widget?id=1187482578274488391&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe> -->
    <!-- <a href="https://discord.gg/9QdzJqwW6A"><img src="https://discord.com/api/guilds/1187482578274488391/widget.png?style=banner2" alt=""></a> -->
    <script src="js/color_modes.js"></script>
    <script src="js/ttt.js"></script>
    <script src="js/confetti.js"></script>
    <script>
    $(function(){
        $('#add-local-overlay').click(function(){
            // Find the closest parent with class "add_player"
            // Then find the child with class "opening_content"
            var divToOpen = $(this).closest('.add_player').find('.opening_content');

            // Toggle the visibility of the div
            divToOpen.slideToggle();

            // Toggle arrow immediately on click
            var arrow = document.getElementById('add-local-arrow');
            arrow.classList.toggle('rotated'); // Toggle the 'rotated' class on click
        });
    });

    // Handle the click event for the "Join Random Online Queue" button
    $('#joinQueueBtn').click(function() {
        $(this).prop("disabled", true);
        joinQueue();
    });

    // Function to join the online random queue
    function joinQueue() {
        $.ajax({
            url: 'join_random_game.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'joined') {
                    $('#queueStatus').text('Status: Waiting for a match...');
                    checkForMatch();                    
                    var leave_queue = document.getElementById("leave_queue");
                    leave_queue.style.display = "block";
                } else if (response.status === 'already_in_queue') {
                    alert('You are already in the queue.');
                    checkForMatch();
                    var leave_queue = document.getElementById("leave_queue");
                    leave_queue.style.display = "block";
                    $('#queueStatus').text('Status: Waiting for a match...');
                } else {
                    $('#joinQueueBtn').prop("disabled", false);
                    alert('An error occurred.');
                }
            }
        });
    }

    // Function to check for a match
    function checkForMatch() {
        $.ajax({
            url: 'check_for_match.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.status === 'match') {
                    // Start countdown
                    startCountdown(5);
                } else {
                    setTimeout(checkForMatch, 2000);  // Check again in 2 seconds
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error:", textStatus, errorThrown);
            }
        });
    }

    // Function to start a countdown
    function startCountdown(seconds) {
        let countdown = setInterval(function() {
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = "game.php";  // Redirect to the game screen
            } else {
                $('#queueStatus').text(`Game starting in ${seconds} seconds...`);
            }
            seconds--;
        }, 1000);
    }
    </script>
</body>
</html>
