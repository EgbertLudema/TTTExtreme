document.addEventListener("DOMContentLoaded", function () {
    let currentPlayer = "X";
    let bigBoardState = Array(9).fill(null);
    let smallBoardsState = Array(9).fill(null).map(() => Array(9).fill(null));
    let nextBoard = null;
    let completedBoards = new Set();

    const gameId = window.gameId;  // This should be set from your PHP script

    // Initialize the board UI and add event listeners
    const bigBoard = document.getElementById("big-board");
    for (let i = 0; i < 9; i++) {
        const smallBoard = document.createElement("div");
        smallBoard.classList.add("small-board");
        smallBoard.id = `board-${i}`;
        for (let j = 0; j < 9; j++) {
            const cell = document.createElement("div");
            cell.classList.add("cell");
            cell.id = `cell-${i}-${j}`;
            cell.addEventListener("click", function () {
                makeMove(i, j);
            });
            smallBoard.appendChild(cell);
        }
        bigBoard.appendChild(smallBoard);
    }

    // Function to save game state to the server
    function saveGameState() {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "save_game_state.php", true);
        xhr.setRequestHeader("Content-type", "application/json");
        xhr.send(JSON.stringify({
            gameId: gameId,
            bigBoardState: bigBoardState,
            smallBoardsState: smallBoardsState,
            currentPlayer: currentPlayer,
            nextBoard: nextBoard,
            completedBoards: Array.from(completedBoards)
        }));
    }

    function updateCurrentPlayerDisplay() {
        const currentPlayerElement = document.getElementById("current-player-img");
        const currentPlayerNameElement = document.getElementById("current-player-name");

        // Clear existing content
        currentPlayerElement.innerHTML = '';
        currentPlayerNameElement.innerHTML = '';

        // Create new image element for current player
        const imgElement = document.createElement('img');
        imgElement.src = `images/${currentPlayer}.png`;
        imgElement.alt = currentPlayer;
        imgElement.classList.add("player-image");

        // Append image to the current player display
        currentPlayerElement.appendChild(imgElement);

        // Update current player name
        currentPlayerNameElement.innerText = currentPlayer === "X" ? player1.username : player2.username;
    }

    function updateScore(winner, loser, draw1, draw2) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_score.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        if (winner && loser) {
            xhr.send(`winner=${winner}&loser=${loser}`);
        } else if (draw1 && draw2) {
            xhr.send(`draw1=${draw1}&draw2=${draw2}`);
        }
    }

    function checkForUnavoidableDraw() {
        // ... (Your existing function here)
    }

    // Function to fetch game state from the server
    function fetchGameState() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `fetch_game_state.php?gameId=${gameId}`, true);
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const gameState = JSON.parse(this.responseText);
                bigBoardState = gameState.bigBoardState;
                smallBoardsState = gameState.smallBoardsState;
                currentPlayer = gameState.currentPlayer;
                nextBoard = gameState.nextBoard;
                completedBoards = new Set(gameState.completedBoards);

                // Update the UI based on the fetched game state
                // Loop through the big board and small boards to update cells
                for (let boardIndex = 0; boardIndex < 9; boardIndex++) {
                    const smallBoard = smallBoardsState[boardIndex];
                    for (let cellIndex = 0; cellIndex < 9; cellIndex++) {
                        const cellValue = smallBoard[cellIndex];
                        if (cellValue) {
                            const imgElement = document.createElement('img');
                            imgElement.src = cellValue === "X" ? "images/X.png" : "images/O.png";
                            imgElement.alt = cellValue;
                            imgElement.classList.add("player-image");
                            
                            const cellElement = document.getElementById(`cell-${boardIndex}-${cellIndex}`);
                            cellElement.innerHTML = '';
                            cellElement.appendChild(imgElement);
                        }
                    }

                    // Update the UI for completed boards
                    if (bigBoardState[boardIndex]) {
                        const smallBoardElement = document.getElementById(`board-${boardIndex}`);
                        smallBoardElement.classList.add(bigBoardState[boardIndex] === 'X' || bigBoardState[boardIndex] === 'O' ? "completed" : "draw");
                    }
                }

                // Update selectable boards
                updateSelectableBoards();
            }
        };
        xhr.send();
    }

    function makeMove(boardIndex, cellIndex) {
        // Validate move
        if (smallBoardsState[boardIndex][cellIndex] !== null || 
            completedBoards.has(boardIndex) ||
            (nextBoard !== null && nextBoard !== boardIndex && !completedBoards.has(nextBoard))) {
            return;
        }
    
        // Update the board state
        smallBoardsState[boardIndex][cellIndex] = currentPlayer;
    
        // Update the UI by appending the img element to the cell
        const imgElement = document.createElement('img');
        imgElement.src = currentPlayer === "X" ? "images/X.png" : "images/O.png";
        imgElement.alt = currentPlayer;
        imgElement.classList.add("player-image");
    
        const cellElement = document.getElementById(`cell-${boardIndex}-${cellIndex}`);
        cellElement.appendChild(imgElement);
    
        // Check for win on the small board
        if (checkWin(smallBoardsState[boardIndex])) {
            bigBoardState[boardIndex] = currentPlayer;
            const smallBoardElement = document.getElementById(`board-${boardIndex}`);
            smallBoardElement.classList.add("completed");
            completedBoards.add(boardIndex);
        } 
        // Check for draw on the small board
        else if (isBoardFull(smallBoardsState[boardIndex]) && !checkWin(smallBoardsState[boardIndex])) {
            bigBoardState[boardIndex] = "draw";
            const smallBoardElement = document.getElementById(`board-${boardIndex}`);
            smallBoardElement.classList.add("draw");
            completedBoards.add(boardIndex);
        }
    
        // Check for win on the big board
        if (checkWin(bigBoardState)) {
            alert(`${currentPlayer} wins!`);
            // Your logic to update the database that the game is won
            resetGame();
            return;
        }
        // Check for draw on the big board
        else if (isBoardFull(bigBoardState) && !checkWin(bigBoardState)) {
            alert("The game is a draw!");
            // Your logic to update the database that the game is a draw
            resetGame();
            return;
        }
    
        // Toggle player and set next board
        currentPlayer = currentPlayer === "X" ? "O" : "X";
        nextBoard = completedBoards.has(cellIndex) ? null : cellIndex;
    
        // Update selectable boards
        updateSelectableBoards();
    
        // Save game state after making a move
        saveGameState();
    } 

    // Periodically fetch the game state to keep both players in sync
    setInterval(fetchGameState, 2000);
});
