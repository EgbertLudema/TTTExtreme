document.addEventListener("DOMContentLoaded", function () {
	let currentPlayer = "X";
	let bigBoardState = Array(9).fill(null);
	let smallBoardsState = Array(9).fill(null).map(() => Array(9).fill(null));
	let nextBoard = null;
	let completedBoards = new Set();

	let player1 = "";  // Will be populated from the server
	let player2 = "";  // Will be populated from the server

	// Fetch the current players from the server when the game starts
	function fetchCurrentPlayers() {
		const xhr = new XMLHttpRequest();
		xhr.open("GET", "get_current_players.php", true);
		xhr.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				const response = JSON.parse(this.responseText);
				if (!response.error) {
					player1 = response.player1;
					player2 = response.player2;

					// Update current player display after fetching names
					updateCurrentPlayerDisplay();
				}
			}
		};
		xhr.send();
	}

	// Call this function when the game starts
	fetchCurrentPlayers();

	// New function to update the current player display
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
		currentPlayerNameElement.innerText = currentPlayer === "X" ? player1 : player2;
	}

	// Function to update scores via AJAX
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
  
	// Initialize the board UI and add event listeners
	const bigBoard = document.getElementById("big-board");
	for (let i = 0; i < 9; i++) {
		const smallBoard = document.createElement("div");
		smallBoard.classList.add("small-board");
		smallBoard.id = `board-${i}`;
		// Add cells and event listeners
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

	function isBoardFull(boardState) {
		return boardState.every(cell => cell !== null);
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
	
		// Create an img element and set its src to the image file
		const imgElement = document.createElement('img');
		imgElement.src = currentPlayer === "X" ? "images/X.png" : "images/O.png";
		imgElement.alt = currentPlayer;
		imgElement.classList.add("player-image");
	
		// Update the UI by appending the img element to the cell
		const cellElement = document.getElementById(`cell-${boardIndex}-${cellIndex}`);
		cellElement.appendChild(imgElement);
	
		// Check for win on the small board
		if (checkWin(smallBoardsState[boardIndex])) {
			bigBoardState[boardIndex] = currentPlayer;
			const smallBoardElement = document.getElementById(`board-${boardIndex}`);
			smallBoardElement.classList.add("completed");
			completedBoards.add(boardIndex);
			
			// Create and append the winner overlay div
			const winnerOverlay = document.createElement('div');
			winnerOverlay.classList.add("winner-overlay");
			
			// Create an img element for the winner and set its src to the image file
			const winnerImage = document.createElement('img');
			winnerImage.src = currentPlayer === "X" ? "images/X.png" : "images/O.png";
			winnerImage.alt = currentPlayer + " wins";
			winnerImage.classList.add("winner-image");
			
			// Append the image to the overlay div
			winnerOverlay.appendChild(winnerImage);
			
			// Append the overlay div to the small board
			smallBoardElement.appendChild(winnerOverlay);
		} else if (isBoardFull(smallBoardsState[boardIndex]) && !checkWin(smallBoardsState[boardIndex])) {
			const smallBoardElement = document.getElementById(`board-${boardIndex}`);
			smallBoardElement.classList.add("draw");
			completedBoards.add(boardIndex);
			
			// Create and append the draw overlay div
			const drawOverlay = document.createElement('div');
			drawOverlay.classList.add("draw-overlay");

			// Create an img element for the draw and set its src to the image file
			const drawImage = document.createElement('img');
			drawImage.src = "images/Draw.png";
			drawImage.classList.add("draw-image");

			// Append the image to the overlay div
			drawOverlay.appendChild(drawImage);

			// Append the overlay div to the small board
			smallBoardElement.appendChild(drawOverlay);
		}
	
		// Check for win on the big board
		if (checkWin(bigBoardState)) {
			alert(`${currentPlayer} wins!`);
			updateScore(currentPlayer === "X" ? player1 : player2, currentPlayer === "O" ? player1 : player2, null, null);
			resetGame(currentPlayer);
			return;
		} 
		// Check for an unavoidable draw on the big board
		else if (checkForUnavoidableDraw()) {
			alert("The game is a draw!");
			updateScore(null, null, player1, player2);
			resetGame();
			return;
		}
	
		// Toggle player and set next board
		currentPlayer = currentPlayer === "X" ? "O" : "X";
		const currentName = currentPlayer === "X" ? player1 : player2;

		// Update the current player display
		updateCurrentPlayerDisplay();

		nextBoard = completedBoards.has(cellIndex) ? null : cellIndex;
	
		// Update selectable boards
		updateSelectableBoards();
	}	
  
	function updateSelectableBoards() {
		// Remove 'selectable' and 'selectable-cell' classes first
		for (let i = 0; i < 9; i++) {
			const smallBoardElement = document.getElementById(`board-${i}`);
			smallBoardElement.classList.remove("selectable");
			for (let j = 0; j < 9; j++) {
				const cellElement = document.getElementById(`cell-${i}-${j}`);
				cellElement.classList.remove("selectable-cell");
			}
		}
	
		// If the next board is specified and not completed, only it is selectable
		if (nextBoard !== null && !completedBoards.has(nextBoard)) {
			const smallBoardElement = document.getElementById(`board-${nextBoard}`);
			smallBoardElement.classList.add("selectable");
			for (let j = 0; j < 9; j++) {
				const cellElement = document.getElementById(`cell-${nextBoard}-${j}`);
				if (smallBoardsState[nextBoard][j] === null) {
					cellElement.classList.add("selectable-cell");
				}
			}
			return;
		}
	
		// If nextBoard is null or completed, all non-completed boards are selectable
		for (let i = 0; i < 9; i++) {
			if (!completedBoards.has(i)) {
				const smallBoardElement = document.getElementById(`board-${i}`);
				smallBoardElement.classList.add("selectable");
				for (let j = 0; j < 9; j++) {
					const cellElement = document.getElementById(`cell-${i}-${j}`);
					if (smallBoardsState[i][j] === null) {
						cellElement.classList.add("selectable-cell");
					}
				}
			}
		}

		if (nextBoard !== null && !completedBoards.has(nextBoard)) {
			const smallBoardElement = document.getElementById(`board-${nextBoard}`);
			smallBoardElement.classList.add("selectable");
			for (let j = 0; j < 9; j++) {
				const cellElement = document.getElementById(`cell-${nextBoard}-${j}`);
				if (smallBoardsState[nextBoard][j] === null) {
					cellElement.classList.add("selectable-cell");
				}
			}
			if (isBoardFull(smallBoardsState[nextBoard])) {
				nextBoard = null;  // Allow to play on any board
			}
			return;
		}
	}

	function checkWin(boardState) {
		const lines = [
			[0, 1, 2],
			[3, 4, 5],
			[6, 7, 8],
			[0, 3, 6],
			[1, 4, 7],
			[2, 5, 8],
			[0, 4, 8],
			[2, 4, 6],
		];
		for (let [a, b, c] of lines) {
			if (
				boardState[a] &&
				boardState[a] === boardState[b] &&
				boardState[a] === boardState[c]
			) {
				return true;
			}
		}
	  	return false;
	}

	// Checking all possibilities of the bigboard for a player to win, if there are no possibilities for a player to win it returns true
	function checkForUnavoidableDraw() {
		const lines = [
			[0, 1, 2],
			[3, 4, 5],
			[6, 7, 8],
			[0, 3, 6],
			[1, 4, 7],
			[2, 5, 8],
			[0, 4, 8],
			[2, 4, 6],
		];
	
		for (let [a, b, c] of lines) {
			if (
				(bigBoardState[a] === null || bigBoardState[a] === "draw") &&
				(bigBoardState[b] === null || bigBoardState[b] === "draw") &&
				(bigBoardState[c] === null || bigBoardState[c] === "draw")
			) {
				// There is still a possible win for someone
				return false;
			}
		}
		return true; // If we reached here, then a draw is inevitable
	}		

	function resetGame(winner = 0) {
		// Update the last starting player based on the game's outcome
		if (winner) {
			lastStartingPlayer = winner;
		} else { // It's a draw
			lastStartingPlayer = lastStartingPlayer === "X" ? "O" : "X";
		}
		
		// Set the current player to the last starting player for the next game
		currentPlayer = lastStartingPlayer;

		// Reset game state variables
		bigBoardState = Array(9).fill(null);
		smallBoardsState = Array(9).fill(null).map(() => Array(9).fill(null));
		nextBoard = null;
		completedBoards.clear();
	
		// Reset the UI
		for (let i = 0; i < 9; i++) {
			const smallBoardElement = document.getElementById(`board-${i}`);
			smallBoardElement.classList.remove("completed", "selectable", "draw", "X", "O");
			
			// Remove any overlays
			const winnerOverlay = smallBoardElement.querySelector(".winner-overlay");
			const drawOverlay = smallBoardElement.querySelector(".draw-overlay");
			if (winnerOverlay) {
				smallBoardElement.removeChild(winnerOverlay);
			}
			if (drawOverlay) {
				smallBoardElement.removeChild(drawOverlay);
			}
	
			for (let j = 0; j < 9; j++) {
				const cellElement = document.getElementById(`cell-${i}-${j}`);
				cellElement.innerHTML = ''; // Remove any children elements (like img)
				cellElement.classList.remove("selectable-cell");
			}
		}
	
		// Update selectable boards
		updateSelectableBoards();

		// Update the current player display after resetting
		updateCurrentPlayerDisplay();
	}	

	// Update selectable boards
	updateSelectableBoards();
});  