<?php

  require('../connection/connection.php');
  global $host;
  global $user;
  global $pass;
  global $dbname;

  ob_start();
  require('../validation/checkAdmin.php');
  ob_end_clean();
  global $isAdmin;

  if($isAdmin == 1){
      try {
          $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);

          $sql = 'SELECT Time, Output FROM updateProcess ORDER BY Time DESC';
          $stmt = $pdo->prepare($sql);
          $stmt->execute();
          $getRowCount = $stmt->rowCount();

          if($getRowCount != 0) {
              $JSON = array();
              while ($result = $stmt->fetch()){
                  $data = array('time' => $result[0],
                      'output' => $result[1]);
                  array_push($JSON, $data);
              }

              // Store in JSON to send back to client-side
              $JSON = json_encode(array( 'data' => $JSON));

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
  } else {
      echo "1";
  }
?>
