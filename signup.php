<?php

session_start();

if ( !empty($_POST['email']) ) {
    
    // 連続送信の防止 セッションにIPが保存されていないことを確認	
    if (@$_SESSION['user_ip']['ip'] == $_SERVER['REMOTE_ADDR']
        && @$_SESSION['user_ip']['email'] == $_POST['email']
        && @$_SESSION['user_ip']['count'] > 1) 
    {
        echo '<p>連続して送信することはできません</p>';
        // echo '<meta http-equiv="refresh" content="2; URL=./">';
        exit; 
    }
    else {
    
        // トークン発行
        $token = sha1(uniqid(mt_rand(), true));
  

        // メール送信
        mb_language("Japanese"); 
        mb_internal_encoding("UTF-8");

        $email = "mail@go-at.net";
        $subject = "【go-at】登録用URLのお知らせ"; // 題名
        // 本文(URL末尾にトークンを付加)
        $body = "会員登録用URLをお送りします。\n下記より登録を完了させてください。\n<https://go-at.net/member/regist.php?k=$token>";
        $to = $_POST['email'];
        $header = "From: $email\nReply-To: $email\n";

        mb_send_mail($to, $subject, $body, $header);
 
    

        // DBにトークンとメールアドレスを登録
        require_once("connect.php");
        try {
            $dbh->beginTransaction();
            $sql = 'INSERT INTO signup (token, email) VALUES ( ?,? )';
            $stmh = $dbh->prepare($sql);

            $stmh->bindValue(1, $token, PDO::PARAM_STR );
            $stmh->bindValue(2, $_POST['email'],  PDO::PARAM_STR ); 

            $stmh->execute();
            $dbh->commit();
          
        } catch (PDOException $Exception) {
            $dbh->rollBack();
            echo "エラー：" . $Exception->getMessage();
        }

        // 送信後にセッションにユーザーIP保存
        if( empty($_SESSION['user_ip']) 
            || @$_SESSION['user_ip']['email'] != $_POST['email'] 
        ){
    	    $_SESSION['user_ip']['ip'] = $_SERVER['REMOTE_ADDR'];
    	    $_SESSION['user_ip']['email'] = $_POST['email'];
    	    $_SESSION['user_ip']['count'] = 1;
        }
         else {
    	$_SESSION['user_ip']['count']++ ; //カウントアップする
        }
        
        echo "メールを送信しました。ご確認のうえ、登録手続きを完了させてください。<br>";
        echo '<meta http-equiv="refresh" content="3; URL=./">';
        exit; 
        
    }
}
?>


<?php
    $title = 'サインアップ';        //ページタイトル
    $headerTitle = 'サインアップ【新規会員登録】';  //ヘッダータイトル
    include_once('header.php');
?>

<main>
<div class="container">
<div class="main-inner">

    <p>メールアドレスを入力してください。</p>
    <p>登録用URLが記載されたメールが送信されます。</p>
    <form action="#" method="post" onsubmit="return formValidation();">
        <label for="email" class="hissu">メールアドレス</label>
        <input id="email" type="text" name="email" required>
        <div class="alert"><i class="fas fa-exclamation-circle"></i>正しいメールアドレスを入力してください</div>
        <div class="alert-chofuku-email"><i class="fas fa-exclamation-circle"></i>このメールアドレスは登録済みです｡</div>
        <input type="submit" value="進む">
    </form>

    </div>
</div>
</main>

<script>
var returnFlag = {};
returnFlag['#email'] = false;
returnFlag['chofuku_#email'] = false;



//入力値のバリデーションチェックする関数
function checkEmail(){
	let input = $('#email').val();
	let trimmed = $.trim(input);
	$('#email').val(trimmed);
	
	if( !trimmed.match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])*\.+([a-zA-Z0-9\._-]+)+$/) ){
		$('.alert').show(300);
		returnFlag['#email'] = false;
	} else {
		$('.alert').hide(300);
		returnFlag['#email'] = true;
	}
}



//入力されたアドレスが登録済みでないかチェックする関数 (Ajax)
function checkChofukuEmail() {

    //POSTメソッドで送るデータを定義します var data = {パラメータ名 : 値};
    var data = {'email' : $('#email').val()};
    
	$.ajax({
	type: "POST",
	url: "ajax-check-email.php",
	data: data
	})
	
	.done(function(data, dataType) {
        
		if( data > 0 ){
            //0件じゃないなら既にメールアドレスが登録済み
            $('.alert-chofuku-email').show(300);
            returnFlag['chofuku_#email'] = false;
            
		}
		else {
            $('.alert-chofuku-email').hide(300);
            returnFlag['chofuku_#email'] = true;

		}
	}) // end .done

	.fail(function(XMLHttpRequest, textStatus, errorThrown) {
		alert('Error : ' + errorThrown);
	});
 }


// changeイベントで上の2関数を呼び出す
$('#email').change(function(){
	
    checkEmail();
    

	// checkEmail()を実行した結果, retrunFlag['#email']がtrueなら重複の確認も行う
    // falseなら,そもそもメールアドレスの形式を満たしていないので,重複を確認する必要もない
    
	if( returnFlag['#email'] == true){	
        checkChofukuEmail();
            
	}else {
		$('.alert-chofuku-email').hide(300);
		$('#email').removeClass('not-valid-chofuku');
	}
});



//サブミットされたとき
function formValidation(){
    checkEmail();
      
    if (    returnFlag['#email'] != true
        ||  returnFlag['chofuku_#email'] != true ){
            //どちらかがtrueでないなら送信させない
            return false;
        } 

}




</script>

</body>
</html>