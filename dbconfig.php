<?php

    $servername = "localhost";
    $username = "tttextre_egbertl";
    $password = "Kk1^Snw!T^8hhE@Ee6#m";
    $dbname = "tttextre_tttextreme";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(array("error" => "Connection failed: " . $conn->connect_error)));
    }

?>