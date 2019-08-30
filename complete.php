<?php
session_start();

/* ここでやること
   $_SESSION['regist']の値があるとき
      DB接続
      memberテーブルにインサート ※パスワードはハッシュ化
      user_idを取得
      自動ログインのためのセッション保存
      ログイン情報はCookieに10日間保存
      会員TOPページへリダイレクト
*/



if( empty($_SESSION['regist']['user_name'])
    || empty($_SESSION['regist']['profile_image'])
    || empty($_SESSION['regist']['seimei'])
    || empty($_SESSION['regist']['phone'])
    || empty($_SESSION['regist']['email'])
    || empty($_SESSION['regist']['user_password']) ){
            echo '<p>不正な処理が行われました</p>';
            echo '<meta http-equiv="refresh" content="3; URL=./">';
            exit; 
    }
 
else {

      // DBへ接続
      require_once("connect.php");


      // memberへインサート
      try {
            $dbh->beginTransaction();
            $sql = 'INSERT INTO member (user_name, profile_image, seimei, phone, email, user_password) VALUES ( ?,?,?,?,?,? )';
            $stmh = $dbh->prepare($sql);

            $i=0;
            $stmh->bindValue(++$i, $_SESSION['regist']['user_name'],  PDO::PARAM_STR );
            $stmh->bindValue(++$i, $_SESSION['regist']['profile_image'],  PDO::PARAM_STR ); 
            $stmh->bindValue(++$i, $_SESSION['regist']['seimei'],  PDO::PARAM_STR );
            $stmh->bindValue(++$i, $_SESSION['regist']['phone'],  PDO::PARAM_STR );//電話番号はintじゃない
            $stmh->bindValue(++$i, $_SESSION['regist']['email'],  PDO::PARAM_STR );
            // パスワードはハッシュ化したものを登録
            $pswd_hash = password_hash( $_SESSION['regist']['user_password'], PASSWORD_DEFAULT );
            $stmh->bindValue(++$i, $pswd_hash, PDO::PARAM_STR );

            $stmh->execute();

            // user_id取得 ... PDO::lastInsertId — 最後に挿入された行の ID あるいはシーケンスの値を返す
            $user_id = $dbh->lastInsertId();
            $dbh->commit();
            // echo "会員登録が完了しました。<br>";
            // echo "ユーザID: $user_id";

      } catch (PDOException $Exception) {
            $dbh->rollBack();
            echo "エラー：" . $Exception->getMessage();
      }


      // 自動ログインのためのセッション保存
      $_SESSION['login']['user_name'] = $_SESSION['regist']['user_name'];
      $_SESSION['login']['profile_image'] = $_SESSION['regist']['profile_image'];
      $_SESSION['login']['email'] = $_SESSION['regist']['email'];
      $_SESSION['login']['pswd_verify'] = password_verify( $_SESSION['regist']['user_password'], $pswd_hash);
      $_SESSION['regist'] = array();
      unset($_SESSION['regist']);


      // ログイン情報はCookieに10日間保存
      setcookie("user_id", $user_id, time()+60*60*24*10,"/"); //第4引数はパス /(DR)以下でクッキー有効つまりサイト内全体


      // 会員TOPページへリダイレクト
      echo '<p>登録が完了しました。会員ページに遷移します...</p>';
      echo '<meta http-equiv="refresh" content="3; URL=./new-member.php">';
      
}