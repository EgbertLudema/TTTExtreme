document.addEventListener("DOMContentLoaded", function () {
	let currentPlayer = "X";
	let lastStartingPlayer = "X";
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
		currentPlayerNameElement.innerText = currentPlayer === "X" ? player1.username : player2.username;
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
			updateScore(currentPlayer === "X" ? player1.id : player2.id, currentPlayer === "O" ? player1.id : player2.id, null, null);
			showOverlay(`${currentPlayer === "X" ? player1.username : player2.username} wins!`);
			startConfetti();
			return;
		}

		// Check for a draw only when all boards are completed without a winner
		if (completedBoards.size === 9 && !checkWin(bigBoardState)) {
			updateScore(null, null, player1.id, player2.id);
			showOverlay("The game is a draw!");
			return;
		}
	
		// Toggle player and set next board
		currentPlayer = currentPlayer === "X" ? "O" : "X";

		// Set the next board to the index of the cell that was just played
		nextBoard = cellIndex;

		// If the next board is completed, set nextBoard to null to allow choosing any non-completed board
		if (completedBoards.has(nextBoard)) {
			nextBoard = null;
		}

		// Update the current player display
		updateCurrentPlayerDisplay();

		// Update selectable boards
		updateSelectableBoards();
	}
  
	function updateSelectableBoards() {
		// First, remove 'selectable' and 'selectable-cell' classes
		for (let i = 0; i < 9; i++) {
			const smallBoardElement = document.getElementById(`board-${i}`);
			smallBoardElement.classList.remove("selectable");
			for (let j = 0; j < 9; j++) {
				const cellElement = document.getElementById(`cell-${i}-${j}`);
				cellElement.classList.remove("selectable-cell");
			}
		}
	
		// If the next board is not null or completed, only that board is selectable
		if (nextBoard !== null && !completedBoards.has(nextBoard)) {
			const smallBoardElement = document.getElementById(`board-${nextBoard}`);
			smallBoardElement.classList.add("selectable");
			for (let j = 0; j < 9; j++) {
				const cellElement = document.getElementById(`cell-${nextBoard}-${j}`);
				if (smallBoardsState[nextBoard][j] === null) {
					cellElement.classList.add("selectable-cell");
				}
			}
		}
		// If the next board is null or completed, all non-completed boards are selectable
		else {
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

	function resetGame() {
		// Alternate the starting player for the next game
		lastStartingPlayer = lastStartingPlayer === "X" ? "O" : "X";
	
		// Set the current player to the new starting player
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
	
	// When a game is finished the overlay shows
	function showOverlay(message) {
		const overlay = document.getElementById('game-overlay');
		const resultMessageElement = document.getElementById('game-result-message');
	
		// Clear previous content
		resultMessageElement.innerHTML = '';
	
		// Create an img element for displaying the symbol or draw image
		const imgElement = document.createElement('img');
		imgElement.classList.add('game-result-image');
	
		// Check if the game is a draw or a win
		if (message === "The game is a draw!") {
			// Set the src for the draw image
			imgElement.src = 'images/Draw.png'; // Update the path as needed
			imgElement.alt = 'Draw';
		} else if (message.includes('wins')) {
			// Set the src for the player's symbol image
			imgElement.src = currentPlayer === 'X' ? 'images/X.png' : 'images/O.png'; // Update the path as needed
			imgElement.alt = currentPlayer;
	
			const winningPlayerName = currentPlayer === 'X' ? player1.username : player2.username;
			message = message.replace(currentPlayer, winningPlayerName);
		}
	
		// Append the image to the result message element
		resultMessageElement.appendChild(imgElement);
	
		// Create a text node for the message and append it
		const textNode = document.createTextNode(message);
		resultMessageElement.appendChild(textNode);
	
		overlay.style.display = 'flex';
	}	

	// By clicking the close butten in the overlay the overlay closes
	document.getElementById('close-overlay-btn').addEventListener('click', hideOverlay);
	function hideOverlay() {
		// Reset the game based on the last game result
		resetGame();
		document.getElementById('game-overlay').style.display = 'none';
		stopConfetti();
	}

	// Update selectable boards
	updateSelectableBoards();
});  