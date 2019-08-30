<?php

header("Content-type: text/plain; charset=UTF-8");

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])
   && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

  // Ajaxリクエストの場合のみ処理する
  if (isset($_POST['user_name']))  {

      //ここに何かしらの処理を書く（DB登録やファイルへの書き込みなど）
      try { 
        require_once('connect.php');
        $sql= "SELECT user_name FROM member WHERE user_name = ? ";
        $stmh = $dbh->prepare($sql);
        $stmh->bindValue(1, $_POST['user_name'], PDO::PARAM_STR );
        $stmh->execute();
        $count = $stmh->rowCount();
        echo $count;
      } catch (PDOException $Exception) {
        print "エラー：" . $Exception->getMessage();
      } 

    }// end if2

  } //end if 1
?>