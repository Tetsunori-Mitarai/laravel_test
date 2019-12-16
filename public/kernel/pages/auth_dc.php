<?php

require_once('./kernel/auth/session.php');			//セッション設定、維持するクラス

// セッション変数を全て解除する
$_SESSION = array();

// セッションを保持していたクッキー削除
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

//データベースからセッション情報を読み出す
$con = mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
if($con == 0){
	putError("データベースに接続できません：" . mysql_error());
}
	
mysql_select_db(DB_NAME, $con);

mysql_query("SET NAMES utf8",$con);

// SQLを実行
// データベースから行を delete する。
// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する

//--------------------------------------------
// SQLインジェクション対策
//--------------------------------------------
$sid = mysql_real_escape_string(session_id());

$sql =
"DELETE"." ".
"FROM ".SESSION_TABLE." ".
"WHERE ".SESSION_SID_FIELDNAME."='".$sid."'";

if(! ($rset = mysql_query($sql, $con))){
	putError("実行に失敗しました：" . mysql_error());
	return;
}

/* 結果集合を開放します。 */
mysql_freeresult($rset);

/* データベース接続を終了します。 */
mysql_close($con);

session_destroy();

// ページをコンテンツに飛ばす
header("Location: ./index.php?a=internal");

?>
<a href="./index.php?a=internal">元のページに戻る</a>