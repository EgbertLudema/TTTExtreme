document.addEventListener("DOMContentLoaded", function () {
    fetchScoreboard();
});

function fetchScoreboard() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_scoreboard.php", true);
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(this.responseText);
            populateScoreboard(response);
        }
    };
    xhr.send();
}

let currentData = [];

function populateScoreboard(data) {
    currentData = data;  // Save the current data for sorting later
    const tbody = document.getElementById("scoreboard-body");
    tbody.innerHTML = "";

    data.forEach((row, index) => {
        const tr = document.createElement("tr");

        // Calculate the total games
        const totalGames = parseInt(row.wins) + parseInt(row.losses) + parseInt(row.draws);

        // Calculate the win percentage
        const winPercentRaw = totalGames === 0 ? 0 : ((parseInt(row.wins) / totalGames) * 100);
        const winPercent = winPercentRaw % 1 === 0 ? Math.floor(winPercentRaw) : winPercentRaw.toFixed(2);

        // Create an array for the column data, and append "%" to winPercent
        const columns = [index + 1, row.username, row.wins, row.losses, row.draws, totalGames, winPercent + "%"];  // Changed 'row.player_name' to 'row.username'

        columns.forEach((col) => {
            const td = document.createElement("td");
            td.textContent = col;
            tr.appendChild(td);
        });

        tbody.appendChild(tr);
    });
}

function sortTable(key) {
    let sortedData = [...currentData];

    // Remove active class from all sort buttons
    document.querySelectorAll(".sort-button").forEach((button) => {
        button.classList.remove("active");
    });

    // Add active class to the clicked sort button
    document.getElementById(`sort-${key}`).classList.add("active");

    if (key === 'winPercent') {
        sortedData.sort((a, b) => {
            const totalGamesA = parseInt(a.wins) + parseInt(a.losses);
            const totalGamesB = parseInt(b.wins) + parseInt(b.losses);
            const winPercentA = totalGamesA === 0 ? 0 : ((parseInt(a.wins) / totalGamesA) * 100);
            const winPercentB = totalGamesB === 0 ? 0 : ((parseInt(b.wins) / totalGamesB) * 100);
            return winPercentB - winPercentA;
        });
    } else if (key === 'totalGames') {
        sortedData.sort((a, b) => {
            const totalGamesA = parseInt(a.wins) + parseInt(a.losses) + parseInt(a.draws);
            const totalGamesB = parseInt(b.wins) + parseInt(b.losses) + parseInt(b.draws);
            return totalGamesB - totalGamesA;
        });
    } else {
        sortedData.sort((a, b) => parseInt(b[key]) - parseInt(a[key]));
    }

    populateScoreboard(sortedData);
}