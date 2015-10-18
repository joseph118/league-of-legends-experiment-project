<?php
date_default_timezone_set("Europe/Malta");

require('../php/connection/connection.php');
global $host;
global $user;
global $pass;
global $dbname;

try {
    $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);
    $from = 0;
    $to = 0;

    $stmt = $pdo->prepare('SELECT current FROM updateCurrent WHERE updateCurrentID=1');
    $stmt->execute();
    $result = $stmt->fetch();
    $getLastUpdatedRecord = $result['current'];

    $sql = 'SELECT va.summonerID FROM verifiedAccount AS va INNER JOIN verifiedSummonerDetails as vsd ON va.verifiedSummonerDetailsID = vsd.verifiedSummonerDetailsID LIMIT :startOfRecords, 10';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':startOfRecords', (int) $getLastUpdatedRecord, PDO::PARAM_INT);
    $stmt->execute($insertVar);
    $row_count = $stmt->rowCount();

    // Update Table
    if($row_count == 0) {
        $getLastUpdatedRecord = 0;
        $from = 0;
        $to = 0;

        $stmt = $pdo->prepare('UPDATE updateCurrent SET current=? WHERE updateCurrentID=1');
        $insertVar = array($getLastUpdatedRecord);
        $stmt->execute($insertVar);

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':startOfRecords', (int) $getLastUpdatedRecord, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $from = $getLastUpdatedRecord;

        $to = $getLastUpdatedRecord + 10;

        $stmt = $pdo->prepare('UPDATE updateCurrent SET current = :to WHERE updateCurrentID = 1');
        $stmt->bindValue(':to', (int) $to, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':startOfRecords', (int) $from, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Counter is used to switch API keys
    $counter = 0;

    $error = "";
    $isOn = false;

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($records as $record) {
        $tier = "WOOD";
        $rank = "V";
        $leaguePoints = 0;
        $summonerName = "";
        $summonerID = $record['summonerID'];
        $key = "";
        
        // I've managed to key 2 keys, one of them from my friend
        if($counter < 8) {
            $key = ""; // RIOT API Key tokken 1
        } else if ($counter == 8 || ($counter > 8 && $counter < 16) ){
            $key = ""; // RIOT API Key tokken 2
            if($counter == 15) {
                $counter = 0;
            }
        }

        $url = "https://euw.api.pvp.net/api/lol/euw/v2.5/league/by-summoner/".$summonerID."/entry?api_key=".$key;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $getResult = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($statusCode == 200){
            $obj = json_decode($getResult, true);
	    $unranked = true;
            for($i = 0; $i < count($obj[$summonerID]); $i++) {
                if($obj[$summonerID][$i]["queue"] == "RANKED_SOLO_5x5") {
                    $unranked = false;
		    $tier = $obj[$summonerID][$i]["tier"];

                    $leaguePoints = $obj[$summonerID][$i]["entries"][0]["leaguePoints"];
                    $rank = $obj[$summonerID][$i]["entries"][0]["division"];
                    $summonerName = $obj[$summonerID][$i]["entries"][0]["playerOrTeamName"];
                    echo " { ".$summonerName." - Updated } ";
                }
            }

            if($unranked == true) {
            	// if unranked, get the summoner name
            	
            	$url = "https://euw.api.pvp.net/api/lol/euw/v1.4/summoner/".$summonerID."?api_key=".$key;
            	$curl = curl_init($url);            	
            	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $getResult = curl_exec($curl);
            	$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            	curl_close($curl);
            	
            	if($statusCode == 200){
			$obj = json_decode($getResult, true);
			$summonerName = $obj[$summonerID]["name"];
			echo " { ".$summonerName." - Updated } ";
		} else {
			echo " { ".$summonerID." - Fail } ";
		}
            }

            // Update Record

            $sql = 'SELECT verifiedSummonerDetailsID FROM verifiedAccount WHERE summonerID = ?';
            $stmt = $pdo->prepare($sql);
            $insertVar = array($summonerID);
            $stmt->execute($insertVar);
            $vsd = $stmt->fetch();
            $vsdID = $vsd[0];

            $sql = 'UPDATE verifiedSummonerDetails SET summonerName=?,tier=?,rank=?,leaguePoints=? WHERE verifiedSummonerDetailsID=?';
            $stmt = $pdo->prepare($sql);
            $insertVar = array($summonerName,$tier,$rank,$leaguePoints,$vsdID);
            $stmt->execute($insertVar);
        } else if ($statusCode == 503) {
            $error =  $error." { Service Unavailable } ";
            $isOn = true;
        } else {
            $error = $error." {".$summonerID." - Fail } ";
        }
        $counter++;

    }

    $date = date('Y-m-d H:i:s');

    if($error == ""){
        $sql = "INSERT INTO updateProcess (Time, Output) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $insertVar = array($date, 'Pass');
        $stmt->execute($insertVar);
    } else {
        if ($isOn == false){
            $sql = "INSERT INTO updateProcess (Time, Output) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $insertVar = array($date, 'Fail');
            $stmt->execute($insertVar);
        } else {
            $sql = "INSERT INTO updateProcess (Time, Output) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $insertVar = array($date, 'Service Unavailable');
            $stmt->execute($insertVar);
        }
    }

    echo $error;

    $stmt = null;
    $pdo = null;
} catch (PDOException $e) {
    echo $e->getMessage();
    die();
}
?>			
