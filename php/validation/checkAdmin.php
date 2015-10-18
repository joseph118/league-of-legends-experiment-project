<?php

    require('//home/a9668541/public_html/php/connection/connection.php');
    global $host;
    global $user;
    global $pass;
    global $dbname;

    session_start();
    if(isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        $username = $_SESSION['username'];
        // check if admin
        try {
            $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);

            $stmt = $pdo->prepare('SELECT * FROM verifiedAccount WHERE username = ?');
            $insertVar = array($username);
            $stmt->execute($insertVar);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $isAdmin = $result['isAdmin'];

            if($isAdmin == 1){
                echo "100";
            } else {
                echo "400";
            }

            $stmt = null;
            $pdo = null;
        } catch (PDOException $e) {
            echo "400";
            die();
        }
    } else {
        // Not logged in
        echo "400";
    }

?>
