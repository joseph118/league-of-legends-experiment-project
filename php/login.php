<?php
    $username = $_POST['lUser'];
    $password = $_POST['lPass'];
    $password = md5("1QE".$password."qwerty!@#");

    require('connection/connection.php');
    global $host;
    global $user;
    global $pass;
    global $dbname;

    try {
        $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);
    
        $stmt = $pdo->prepare('SELECT * FROM verifiedAccount WHERE username = ?');
        $insertVar = array($username);
        $stmt->execute($insertVar);
        $getRowCount = $stmt->rowCount();
        if ($getRowCount == 1) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            //if (password_verify("1QE".$password."qwerty!@#", $result["password"] )) {
            if ($password == $result["password"] ) {
                $name = $result['name'];
                $surname = $result['surname'];

                session_start();
                $_SESSION['name'] = $name;
                $_SESSION['surname'] = $surname;
                $_SESSION['username'] = $username;

                echo "100";
            } else {
                echo "Wrong Username/Password!";
            }
        } else if ($getRowCount > 1) {
            echo "Error! Contact Administrator!";
        } else {
            echo "Wrong Username/Password!";
        }
        $stmt = null;
        $pdo = null;
    } catch (PDOException $e) {
        echo "Server Error";
        die();
    }
?>
