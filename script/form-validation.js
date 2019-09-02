//挙動確認のためrequired属性をオフ 最後にコメントアウト
// $(function(){
// 	$('[required]').prop("required", "");
// });


//各入力項目をチェックしてOKならtrue; 全部trueになれば送信可にする
var returnFlag = {};
returnFlag['#user_name'] = false;
returnFlag['#phone'] = false;
returnFlag['#email'] = false;
returnFlag['#user_password'] = false;
returnFlag['#user_password_confirm'] = false;

returnFlag['chofuku_#user_name'] = false;
returnFlag['chofuku_#email'] = false;



// アラート表示のための変数&関数 =======================================================================================

// アラート表示の際に埋め込むアイコンとテキストを変数にセット
var icon = '<i class="fas fa-exclamation-circle"></i>'; //faを読み込んでおく
var alertMessage = {};
alertMessage['#user_name'] = '5文字以上で入力してください';
alertMessage['#seimei'] = '名前を入力してください';
alertMessage['#phone'] = 'ハイフン無しの数字10-11桁で入力してください';
alertMessage['#email'] = '正しい形式で入力してください';
alertMessage['#user_password'] = '8~25文字かつ英字と数字の両方を含む必要があります';
alertMessage['#user_password_confirm'] = 'パスワードが一致しません';


//アラート表示・非表示する関数 check関数から呼び出す

	//アラート表示 + テキストボックスにnot-validクラスを付加
	function showAlert(s){
			$(s).addClass('not-valid');
			$(s+' ~ .alert').show(300);
			$(s+' ~ .alert').html(icon + '&nbsp;' + alertMessage[s]);
	}

	//アラート非表示 + not-validクラス除去
	function hideAlert(s){
			$(s).removeClass('not-valid');
			$(s+' ~ .alert').hide(300);
			$(s+' ~ .alert').html('');
	}



// 各項目をチェックする関数 =======================================================================================

// submit or テキストボックスのchangeイベントが発生したらここの関数を呼び出す
// 引数(s)にはJQueryで記述するセレクタ名を代入 ex. ('#phone')
// 1. 値をトリムして再代入
// 2. 1の値をチェック
// 3. NGならshowAlert()を呼び出しFlagにfalse代入;OKならhideAlert()を呼び出しFlagにtrue代入

function checkUserName(s){
	let input = $(s).val();
	let trimmed = $.trim(input);
	$(s).val(trimmed);
	
	if( $(s).val().length < 5 ){
		showAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = false;
	} else {
		hideAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = true;
	}
}

function checkSeimei(s){
	let input = $(s).val();
	let trimmed = $.trim(input);
	$(s).val(trimmed);
	
	if( $(s).val().length < 1 ){
		showAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = false;
	} else {
		hideAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = true;
	}
}

function checkPhone(s){
	let input = $(s).val();
	let trimmed = $.trim(input);
	$(s).val(trimmed);
	
	if( !trimmed.match(/^\d{10,11}$/) ){
		showAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = false;
	} else {
		hideAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = true;
	}
}

function checkEmail(s){
	let input = $(s).val();
	let trimmed = $.trim(input);
	$(s).val(trimmed);
	
	if( !trimmed.match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])*\.+([a-zA-Z0-9\._-]+)+$/) ){
		showAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = false;
	} else {
		hideAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = true;
	}
}

function checkUserPassword(s){
	let input = $(s).val();
	let trimmed = $.trim(input);
	$(s).val(trimmed);
	
	if( !trimmed.match(/(?=.{8,25})(?=.*\d+.*)(?=.*[a-zA-Z]+.*).*/) ){
		showAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = false;
	} else {
		hideAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = true;
	}
}

function checkUserPasswordConfirm(s){
	let input = $(s).val();
	let trimmed = $.trim(input);
	$(s).val(trimmed);
	
	if( trimmed != $('#user_password').val() ){
		showAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = false;
	} else {
		hideAlert(s);
		// alert(s+" の値は '"+trimmed+"'");
		returnFlag[s] = true;
	}
}



// Ajax DB接続して重複をチェックする関数===============================================================================================================

function checkChofukuUserName() {
	// POSTメソッドで送るデータを定義します var data = {パラメータ名 : 値};
	var data = {'user_name' : $('#user_name').val()};

	$.ajax({
	type: "POST",
	url: "ajax-check-username.php",
	data: data,
	})
	
	.done(function(data, dataType) {
		if( data > 0 ){ 
			// data > 0になるのは値が重複しているとき
			$('.alert-chofuku').html(icon + '&nbsp;' + 'このニックネームは登録済みです｡別のニックネームを入力してください。');
			$('.alert-chofuku').show(300);
			$('#user_name').addClass('not-valid-chofuku');
			returnFlag['chofuku_#user_name'] = false;
		}
		else {
			$('.alert-chofuku').html('');
			$('.alert-chofuku').hide(300);
			$('#user_name').removeClass('not-valid-chofuku');
			returnFlag['chofuku_#user_name'] = true;
		}
	}) // end .done

	.fail(function(XMLHttpRequest, textStatus, errorThrown) {
		alert('Error : ' + errorThrown);
	});
}

function checkChofukuEmail() {
	// POSTメソッドで送るデータを定義します var data = {パラメータ名 : 値};
	var data = {'email' : $('#email').val()};

	$.ajax({
	type: "POST",
	url: "ajax-check-email.php",
	data: data
	})
	
	.done(function(data, dataType) {
		if( data > 0 ){ 
			// data > 0になるのは値が重複しているとき
			$('.alert-chofuku-email').html(icon + '&nbsp;' + 'このメールアドレスは登録済みです｡別の値を入力してください。');
			$('.alert-chofuku-email').show(300);
			$('#email').addClass('not-valid-chofuku');
			returnFlag['chofuku_#email'] = false;
		}
		else {
			$('.alert-chofuku-email').html('');
			$('.alert-chofuku-email').hide(300);
			$('#email').removeClass('not-valid-chofuku');
			returnFlag['chofuku_#email'] = true;
		}
	}) // end .done

	.fail(function(XMLHttpRequest, textStatus, errorThrown) {
		alert('Error : ' + errorThrown);
	});
}


// changeイベント check関数を呼び出す =======================================================================================

$('#user_name').change(function(){
	checkUserName('#user_name');

	//checkUserName()を実行した結果, retrunFlag['#user_name']がtrueなら重複の確認も行う
	//falseなら,5文字未満でNG値なので重複を確認する必要もない
	if( returnFlag['#user_name'] == true){
		checkChofukuUserName();
	}else {
		$('.alert-chofuku').html('');
		$('.alert-chofuku').hide(300);
		$('#user_name').removeClass('not-valid-chofuku');
	}
});

$('#seimei').change(function(){
	checkSeimei('#seimei');
});

$('#phone').change(function(){
	checkPhone('#phone');
});

$('#email').change(function(){
	checkEmail('#email');

	//checkEmail()を実行した結果, retrunFlag['#email']がtrueなら重複の確認も行う
	//falseなら,そもそもメールアドレスの形式を満たしていないので,重複を確認する必要もない
	if( returnFlag['#email'] == true){	
		checkChofukuEmail();
	}else {
		$('.alert-chofuku-email').html('');
		$('.alert-chofuku-email').hide(300);
		$('#email').removeClass('not-valid-chofuku');
	}
});

$('#user_password').change(function(){
	checkUserPassword('#user_password');
});

$('#user_password_confirm').change(function(){
	checkUserPasswordConfirm('#user_password_confirm');
});



// onsubmit  全てのcheck関数を呼び出す =====================================================================================

function formValidation(){
	
	checkUserName('#user_name');
	checkSeimei('#seimei');
	checkPhone('#phone');
	checkEmail('#email');
	checkUserPassword('#user_password');
	checkUserPasswordConfirm('#user_password_confirm');

	if( returnFlag['#user_name'] == true)	checkChofukuUserName();
	if( returnFlag['#email'] == true)		checkChofukuEmail();
	
	//配列returnFlagを回して全部trueか確認
	// for( key in returnFlag ){
	// 	alert(key+" : "+returnFlag[key]);
	// }
	for( key in returnFlag ){
		if ( returnFlag[key] != true ) return false;
	}
}




















// //挙動確認のためrequired属性をオフ > 後でコメントアウト
// // $(function(){
// // 	$('[required]').prop("required","");
// // });


// // 全部の項目がtrueなら送信可能とする
// // falseのときはアラートを出す
// var returnFlag = {};
// returnFlag["user_name"] = false;
// returnFlag['seimei'] = false;
// returnFlag['phone'] = false;
// returnFlag["email"] = false;
// returnFlag["user_password"] = false;
// returnFlag["user_password_confirm"] = false;



// /* user_name 5文字以上 */
// $('#user_name').change(function(){
// 	let userInput = $(this).val();
// 	let trimmedUserInput = $.trim(userInput);
//     $(this).val( trimmedUserInput );
    
// 	if( $(this).val().length < 5  ){
// 	  	$('#user_name + .alert').show();
// 		$(this).addClass('not-valid');
// 		returnFlag['user_name'] = false;
// 	}	else {
// 		$('#user_name + .alert').fadeOut(300);
// 		$(this).removeClass('not-valid');
// 		returnFlag['user_name'] = true;
// 	} 
// });

// /* seimei 空じゃなければなんでもok */
// $('#seimei').change(function(){
// 	let userInput = $(this).val();
// 	let trimmedUserInput = $.trim(userInput);
//     $(this).val( trimmedUserInput );
    
// 	if( $(this).val().length < 1  ){
// 	  	$('#seimei + .alert').show();
// 		$(this).addClass('not-valid');
// 		returnFlag['seimei'] = false;
// 	}	else {
// 		$('#seimei + .alert').fadeOut(300);
// 		$(this).removeClass('not-valid');
// 		returnFlag['seimei'] = true;
// 	} 
// });


// /* phone 数字のみ かつ 10-11桁 */
// $('#phone').change(function(){
//     let userInput = $(this).val();
    
//     if ( !userInput.match(/^\d{10,11}$/) ){
//         $('#phone + .alert').show();
//         $(this).addClass('not-valid');
// 		returnFlag['phone'] = false;
// 	}	else {
// 		$('#phone + .alert').fadeOut(300);
// 		$(this).removeClass('not-valid');
// 		returnFlag['phone'] = true;
// 	}
// });


// /* email a@b.c */
// $('#email').change(function(){
// let userInput = $(this).val();
// 	if ( !userInput.match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])*\.+([a-zA-Z0-9\._-]+)+$/) ){
//         $('#email + .alert').show();
//         $(this).addClass('not-valid');
// 		returnFlag['email'] = false;
// 	}	else {
// 		$('#email + .alert').fadeOut(300);
// 		$(this).removeClass('not-valid');
// 		returnFlag['email'] = true;
// 	}
// });


// /* user_password 8-25文字英数字のみ */
// $('#user_password').change(function(){
// 	let userInput = $(this).val();
// 	let trimmedUserInput = $.trim(userInput);
//     $(this).val( trimmedUserInput );
    
// 	if( !userInput.match( /(?=.{8,25})(?=.*\d+.*)(?=.*[a-zA-Z]+.*).*/ )  ){
// 	  	$('#user_password + .alert').show();
//         $(this).addClass('not-valid');
//         returnFlag['user_password'] = false;
// 	}	else {
// 		$('#user_password + .alert').fadeOut(300);
// 		$(this).removeClass('not-valid');
// 		returnFlag['user_password'] = true;
// 	} 
// });


// /* user_password_confirm user_passwordと値が等しいかどうか */
// $('#user_password_confirm').change(function(){
// 	let userInput = $(this).val();
// 	let trimmedUserInput = $.trim(userInput);
//     $(this).val( trimmedUserInput );
    
//     if( returnFlag["user_password"] == false || userInput != $('#user_password').val() ){
//             $('#user_password_confirm + .alert').show();
//             $(this).addClass('not-valid');
// 			returnFlag['user_password_confirm'] = false;
// 	}	else {
// 			$('#user_password_confirm + .alert').fadeOut(300);
// 			$(this).removeClass('not-valid');
// 			returnFlag['user_password_confirm'] = true;
// 	} 
// });

// function checkPasswordConfirm(){
//     if ( $('#user_password_confirm').val() != $('#user_password').val() ){
//         $('#user_password_confirm + .alert').show();
//             $(this).addClass('not-valid');
// 			returnFlag['user_password_confirm'] = false;
// 	}	else {
// 			$('#user_password_confirm + .alert').fadeOut(300);
// 			$(this).removeClass('not-valid');
// 			returnFlag['user_password_confirm'] = true;
// 	} 
//     }


// function formValidation(){
//     let falseCounter = 0;
//     checkPasswordConfirm();
// 	for (key in returnFlag){
// 		if( returnFlag[key] != true ){
// 			$('#'+key+' + .alert').show();
// 			$('#'+key).addClass('not-valid');
// 			falseCounter++;
// 		} // end if
// 	} // end for
// 	if(falseCounter > 0) return false;
// }







