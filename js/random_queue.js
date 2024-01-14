document.addEventListener("DOMContentLoaded", function () {
    // Code for online random player
    let waitingOverlay = document.getElementById("waiting-overlay");
    let waitingTimer = document.getElementById("waiting-timer");
    let waitingCount = document.getElementById("waiting-count");
    let leaveQueueBtn = document.getElementById("leave-queue");

    // Overlay and AJAX Logic (New Code)
    let startTime = Date.now();

    // Update timer every second
    setInterval(() => {
        let elapsedTime = Math.floor((Date.now() - startTime) / 1000);
        waitingTimer.textContent = `Waiting Time: ${elapsedTime} seconds`;
    }, 1000);

    // Check for a match every 5 seconds
    setInterval(() => {
        fetch("check_for_match.php")
        .then(response => response.json())
        .then(data => {
            if (data.status === "match") {
                // Start the game
                player1 = data.player1;
                player2 = data.player2;
                // Call your existing functions to start the game
                fetchCurrentPlayers();
                resetGame();
                waitingOverlay.style.display = "none"; // Hide the overlay
            } else {
                // Update waiting count
                waitingCount.textContent = `Players Waiting: ${data.waitingCount}`;
            }
        });
    }, 5000);

    // Leave queue button
    leaveQueueBtn.addEventListener("click", () => {
        fetch("leave_queue.php")
        .then(response => response.json())
        .then(data => {
            if (data.status === "left") {
                waitingOverlay.style.display = "none";
            }
        });
    });
});  