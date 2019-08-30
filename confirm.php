<?php
session_start();

//トークンの一致と値が空でないかを確認
if(   empty($_POST['token'])                    //トークンが空
      || $_SESSION['token'] != $_POST['token']  //トークンが一致しない
      || empty($_POST['user_name'])
      || empty($_POST['seimei'])
      || empty($_POST['phone'])
      || empty($_POST['email'])
      || empty($_POST['user_password'])
      || empty($_POST['user_password_confirm']) ){
     
       header('Location: regist.php');
}   
        
//POST値をセッションに代入
    $_SESSION['regist']['user_name'] = $_POST['user_name'];
    $_SESSION['regist']['profile_image'] = !empty(trim($_POST['profile_image']))? trim($_POST['profile_image']): "no-image.png" ;
    $_SESSION['regist']['seimei'] = $_POST['seimei'];
    $_SESSION['regist']['phone'] = $_POST['phone'];
    $_SESSION['regist']['email'] = $_POST['email'];
    $_SESSION['regist']['user_password'] = $_POST['user_password']; 

//サニタイズ用関数
    function hsc($str){
        $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        $str = str_replace('-','ー',$str);
        $str = str_replace(',','、',$str);
        return $str;
    }

//内容確認の出力部分 <dl>の中に挿入
    $input_arr = [  "user_name" => "ニックネーム",
                    "profile_image" => "プロフィール画像",
                    "seimei" => "お名前",
                    "phone" => "電話",
                    "email" => "メールアドレス",
                    "user_password" => "パスワード",
    ];

    $body = '';
    foreach ($input_arr as $nameAttr => $label){
                
        switch ($nameAttr){

            //パスワードは入力値を直接表示しない
            case 'user_password':   $output = "";
                                    for($i = 1; $i <= strlen($_POST[$nameAttr]); $i++){
                                        $output .= '●';
                                    }
                                    $body .= "<dt>$label</dt><dd>" . $output . "</dd>";
                                    break;

            //プロフィール画像は未登録or登録で出力を分岐                        
            case 'profile_image' :  $img_dir = "profile_image/";
            
                                    if( empty(trim($_POST[$nameAttr])) ){
                                       
                                        

                                        //未登録
                                        $img = "<div class='img-wrapper'><img alt='' src='" . $img_dir . "no-image.png'>";
                                        $body .= "<dt>{$label}</dt><dd>{$img}</dd>";
                                        
                                    } else {
                                        //登録
                                        $img = "<div class='img-wrapper'><img alt='' src='" . $img_dir . trim($_POST[$nameAttr]) . "'>";
                                        $body .= "<dt>{$label}</dt><dd>{$img}</dd>";
                                    }
                                    break;

            default: $body .= "<dt>$label</dt><dd>" . hsc($_POST[$nameAttr]) . "</dd>";
        }
    }
?>






<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="style.css">
    <title>確認画面</title>
    <script type="text/javascript" src="//webfonts.xserver.jp/js/xserver.js"></script>
    
    <style>
        
        

       
    </style>
    
</head>



<body>
<header>
    <div class="container">
        <div class="header-inner">
        <h1>登録内容の確認</h1>
        </div>
    </div>
</header>  
   

<main>
    <div class="container">
        <div class="main-inner">
            <dl class="confirm-dl">
                <?= $body ?>
            </dl>
            <p>上記の内容で登録します。よろしいですか?</p>
            <a class="button-like" href="#" onclick="history.back()">戻る</a>
            <a class="button-like regist-submit" href="complete.php" >登録</a>
        </div>
            
        
    </div>
</main>

</body>
</html>