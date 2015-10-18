<?php
    $username = $_POST['sUser'];
    $name = ucfirst(strtolower($_POST['sName']));
    $surname = ucfirst(strtolower($_POST['sSurname']));
    $password = $_POST['sPassword'];

    //$password = password_hash("1QE".$password."qwerty!@#", PASSWORD_DEFAULT);
    $password = md5("1QE".$password."qwerty!@#");

    $summonerName = $_POST['sSummonerName'];

    $lookingForTeam = false;
    if (isset($_POST['y'])) {
        $lookingForTeam = true;
    }

    $top = false;
    $middle = false;
    $support = false;
    $marksman = false;
    $jungle = false;
    if($lookingForTeam == true) {
        if (isset($_POST['tl'])) {
            $top = true;
        }
        if (isset($_POST['ml'])) {
            $middle = true;
        }
        if (isset($_POST['s'])) {
            $support = true;
        }
        if (isset($_POST['m'])) {
            $marksman = true;
        }
        if (isset($_POST['j'])) {
            $jungle = true;
        }
        // if all is not set, then change all values to true
        if($top == false && $middle == false && $support == false && $marksman == false && $jungle == false) {
            $top = true;
            $middle = true;
            $support = true;
            $marksman = true;
            $jungle = true;
        }
    }

    $sumNameWeb = str_replace(' ', '', $summonerName);

    require('connection/connection.php');
    global $host;
    global $user;
    global $pass;
    global $dbname;
	
	try {
        $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);
        // Check for the same user in verified and unverified tables

        $userFound = false;
        // Verified Table
        $sql = "SELECT * FROM verifiedAccount WHERE username=?";
        $stmt = $pdo->prepare($sql);
        $insertVar = array($username);
        $stmt->execute($insertVar);
        if($stmt->fetch()) {
            $userFound = true;
        }

        // Unverified Table
        $sql = "SELECT * FROM unverifiedAccount WHERE username=?";
        $stmt = $pdo->prepare($sql);
        $insertVar = array($username);
        $stmt->execute($insertVar);
        if($stmt->fetch()) {
            $userFound = true;
        }

        if($userFound == false){
            // Get Data from Riot
            $key = ""; // RIOT API Key tokken

            $curl = curl_init();
            $url = "https://euw.api.pvp.net/api/lol/euw/v1.4/summoner/by-name/{$sumNameWeb}?api_key=".$key;

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_ENCODING ,"");
            curl_setopt($curl, CURLOPT_URL, $url);
            $result = curl_exec($curl);

            $obj = json_decode($result, true);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if($statusCode == 200){
                foreach($obj as $users){
                    $sumID = $users['id'];
                    $sumLvl = $users['summonerLevel'];
                }

                if($sumLvl == 30) {
                    // Check for the same summoner name in verified and unverified tables
                    $found = false;
                    // Verified Table
                    $sql = "SELECT * FROM verifiedAccount WHERE summonerID=?";
                    $stmt = $pdo->prepare($sql);
                    $insertVar = array($sumID);
                    $stmt->execute($insertVar);
                    if($stmt->fetch()) {
                        $found = true;
                    }

                    // Unverified Table
                    $sql = "SELECT * FROM unverifiedAccount WHERE summonerID=?";
                    $stmt = $pdo->prepare($sql);
                    $insertVar = array($sumID);
                    $stmt->execute($insertVar);
                    if($stmt->fetch()) {
                        $found = true;
                    }

                    if($found == false) {
                        // Save Data in unverified Table
                        $stmt = $pdo->prepare('INSERT INTO unverifiedTeam (top, middle, support, marksman, jungle) VALUES (?,?,?,?,?)');
                        $insertVar = array($top,$middle,$support,$marksman,$jungle);
                        $stmt->execute($insertVar);

                        $getID = $pdo->lastInsertId();

                        $stmt = $pdo->prepare('INSERT INTO unverifiedAccount (username, name, surname, password, summonerName, unverifiedTeamID, summonerID) VALUES (?,?,?,?,?,?,?) ');
                        $insertVar = array($username,$name,$surname,$password, $summonerName, $getID, $sumID);
                        $stmt->execute($insertVar);
                        echo "100";
                    } else {
                        // Summoner is in use
                        echo "Summoner already in-use!";
                    }

                } else {
                    // Summoner is not level 30
                    echo "Summoner is not level 30!";
                }
            } else {
                // Failed Connection to Riot
                echo "Failed to Connect to Riots Server!";
            }
        } else {
            // Username in use
            echo "Username in use!";
        }
        $stmt = null;
        $pdo = null;
    } catch (PDOException $e) {
        echo "Server Error";
        die();
    }
?>
