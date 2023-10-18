<!DOCTYPE html>
<html>
<?php
    require_once "./components/head.php";
?>
<body>
    <?php
    require_once "./components/menu.php";
    ?>
    <div class="container">
        <div class="sort-buttons">
            <button id="sort-wins" class="sort-button active" onclick="sortTable('wins')">Most Wins</button>
            <button id="sort-losses" class="sort-button" onclick="sortTable('losses')">Most Losses</button>
            <button id="sort-draws" class="sort-button" onclick="sortTable('draws')">Most Draws</button>
            <button id="sort-totalGames" class="sort-button" onclick="sortTable('totalGames')">Most Games</button>
            <button id="sort-winPercent" class="sort-button" onclick="sortTable('winPercent')">Highest Win%</button>
        </div>
        <table id="scoreboard-table">
        <thead>
            <tr>
                <th></th>
                <th>Username</th>
                <th>Wins</th>
                <th>Losses</th>
                <th>Draws</th>
                <th>Total Games</th>
                <th>Win%</th>
            </tr>
        </thead>
        <tbody id="scoreboard-body">
            <!-- Data will be populated here -->
        </tbody>
    </table>
    </div>
    <script src="js/scoreboard.js"></script>
    <script src="js/color_modes.js"></script>
</body>
</html>
