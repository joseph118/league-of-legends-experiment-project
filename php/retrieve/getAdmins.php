<?php

  require('../connection/connection.php');
  global $host;
  global $user;
  global $pass;
  global $dbname;

  try {
      $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);

      $sql = 'SELECT vsd.summonerName FROM verifiedAccount AS va INNER JOIN verifiedSummonerDetails AS vsd ON va.verifiedSummonerDetailsID = vsd.verifiedSummonerDetailsID WHERE va.isAdmin=true GROUP BY vsd.summonerName';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $getRowCount = $stmt->rowCount();

      if($getRowCount != 0) {
          $JSON = array();
          while ($result = $stmt->fetch()){
              $data = array('username' => $result[0]);
              array_push($JSON, $data);
          }

          // Store in JSON to send back to client-side
          $JSON = json_encode(array( 'users' => $JSON));

          echo $JSON;
      } else {
          echo "{ Currently No Admins! }";
      }

      $stmt = null;
      $pdo = null;
  } catch (PDOException $e) {
      echo "Server Error";
      die();
  }
?>
