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

// ---- ---- ---- ---- ---- ---- ----
// SQLコマンドを実行（FullAPIが登録されているDirectorを探す）
// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
// ---- ---- ---- ---- ---- ---- ----
$sql = "SELECT userID,characterID,apiKey FROM kernel_auth WHERE Director=1";
if(! ($sql_result_Directors = mysql_query($sql, $con))){
	putError("SQLコマンドの実行に失敗しました(Director情報の取得)：" . mysql_error());
	return;
}

// ---- ---- ---- ---- ---- ---- ----
// 結果取り出し
// ---- ---- ---- ---- ---- ---- ----
$Count_Directors = mysql_num_rows($sql_result_Directors);
for($i=0; $i<$Count_Directors; $i++){
	$f_row = mysql_fetch_row($sql_result_Directors);
	$Account[$i]['userID']		 = $f_row[0];
	$Account[$i]['characterID']	 = $f_row[1];
	$Account[$i]['apiKey']		 = $f_row[2];
}

/* 結果集合を開放します */
mysql_freeresult($sql_result_Directors);

// ---- ---- ---- ---- ---- ---- ----
// SQLコマンドを実行（登録されているStarbase一覧を取得）
// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
// ---- ---- ---- ---- ---- ---- ----
$sql = "SELECT * FROM kernel_api_StarbaseList";
$sql_result_StarbaseList = mysql_query($sql, $con);
if(!$sql_result_StarbaseList){
	putError("SQLコマンドの実行に失敗しました(Starbase一覧の取得)：" . mysql_error());
	return;
}

$Count_KnownStarbase = mysql_num_rows($sql_result_StarbaseList);
for($i=0; $i<$Count_KnownStarbase; $i++){
	$Update_OK = 0;
	$f_row = mysql_fetch_row($sql_result_StarbaseList);
	$KnownStarbase[$i]['corpID'] = $f_row[0];
	$KnownStarbase[$i]['itemID'] = $f_row[1];
	$KnownStarbase[$i]['typeID'] = $f_row[2];
	$KnownStarbase[$i]['ownerID'] = $f_row[3];
	$KnownStarbase[$i]['purpose'] = $f_row[4];
	$KnownStarbase[$i]['locationID'] = $f_row[5];
	$KnownStarbase[$i]['moonID'] = $f_row[6];
	$KnownStarbase[$i]['state'] = $f_row[7];
	$KnownStarbase[$i]['stateTimestamp'] = $f_row[8];
	$KnownStarbase[$i]['onlineTimestamp'] = $f_row[9];
}

// ---- ---- ---- ---- ---- ---- ----
// 結果集合を開放します
// ---- ---- ---- ---- ---- ---- ----
mysql_freeresult($sql_result_StarbaseList);

// ---- ---- ---- ---- ---- ---- ----
// APIによる更新を試行する
// ---- ---- ---- ---- ---- ---- ----
$API_connect_cache = 0;
$API_OK = 0;
for($Starbase_row=0; $Starbase_row<$Count_KnownStarbase; $Starbase_row++){
	if($API_connect_cache){
		$url = 'http://api.eve-online.com/corp/StarbaseDetail.xml.aspx';
		$request = new HTTP_Request($url);
		$request->addQueryString('userID', $Account[$API_connect_cache]['userID']);
		$request->addQueryString('characterID', $Account[$API_connect_cache]['characterID']);
		$request->addQueryString('apiKey', $Account[$API_connect_cache]['apiKey']);
		$request->addQueryString('itemID', $KnownStarbase[$Starbase_row]['itemID']);
		$request->sendRequest();
		$xml = new SimpleXMLElement($request->getResponseBody());
	
		//XML出力中のerror codeは$xml->error[0]['code']で取得できる
		//これがnullじゃない場合はエラーが出ていると判断する
		if($xml->error[0]['code']){
			$API_OK = 0;
			putError("取得に失敗しました:[" . $xml->error[0]['code']. "]" . $xml->error[0] ."<br />\n");
		}else{
			$j=0;
			foreach($xml->result[0]->rowset[0]->row as $xml_row){
				$StarbaseDetail_fuel[$j]['fuel_typeID'] = $xml_row['typeID'];
				$StarbaseDetail_fuel[$j]['fuel_qty'] = $xml_row['quantity'];
				// SQLを実行
				// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
				$sql = "SELECT purpose FROM eve_invControlTowerResources WHERE controlTowerTypeID = ".$KnownStarbase[$Starbase_row]['typeID']." AND resourceTypeID = ".$StarbaseDetail_fuel[$j]['fuel_typeID'];
				if(! ($rset = mysql_query($sql, $con))){
					putError("実行に失敗しました(Step1)：" . mysql_error());
					return;
				}
				$f_row = mysql_fetch_row($rset);
				$StarbaseDetail_fuel[$j]['purpose'] = $f_row[0];
				$j++;
			}
			
			//TypeIDでソートする
			$fuel_typeID = array();
			foreach($StarbaseDetail_fuel as $fuel) $fuel_typeID[] = $fuel['fuel_typeID'];
			array_multisort($fuel_typeID, SORT_ASC, SORT_NUMERIC, $StarbaseDetail_fuel);
		
			$sql =
			"REPLACE INTO kernel_api_StarbaseDetail\n".
			"(itemID, state, stateTimestamp, onlineTimestamp, usageFlags, deployFlags, allowCorporationMembers, allowAllianceMembers, onStandingDrop, onStatusDrop, onAggression, onCorporationWar, fuel1_typeID, fuel1_qty, fuel2_typeID, fuel2_qty, fuel3_typeID, fuel3_qty, fuel4_typeID, fuel4_qty, fuel5_typeID, fuel5_qty, fuel6_typeID, fuel6_qty, fuel7_typeID, fuel7_qty, fuel8_typeID, fuel8_qty, fuel9_typeID, fuel9_qty, timestamp)\n".
			"VALUES (".$KnownStarbase[$Starbase_row]['itemID'].", ".
						$xml->result[0]->state[0].", '".
						$xml->result[0]->stateTimestamp[0]."', '".
						$xml->result[0]->onlineTimestamp[0]."', ".
						$xml->result[0]->generalSettings[0]->usageFlags[0].", ".
						$xml->result[0]->generalSettings[0]->deployFlags[0].", ".
						$xml->result[0]->generalSettings[0]->allowCorporationMembers[0].", ".
						$xml->result[0]->generalSettings[0]->allowAllianceMembers[0].", ".
						$xml->result[0]->combatSettings[0]->onStandingDrop[0]['standing'].", ".
						$xml->result[0]->combatSettings[0]->onStatusDrop[0]['enabled'].", ".
						$xml->result[0]->combatSettings[0]->onAggression[0]['enabled'].", ".
						$xml->result[0]->combatSettings[0]->onCorporationWar[0]['enabled'].", ";
			for($k=1;$k<5;$k++){
				foreach($StarbaseDetail_fuel as $fuel){
					if($fuel['purpose']==$k){
						$sql = $sql.$fuel['fuel_typeID'].", ";
						$sql = $sql.$fuel['fuel_qty'].", ";
					}
				}
			}
			$sql = $sql."'".$xml->currentTime."')";
			
			// SQLを実行
			// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
			if(! ($rset = mysql_query($sql, $con))){
				putError("実行に失敗しました：" . mysql_error());
				return;
			}
			$API_connect_cache = $i;
			$API_OK = 1;
		}
	}else{
		for($i=0; $i<$Count_Directors; $i++){
			$url = 'http://api.eve-online.com/corp/StarbaseDetail.xml.aspx';
			$request = new HTTP_Request($url);
			$request->addQueryString('userID', $Account[$i]['userID']);
			$request->addQueryString('characterID', $Account[$i]['characterID']);
			$request->addQueryString('apiKey', $Account[$i]['apiKey']);
			$request->addQueryString('itemID', $KnownStarbase[$Starbase_row]['itemID']);
			$request->sendRequest();
			$xml = new SimpleXMLElement($request->getResponseBody());
		
			//XML出力中のerror codeは$xml->error[0]['code']で取得できる
			//これがnullじゃない場合はエラーが出ていると判断する
			if($xml->error[0]['code']){
				$API_OK = 0;
				putError("取得に失敗しました:[" . $xml->error[0]['code']. "]" . $xml->error[0] ."<br />\n");
			}else{
				$j=0;
				foreach($xml->result[0]->rowset[0]->row as $xml_row){
					$StarbaseDetail_fuel[$j]['fuel_typeID'] = $xml_row['typeID'];
					$StarbaseDetail_fuel[$j]['fuel_qty'] = $xml_row['quantity'];
					// SQLを実行
					// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
					$sql = "SELECT purpose FROM eve_invControlTowerResources WHERE controlTowerTypeID = ".$KnownStarbase[$Starbase_row]['typeID']." AND resourceTypeID = ".$StarbaseDetail_fuel[$j]['fuel_typeID'];
					if(! ($rset = mysql_query($sql, $con))){
						putError("実行に失敗しました(Step1)：" . mysql_error());
						return;
					}
					$f_row = mysql_fetch_row($rset);
					$StarbaseDetail_fuel[$j]['purpose'] = $f_row[0];
					$j++;
				}
				
				//TypeIDでソートする
				$fuel_typeID = array();
				foreach($StarbaseDetail_fuel as $fuel) $fuel_typeID[] = $fuel['fuel_typeID'];
				array_multisort($fuel_typeID, SORT_ASC, SORT_NUMERIC, $StarbaseDetail_fuel);
			
				$sql =
				"REPLACE INTO kernel_api_StarbaseDetail\n".
				"(itemID, state, stateTimestamp, onlineTimestamp, usageFlags, deployFlags, allowCorporationMembers, allowAllianceMembers, onStandingDrop, onStatusDrop, onAggression, onCorporationWar, fuel1_typeID, fuel1_qty, fuel2_typeID, fuel2_qty, fuel3_typeID, fuel3_qty, fuel4_typeID, fuel4_qty, fuel5_typeID, fuel5_qty, fuel6_typeID, fuel6_qty, fuel7_typeID, fuel7_qty, fuel8_typeID, fuel8_qty, fuel9_typeID, fuel9_qty, timestamp)\n".
				"VALUES (".$KnownStarbase[$Starbase_row]['itemID'].", ".
							$xml->result[0]->state[0].", '".
							$xml->result[0]->stateTimestamp[0]."', '".
							$xml->result[0]->onlineTimestamp[0]."', ".
							$xml->result[0]->generalSettings[0]->usageFlags[0].", ".
							$xml->result[0]->generalSettings[0]->deployFlags[0].", ".
							$xml->result[0]->generalSettings[0]->allowCorporationMembers[0].", ".
							$xml->result[0]->generalSettings[0]->allowAllianceMembers[0].", ".
							$xml->result[0]->combatSettings[0]->onStandingDrop[0]['standing'].", ".
							$xml->result[0]->combatSettings[0]->onStatusDrop[0]['enabled'].", ".
							$xml->result[0]->combatSettings[0]->onAggression[0]['enabled'].", ".
							$xml->result[0]->combatSettings[0]->onCorporationWar[0]['enabled'].", ";
				for($k=1;$k<5;$k++){
					foreach($StarbaseDetail_fuel as $fuel){
						if($fuel['purpose']==$k){
							$sql = $sql.$fuel['fuel_typeID'].", ";
							$sql = $sql.$fuel['fuel_qty'].", ";
						}
					}
				}
				$sql = $sql."'".$xml->currentTime."')";
				
				// SQLを実行
				// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
				if(! ($rset = mysql_query($sql, $con))){
					putError("実行に失敗しました：" . mysql_error());
					return;
				}
				$API_connect_cache = $i;
				$API_OK = 1;
				break;
			}
		}
	}
}
if(!$API_OK){
	putError("APIによる取得に失敗：アクセス可能なDirectorが登録されていません");
	return;
}

/* データベース接続を終了 */
mysql_close($con);
	
print("Starbaseの一覧取得が正常に成功しました");
return;

?>