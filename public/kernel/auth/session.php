<?

//セッションがオープンした際に実行される。
//引数は2で、保管ファイルパス、セッション名。
//データベースに保管するため、保管ファイルパスは$dummyで受け取るが無視する。
function my_session_open($dummy, $sid){
	return(true);
}

function my_session_close(){
	return(true);
}

function my_session_read($sid){
    //データベースからセッション情報を読み出す
	$con = connectDB();

	// SQLを実行
	// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
	$sql =
"SELECT * ".
"FROM ".SESSION_TABLE." ".
"WHERE ".SESSION_SID_FIELDNAME."='".$sid."'";

	if(! ($rset = mysql_query($sql, $con))){
		return false;
	}

	$n_rows = mysql_num_rows($rset);
	$f_row = mysql_fetch_row($rset);
	
	/* 結果集合を開放します。 */
	mysql_freeresult($rset);

	/* データベース接続を終了します。 */
	mysql_close($con);

	if($n_rows > 0) {
		return ($f_row[3]);
	}
    return(true);
}

// セッション変数に変更があったときに呼ばれる。
function my_session_write($sid, $data) {
	// update する。この関数は何回も呼ばれる（セッション内で、ページの
	// 遷移ごとに一回呼び出し）のが普通なので、update するのが基本です。
	// ということで、セッション管理に PostgreSQL は、あまり向いていない
	// かもしれません。なぜなら、vacuum にかかる時間が長くなるからです。
	// 特に、アクセス数の多いサイトでは、絶望的です。その場合は MySQL
	// 等を使いましょう。

	//書き込みデータをデコードして書き込める状態にする
	session_decode($data);

    //データベースからセッション情報を読み出す
	$con = connectDB();

	$sql =
"SELECT * ".
"FROM kernel_session"." ".
"WHERE session_id='".$sid."'";

	if(! ($rset = mysql_query($sql, $con))){
		return(false);
	}
	$f_row = mysql_fetch_row($rset);
	$username = $f_row[1];
	$key = $f_row[2];

	$sql =
"REPLACE INTO ".SESSION_TABLE." ".
"(".SESSION_SID_FIELDNAME.", username, session_key, ".SESSION_RAWDATA_FIELDNAME.", ".SESSION_RDATE_FIELDNAME.")".
" VALUES ('$sid', '$username', '$key', '$data', '".time()."')";

	if(! ($rset = mysql_query($sql, $con))){
		return(false);
	}

	/* データベース接続を終了します。 */
	mysql_close($con);

	return(true);
}

function my_session_destroy($sid){
    //データベースからセッション情報を読み出す
	$con = connectDB();

	// SQLを実行
	// データベースから行を delete する。
	// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
	$sql =
"DELETE"." ".
"FROM ".SESSION_TABLE." ".
"WHERE ".SESSION_SID_FIELDNAME."='".$sid."'";

	if(! ($rset = mysql_query($sql, $con))){
		return(false);
	}

	/* 結果集合を開放します。 */
	mysql_freeresult($rset);

	/* データベース接続を終了します。 */
	mysql_close($con);

	return(true);
}

function my_session_gc($maxlife){
	$now = " < ('now'::timestamp + '-".SESSION_GC_DAYS." day')";
	
    //データベースからセッション情報を読み出す
	$con = connectDB();

	// SQLを実行
	// データベースから行を delete する。
	$sql =
"DELETE"." ".
"FROM ".SESSION_TABLE." ".
"WHERE ".SESSION_RDATE_FIELDNAME."<'".time()."'";

	if(! ($rset = mysql_query($sql, $con))){
		return(false);
	}

	/* 結果集合を開放します。 */
	mysql_freeresult($rset);

	/* データベース接続を終了します。 */
	mysql_close($con);

	return(true);
}

//--------------------------------------------
// セッションの確立の有無を確認
//--------------------------------------------
function my_session_check($con, $key, $session_id){
	// SQLを実行
	// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
	$sql = <<<SQL
SELECT session_key
  FROM kernel_session
 WHERE session_key='$key'
SQL;
	
	if(!($rset = mysql_query($sql, $con)))
	{
		putError("実行に失敗しました：" . mysql_error());
		return;
	}
	//見つかった$keyが一致して且つ
	//session_idがひとつしか見つからないなら、
	//正常に認証できているので$auth_okに1をセット
	$nrows = mysql_num_rows($rset);
	if($nrows == 1){
		$auth_ok = 1;
	}
	else
	{
		$auth_ok = 0;
	}
	
	/* 結果集合を開放します。 */
	mysql_freeresult($rset);

	return($auth_ok);
}

//session_set_save_handler()は自作のセッション制御関数を指示する
//今回のようにデータベースでセッション管理をする場合などに使用する
session_set_save_handler(
		"my_session_open",	
		"my_session_close",
		"my_session_read",
		"my_session_write",
		"my_session_destroy",
		"my_session_gc");
?>