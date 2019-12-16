<?php

// kernel_authテーブルに LoginID と Password の組が
// 存在するかどうかを調べる。
//データベースから情報読み込み
	$con = ConnectDB();

	//--------------------------------------------
	// SQLインジェクション対策
	//--------------------------------------------
	$arg_id = mysql_real_escape_string($arg_id_raw);

	// SQLを実行
	// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
	$sql = "SELECT id, pwd"." FROM kernel_auth"." WHERE id='".$arg_id."'";

	if(! ($rset = mysql_query($sql, $con))){
		putError("データベースへのアクセスに失敗しました：" . mysql_error());
		return;
	}
	
	// 結果取り出し
	$n_rows = mysql_num_rows($rset);
	$f_row = mysql_fetch_row($rset);
	
	// アカウントが複数見つかる場合は異常なので、認証失敗扱い
	if( $n_rows != 1 ) {
		putError("認証失敗");
		return;
	}
	
	// パスワードが一致しない場合、認証失敗扱い
	if( $f_row[1] != md5(trim($arg_pwd))) {	
		putError("認証失敗");
		return;
	}
	
	/* 結果集合を開放 */
	mysql_freeresult($rset);
	
	//--------------------------------------------
	// SQLインジェクション対策
	//--------------------------------------------
	$sid = mysql_real_escape_string(session_id());
	$key = mysql_real_escape_string(md5($arg_id . $arg_pwd . $sid));
	
	$sql =
	"REPLACE INTO ".SESSION_TABLE." ".
	"(".SESSION_SID_FIELDNAME.", username, ".SESSION_KEY_FIELDNAME.", ".SESSION_RDATE_FIELDNAME.")".
	" VALUES ('$sid', '$arg_id', '$key', ".time().")";
	if(!($rset = mysql_query($sql, $con))){
		putError("実行に失敗しました：" . mysql_error());
		return;
	}
	
	//--------------------------------------------
	// データベース接続を終了
	//--------------------------------------------
	CloseDB($con);
	
	//--------------------------------------------
	// セッション変数に登録
	//--------------------------------------------
	$_SESSION["key"] =$key;
	
	//--------------------------------------------
	// ページをコンテンツに飛ばす
	//--------------------------------------------
	header("Location: ./index.php?a=$page");

?>
<a href="./index.php?a=$page">元のページに戻る</a>