<?php
    // Get connection
    require('connection/connection.php');
    global $host;
    global $user;
    global $pass;
    global $dbname;

    // Get Credentials and remove the echo
    ob_start();
    require('../php/retrieve/getCred.php');
    ob_end_clean();

    global $JSON;
    $JSON = json_decode($JSON, true);

    global $username;

  try {
    $pdo = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);

    // check which form has been submitted
      if(isset($_POST['name']) && isset($_POST['surname'])) {
          // Update name and surname
          // check for empty fields... if empty fields are found, replace them with
          // previous data
          $name;
          $surname;
          $lft = false;
          $top = false;
          $middle = false;
          $support = false;
          $marksman = false;
          $jungle = false;

          if(isset($_POST['lft'])) {
              $lft = true;
              if(isset($_POST['tl'])) $top = true;
              if(isset($_POST['ml'])) $middle = true;
              if(isset($_POST['s'])) $support = true;
              if(isset($_POST['m'])) $marksman = true;
              if(isset($_POST['j'])) $jungle = true;
          }

          if(empty($_POST['name'])) {
              $name = $JSON['credentials']['name'];
          } else {
              $name = ucfirst(strtolower($_POST['name']));
          }
          if(empty($_POST['surname'])) {
              $surname = $JSON['credentials']['surname'];
          } else {
              $surname = ucfirst(strtolower($_POST['surname']));
          }

          $stmt = $pdo->prepare("UPDATE verifiedAccount SET name=?,surname=? WHERE username=?");
          $insertVar = array($name, $surname, $username);
          $stmt->execute($insertVar);

          // get id
          $stmt = $pdo->prepare("SELECT verifiedTeamID FROM verifiedAccount WHERE username=?");
          $insertVar = array($username);
          $stmt->execute($insertVar);
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          $id = $result['verifiedTeamID'];



          $stmt = $pdo->prepare("UPDATE verifiedTeam SET top=?,middle=?,marksman=?,jungle=?,support=? WHERE verifiedTeamID=?");
          $insertVar = array($top, $middle, $marksman, $jungle, $support, $id);
          $stmt->execute($insertVar);

          echo "Details Updated!";

      } elseif (isset($_POST['oldPass']) && isset($_POST['newPass'])) {
          // Update passwords
          if(!empty($_POST['oldPass']) && !empty($_POST['newPass'])){
              //$oldPass = $_POST['oldPass'];
              //$newPass = $_POST['newPass'];
              $oldPass = md5("1QE".$_POST['oldPass']."qwerty!@#");
              $newPass = md5("1QE".$_POST['newPass']."qwerty!@#");

              // Get Encrypted Pass from DB
              $stmt = $pdo->prepare('SELECT * FROM verifiedAccount WHERE username = ?');
              $insertVar = array($username);
              $stmt->execute($insertVar);
              $getRowCount = $stmt->rowCount();
              if ($getRowCount == 1) {
                  $result = $stmt->fetch(PDO::FETCH_ASSOC);
                  //if (password_verify("1QE".$oldPass."qwerty!@#", $result["password"] )) {
                  if ($oldPass == $result["password"] ) {
                      //$newPass = password_hash("1QE".$newPass."qwerty!@#", PASSWORD_DEFAULT);

                      $stmt = $pdo->prepare("UPDATE verifiedAccount SET password=? WHERE username=?");
                      $insertVar = array($newPass, $username);
                      $stmt->execute($insertVar);

                      echo "Password Updated!";
                  } else {
                      echo "Incorrect Password!";
                  }
              } else {
                  echo "Error 101! Contact Administrator!";
              }
          } else {
              echo "Please Fill All Text Boxes!";
          }

      } else {
          echo "Error 102!";
      }

    $stmt = null;
    $pdo = null;
  } catch (PDOException $e) {
    echo "Server Error";
    die();
  }
?>
