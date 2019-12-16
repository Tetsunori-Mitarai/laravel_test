<?php

require_once "HTTP/Request.php";

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
//  if(($con = mysql_connect($DB_SERVER, $DB_USER, $DB_PASSWORD)) == 0){
	if(($con = mysql_connect(localhost, souzen_jads, vdx824cy)) == 0){
		putError("データベースに接続できません：" . mysql_error());
	}

//	mysql_select_db("$DB_NAME", $con);
	mysql_select_db("souzen_jads", $con);

  // SQLを実行
  // MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
  $sql = <<<SQL
SELECT *
  FROM kernel_api_corp_cache
 WHERE corporationID
SQL;
  if(! ($rset = mysql_query($sql, $con))){
    putError("実行に失敗しました：" . mysql_error());
    return;
  }

  // 結果取り出し
  $n_rows = mysql_num_rows($rset);
  $n_cols = mysql_num_fields($rset);
  $f_row = mysql_fetch_row($rset);
  for($i=0; $i<$n_rows; $i++){
  		$corporationID[$i] = $f_row[2];
  }

  /* 結果集合を開放します。 */
  mysql_freeresult($rset);

//---- ---- ---- ---- ---- ---- ---- ---- ----
// EVE API (CharacterSheet.xml.aspx) 呼び出し
//---- ---- ---- ---- ---- ---- ---- ---- ----
for($i=0; $i<$n_rows; $i++){
	$url = 'http://api.eve-online.com/corp/CorporationSheet.xml.aspx';
	$request = new HTTP_Request($url);
	$request->addQueryString('corporationID', $corporationID[$i]);
	$request->sendRequest();
	$xml = new SimpleXMLElement($request->getResponseBody());

  // SQLを実行
  // MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
	$sql = "UPDATE kernel_api_corp_cache\nSET corporationName='".$xml->result[0]->corporationName."',".
			"ticker='".$xml->result[0]->ticker."',".
			"ceoID='".$xml->result[0]->ceoID."',".
			"ceoName='".$xml->result[0]->ceoName."',".
			"stationID='".$xml->result[0]->stationID."',".
			"stationName='".$xml->result[0]->stationName."',".
  			"description='".$xml->result[0]->description."',".
			"url='".$xml->result[0]->url."',".
			"allianceID='".$xml->result[0]->allianceID."',".
  			"allianceName='".$xml->result[0]->allianceName."',".
  			"taxRate='".$xml->result[0]->taxRate."',".
  			"memberCount='".$xml->result[0]->memberCount."',".
  			"shares='".$xml->result[0]->shares."',".
  			"logo_graphicID='".$xml->result[0]->logo[0]->graphicID."',".
  			"logo_shape1='".$xml->result[0]->logo[0]->shape1."',".
  			"logo_shape2='".$xml->result[0]->logo[0]->shape2."',".
  			"logo_shape3='".$xml->result[0]->logo[0]->shape3."',".
  			"logo_color1='".$xml->result[0]->logo[0]->color1."',".
  			"logo_color2='".$xml->result[0]->logo[0]->color2."',".
  			"logo_color3='".$xml->result[0]->logo[0]->color3."'\n".
 			"WHERE corporationID='".$corporationID[$i]."'\n".
 			"LIMIT 1";
	if(! ($rset = mysql_query($sql, $con))){
		putError("実行に失敗しました：" . mysql_error()."<br>\n実行コマンド：\n".$sql);
		return;
	}
}
  
  /* データベース接続を終了します。 */
  mysql_close($con);

?>