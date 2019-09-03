<?php
session_start();

if( $_SESSION['delete']['pswd_verify'] !== true ){
            echo '<p>不正な処理が行われました</p>';
            echo '<meta http-equiv="refresh" content="2; URL=./">';
            exit; 
}

//DB接続
require_once('connect.php');
try {

    //delete
    $sql= "DELETE FROM member WHERE user_name = ?";
    $stmh = $dbh->prepare($sql);
    $stmh->bindValue(1, $_SESSION['login']['user_name'], PDO::PARAM_STR );
    $stmh->execute();
    


    //セッションおよびクッキー削除
    unset($_SESSION['login']);
    $_SESSION['login'] = array();
    
    unset($_SESSION['delete']);
    $_SESSION['delete'] = array();
   
    setcookie('login[0]', "",  time()-90, "/");
    setcookie('login[1]', "", time()-90, "/");
    setcookie('login[2]', "", time()-90, "/");   
    


    echo "<p>削除しました。ご利用ありがとうございました。</p>";
    echo '<meta http-equiv="refresh" content="2; URL=./">';
    exit; 

    
} catch (PDOException $Exception) {
        echo "エラー：" . $Exception->getMessage();
}

?>
