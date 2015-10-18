<?php
$username = $_POST['username'];

require('../connection/connection.php');
global $host;
global $user;
global $pass;
global $dbname;

try {
    $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);

    $sql = 'SELECT *
                FROM unverifiedAccount AS va
                  INNER JOIN unverifiedTeam AS vt
                    ON va.unverifiedTeamID = vt.unverifiedTeamID
                WHERE va.username = ?';

    $stmt = $pdo->prepare($sql);
    $insertVar = array($username);
    $stmt->execute($insertVar);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $name = $result['name'];
    $surname = $result['surname'];
    $username = $result['username'];
    $password = $result['password'];
    $summonerName = $result['summonerName'];
    $summonerID = $result['summonerID'];
    // Team Data
    $top = $result['top'];
    $middle = $result['middle'];
    $support = $result['support'];
    $marksman = $result['marksman'];
    $jungle = $result['jungle'];
    // Rank Data
    $tier = "";
    $rank = "";
    $leaguePoints = "";

    // Get the other data from Riot Server
    $key = ""; // API REST Server Tokken
    $url = "https://euw.api.pvp.net/api/lol/euw/v2.5/league/by-summoner/".$summonerID."/entry?api_key=".$key;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $getResult = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);


    if($statusCode == 200){
        $obj = json_decode($getResult, true);
        for($i = 0; $i < count($obj); $i++) {
            if($obj[$summonerID][$i]["queue"] == "RANKED_SOLO_5x5") {
                $tier = $obj[$summonerID][$i]["tier"];
                $leaguePoints = $obj[$summonerID][$i]["entries"][0]["leaguePoints"];
                $rank = $obj[$summonerID][$i]["entries"][0]["division"];
            }
        }
        if(empty($tier)) {
            $tier = "WOOD";
            $rank = "I";
            $leaguePoints = "100";
        }

        // Move into Verified Tables
        $stmt = $pdo->prepare('INSERT INTO verifiedTeam (top, middle, support, marksman, jungle) VALUES (?,?,?,?,?)');
        $insertVar = array($top,$middle,$support,$marksman,$jungle);
        $stmt->execute($insertVar);
        $getVerifiedTeamID = $pdo->lastInsertId();

        $stmt = $pdo->prepare('INSERT INTO verifiedSummonerDetails (summonerName, tier, rank, leaguePoints) VALUES (?,?,?,?)');
        $insertVar = array($summonerName,$tier,$rank,$leaguePoints);
        $stmt->execute($insertVar);
        $getVerifiedSummonerDetailsID = $pdo->lastInsertId();

        $stmt = $pdo->prepare('INSERT INTO verifiedAccount (username, name, surname, password, isAdmin, summonerID, verifiedTeamID, verifiedSummonerDetailsID) VALUES (?,?,?,?,?,?,?,?)');
        $insertVar = array($username, $name, $surname, $password, false, $summonerID, $getVerifiedTeamID, $getVerifiedSummonerDetailsID);
        $stmt->execute($insertVar);

        // Delete from Unverified Tables
        $sql = 'DELETE va.*,vt.* FROM unverifiedAccount AS va INNER JOIN unverifiedTeam AS vt ON va.unverifiedTeamID = vt.unverifiedTeamID WHERE va.username = ?';
        $stmt = $pdo->prepare($sql);
        $insertVar = array($username);
        $stmt->execute($insertVar);
        echo "100";

    } else {
        echo "Failed to Connect to Riots Server";
    }
    $stmt = null;
    $pdo = null;
} catch (PDOException $e) {
    echo "Server Error";
    die();
}
?>
