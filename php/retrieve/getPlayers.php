<?php

  require('../connection/connection.php');
  global $host;
  global $user;
  global $pass;
  global $dbname;

  try {
      $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);

      $sql = 'SELECT va.username, va.name, va.surname, vsd.summonerName, vsd.tier, vsd.rank, vsd.leaguePoints, vt.top, vt.middle, vt.marksman, vt.jungle, vt.support FROM verifiedAccount AS va INNER JOIN verifiedSummonerDetails AS vsd ON va.verifiedSummonerDetailsID = vsd.verifiedSummonerDetailsID INNER JOIN verifiedTeam AS vt ON va.verifiedTeamID = vt.verifiedTeamID';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $getRowCount = $stmt->rowCount();

      if($getRowCount != 0) {
          $JSON = array();
          while ($result = $stmt->fetch()){
              $data = array('username' => $result[0],
                  'name' => $result[1],
                  'surname' => $result[2],
                  'summonerName' => $result[3],
                  'tier' => $result[4],
                  'rank' => $result[5],
                  'lp' => $result[6],
                  'top' => $result[7],
                  'middle' => $result[8],
                  'marksman' => $result[9],
                  'jungle' => $result[10],
                  'support' => $result[11]);
              array_push($JSON, $data);
          }

          // Convert to numerals for sorting
          $JSON = convertWordsToNumerals($JSON);

          // Sort
          // 1st Tear - 2nd Rank - 3rd League Points
          usort($JSON, 'sortArray');

          // Convert Tier Back to Alphabet
          $JSON = convertNumeralsToWords($JSON);

          // Store in JSON to send back to client-side
          $JSON = json_encode(array( 'users' => $JSON));

          echo $JSON;
      } else {
          echo "null";
      }

      $stmt = null;
      $pdo = null;
  } catch (PDOException $e) {
      echo "Server Error";
      die();
  }

    function convertNumeralsToWords($array){
        for ($i =0; $i < count($array); $i++) {
            $numeralTier = $array[$i]['tier'];

            //convert tier
            if ($numeralTier == 1){
                $array[$i]['tier'] = "Diamond";
            } elseif($numeralTier == 2){
                $array[$i]['tier'] = "Platinum";
            } elseif($numeralTier == 3) {
                $array[$i]['tier'] = "Gold";
            } elseif($numeralTier == 4){
                $array[$i]['tier'] = "Silver";
            } elseif($numeralTier == 5) {
                $array[$i]['tier'] = "Bronze";
            } else {
                $array[$i]['tier'] = "Wood";
            }
        }
        return $array;
    }

    function convertWordsToNumerals($array){
        // Diamond 1
        // Platinum 2
        // Gold 3
        // Silver 4
        // Bronze 5
        for ($i = 0; $i < count($array); $i++) {
            $wordTier = $array[$i]['tier'];
            $wordRank = $array[$i]['rank'];

            //convert tier
            if ($wordTier == "DIAMOND"){
                $array[$i]['tier'] = 1;
            } elseif($wordTier == "PLATINUM"){
                $array[$i]['tier'] = 2;
            } elseif($wordTier == "GOLD") {
                $array[$i]['tier'] = 3;
            } elseif($wordTier == "SILVER"){
                $array[$i]['tier'] = 4;
            } elseif($wordTier == "BRONZE") {
                $array[$i]['tier'] = 5;
            } else {
                $array[$i]['tier'] = 6;
            }

            //convert rank
            if ($wordRank == "I"){
                $array[$i]['rank'] = 1;
            } elseif($wordRank == "II"){
                $array[$i]['rank'] = 2;
            } elseif($wordRank == "III") {
                $array[$i]['rank'] = 3;
            } elseif($wordRank == "IV"){
                $array[$i]['rank'] = 4;
            } else {
                $array[$i]['rank'] = 5;
            }
        }
        return $array;
    }

    function sortArray($a , $b) {
        if($a['tier'] == $b['tier']) {
            if($a['rank'] == $b['rank']){
                return $b['lp'] - $a['lp'];
            }
            return $a['rank'] - $b['rank'];
        }
        return $a['tier'] - $b['tier'];
    }
?>
