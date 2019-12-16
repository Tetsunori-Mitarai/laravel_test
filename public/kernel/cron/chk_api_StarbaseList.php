<?php

require_once "HTTP/Request.php";

//PHPの内部処理をUTF-8に設定
mb_language("uni");
mb_internal_encoding("utf-8"); 
mb_http_input("auto");
mb_http_output("utf-8");

//ヘッダー設定、XHTML指定
header('Content-type: text/html; charset=UTF-8');

function putError($msg){
  $url = $_SERVER[REQUEST_URI];
  print <<<MSG
  	<div>
	    アクセス失敗：$msg
	</div>
    </body>
</html>
MSG;
  exit(0);
}


// ---- ---- ---- ---- ---- ---- ----
// データベースに接続(read)
// ---- ---- ---- ---- ---- ---- ----
//if(($con = mysql_connect($DB_SERVER, $DB_USER, $DB_PASSWORD)) == 0){
if(($con = mysql_connect(localhost, souzen_jads, vdx824cy)) == 0){
	putError("データベースに接続できません：" . mysql_error());
}

//mysql_select_db("$DB_NAME", $con);
mysql_select_db("souzen_jads", $con);

//データベースの動作文字コードをUTF-8にセット
mysql_query("SET NAMES utf8",$con);

// SQLを実行
// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
$sql = "SELECT * FROM kernel_auth WHERE Director=1";
if(! ($rset = mysql_query($sql, $con))){
	putError("実行に失敗しました：" . mysql_error());
	return;
}

// 結果取り出し
$Count_Directors = mysql_num_rows($rset);
for($i=0; $i<$Count_Directors; $i++){
	$f_row = mysql_fetch_row($rset);
	$Account[$i]['userID'] = $f_row[2];
	$Account[$i]['characterID'] = $f_row[3];
	$Account[$i]['apiKey'] = $f_row[4];
}

/* 結果集合を開放します */
mysql_freeresult($rset);

//---- ---- ---- ---- ---- ---- ---- ---- ----
// EVE API (CharacterSheet.xml.aspx) 呼び出し
//---- ---- ---- ---- ---- ---- ---- ---- ----
for($i=0; $i<$Count_Directors; $i++){
	$url = 'http://api.eve-online.com/corp/StarbaseList.xml.aspx';
	$request = new HTTP_Request($url);
	$request->addQueryString('userID', $Account[$i]['userID']);
	$request->addQueryString('characterID', $Account[$i]['characterID']);
	$request->addQueryString('apiKey', $Account[$i]['apiKey']);
	$request->sendRequest();
	$xml = new SimpleXMLElement($request->getResponseBody());
	
	print("APIサーバーに接続を試みました(characterID=".$Account[$i]['characterID'].")<br />\n");

	//XML出力中のerror codeは$xml->error[0]['code']で取得できる
	//これがnullじゃない場合はエラーが出ていると判断する
	if($xml->error[0]['code']){
			putError("取得に失敗しました:[" . $xml->error[0]['code']. "]" . $xml->error[0] ."<br />\n");
			return;	
	}
	$sql = "SELECT * FROM kernel_api_StarbaseList";
	if(! ($rset = mysql_query($sql, $con))){
		putError("実行に失敗しました：" . mysql_error());
		return;
    	}
	$Count_KnownStarbase = mysql_num_rows($rset);
	for($j=0; $j<$Count_KnownStarbase; $j++){
		$Update_OK = 0;
		$f_row = mysql_fetch_row($rset);
		$KnownStarbase[$j]['corpID'] = $f_row[0];
		$KnownStarbase[$j]['itemID'] = $f_row[1];
		$KnownStarbase[$j]['typeID'] = $f_row[2];
		$KnownStarbase[$j]['ownerID'] = $f_row[3];
		$KnownStarbase[$j]['purpose'] = $f_row[4];
		$KnownStarbase[$j]['locationID'] = $f_row[5];
		$KnownStarbase[$j]['moonID'] = $f_row[6];
		$KnownStarbase[$j]['state'] = $f_row[7];
		$KnownStarbase[$j]['stateTimestamp'] = $f_row[8];
		$KnownStarbase[$j]['onlineTimestamp'] = $f_row[9];
		foreach($xml->result[0]->rowset[0]->row as $UpdateStarbase) {
			// 新しいリストにまだ存在するので更新
			if($KnownStarbase[$j]['itemID'] == $UpdateStarbase['itemID']){
				$sql = "UPDATE kernel_api_StarbaseList\nSET ".
						"itemID=".$UpdateStarbase['itemID'].
						"typeID=".$UpdateStarbase['typeID'].
						"locationID=".$UpdateStarbase['locationID'].
						"moonID=".$UpdateStarbase['moonID'].
						"state=".$UpdateStarbase['state'].
						"stateTimestamp=".$UpdateStarbase['stateTimestamp'].
						"onlineTimestamp=".$UpdateStarbase['onlineTimestamp'].
 							"WHERE 'itemID'=".$KnownStarbase[$i]['itemID'];
				$Update_OK = 1;
				break;
			}
    	}
    	if(!$Update_OK){
    		//新しいリストに存在しないので削除
    		$sql = "DELETE"." "."FROM kernel_api_StarbaseList WHERE itemID=".$KnownStarbase[$j]['itemID'];

			// SQLを実行
			// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
			if(! ($rset = mysql_query($sql, $con))){
				putError("実行に失敗しました：" . mysql_error()."<br>\n実行コマンド：\n".$sql);
				return;
			}
		}
	}
	
	/* 結果集合を開放します */
	mysql_freeresult($rset);
	
	//新しく建った分が無いかサーチして、データベース側になければ追加
	foreach($xml->result[0]->rowset[0]->row as $UpdateStarbase) {
		$sql =
		"REPLACE INTO kernel_api_StarbaseList\n".
		"(itemID, typeID, locationID, moonID, state, stateTimestamp, onlineTimestamp)\n".
		"VALUES (".$UpdateStarbase['itemID'].", ".
					$UpdateStarbase['typeID'].", ".
					$UpdateStarbase['locationID'].", ".
					$UpdateStarbase['moonID'].", ".
					$UpdateStarbase['state'].", '".
					$UpdateStarbase['stateTimestamp']."', '".
					$UpdateStarbase['onlineTimestamp']."')";
		// SQLを実行
		// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
		if(! ($rset = mysql_query($sql, $con))){
			putError("実行に失敗しました：" . mysql_error()."<br>\n実行コマンド：\n".$sql);
			return;
		}
    }
	
	/* データベース接続を終了 */
	mysql_close($con);
	
	print("Starbaseの一覧取得が正常に成功しました");
	return;
}

/* データベース接続を終了します。 */
mysql_close($con);

print("Starbaseの一覧取得に必要なAPIを持つキャラクターが登録されていません");

?>