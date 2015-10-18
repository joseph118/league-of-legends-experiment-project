<?php
    $username = $_POST['username'];

    require('../connection/connection.php');
    global $host;
    global $user;
    global $pass;
    global $dbname;

    try {
        $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);

        $sql = 'DELETE va.*, vt.*
                FROM unverifiedAccount AS va
                  INNER JOIN unverifiedTeam AS vt
                    ON va.unverifiedTeamID = vt.unverifiedTeamID
                WHERE va.username = ?';

        $stmt = $pdo->prepare($sql);
        $insertVar = array($username);
        $stmt->execute($insertVar);

        echo "100";

        $stmt = null;
        $pdo = null;
    } catch (PDOException $e) {
        echo "Server Error";
        die();
    }
?>
