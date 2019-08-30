<?php

header("Content-type: text/plain; charset=UTF-8");

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])
   && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

  // Ajaxリクエストの場合のみ処理する
  if (isset($_POST['email']))  {

      //ここに何かしらの処理を書く（DB登録やファイルへの書き込みなど）
      try {
        require_once('connect.php');
        $sql= "SELECT email FROM member WHERE email = ? ";
        $stmh = $dbh->prepare($sql);
        $stmh->bindValue(1, $_POST['email'], PDO::PARAM_STR );
        $stmh->execute();
        $count = $stmh->rowCount();
        echo $count;
      } catch (PDOException $Exception) {
        print "エラー：" . $Exception->getMessage();
      }
    
    }// end if2
    
  } //end if 1
?>