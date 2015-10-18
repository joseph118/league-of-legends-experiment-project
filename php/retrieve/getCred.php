<?php
  session_start();
  $username = $_SESSION['username'];

  require('//home/a9668541/public_html/php/connection/connection.php');
  global $host;
  global $user;
  global $pass;
  global $dbname;

  try {
      $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);

      $sql = 'SELECT *
      FROM verifiedAccount AS va
      INNER JOIN verifiedSummonerDetails AS vsd
        ON va.verifiedSummonerDetailsID = vsd.verifiedSummonerDetailsID
      INNER JOIN verifiedTeam AS vt
        ON va.verifiedTeamID = vt.verifiedTeamID
      WHERE username = ?';
      $stmt = $pdo->prepare($sql);
      $insertVar = array($username);
      $stmt->execute($insertVar);
      $getRowCount = $stmt->rowCount();
      if ($getRowCount == 1) {
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          $name = $result['name'];
          $surname = $result['surname'];

          $sumID = $result['summonerID'];
          $sumName = $result['summonerName'];
          $tier = $result['tier'];
          $rank = $result['rank'];

          $top = $result['top'];;
          $middle = $result['middle'];;
          $marksman = $result['marksman'];;
          $jungle = $result['jungle'];
          $support = $result['support'];


          $data = array('name' => $name,
                        'surname' => $surname,
                        'sumID' => $sumID,
                        'sumName' => $sumName,
                        'tier' => $tier,
                        'rank' => $rank,
                        'top' => $top,
                        'middle' => $middle,
                        'marksman' => $marksman,
                        'jungle' => $jungle,
                        'support' => $support);

          $JSON = json_encode(array('credentials' => $data));

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

?>
