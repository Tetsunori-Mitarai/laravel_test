<?php

//--------------------------------------------
// セッションチェック
//--------------------------------------------
if(!$auth_ok){
	include('./kernel/pages/auth.php');
	return;
}
//--------------------------------------------
// 引数が空じゃないか確認する。
//--------------------------------------------
if(!isset($arg_itemID_raw)||$arg_itemID_raw==''){
	include('./kernel/pages/internal_StarbaseList.php');
	return;
}
require_once "HTTP/Request.php";

//--------------------------------------------
// データベースに接続(read)
//--------------------------------------------
//if(($con = mysql_connect($DB_SERVER, $DB_USER, $DB_PASSWORD)) == 0){
if(($con = mysql_connect(localhost, souzen_jads, vdx824cy)) == 0){
	putError("データベースに接続できません：" . mysql_error());
}

//mysql_select_db("$DB_NAME", $con);
mysql_select_db("souzen_jads", $con);

//--------------------------------------------
// データベースの動作文字コードをUTF-8にセット
//--------------------------------------------
mysql_query("SET NAMES utf8",$con);

//--------------------------------------------
// SQLインジェクション対策
//--------------------------------------------
$arg_itemID = mysql_real_escape_string($arg_itemID_raw);

//--------------------------------------------
// SQLコマンドを実行（登録されているStarbase一覧から、itemIDが一致する物を探して取得）
// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
//--------------------------------------------
$sql = "SELECT * FROM kernel_api_StarbaseList WHERE itemID=".$arg_itemID;
if(! ($rset = mysql_query($sql, $con))){
	putError("実行に失敗しました(Step1)：" . mysql_error() . "<br>\n実行コマンド：\n".$sql);
	return;
}

//--------------------------------------------
// 結果取り出し
//--------------------------------------------
if(mysql_num_rows($rset) == 1){
	$f_row = mysql_fetch_row($rset);
	$KnownStarbase['corpID'] = $f_row[0];
	$KnownStarbase['itemID'] = $f_row[1];
	$KnownStarbase['typeID'] = $f_row[2];
	$KnownStarbase['ownerID'] = $f_row[3];
	$KnownStarbase['purpose'] = $f_row[4];
	$KnownStarbase['locationID'] = $f_row[5];
	$KnownStarbase['moonID'] = $f_row[6];
	$KnownStarbase['state'] = $f_row[7];
	$KnownStarbase['stateTimestamp'] = $f_row[8];
	$KnownStarbase['onlineTimestamp'] = $f_row[9];
}

//--------------------------------------------
// 結果集合を開放します
//--------------------------------------------
mysql_freeresult($rset);

//--------------------------------------------
// SQLコマンドを実行（FullAPIが登録されているDirectorを探す）
// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
//--------------------------------------------
$sql = "SELECT userID,characterID,apiKey FROM kernel_auth WHERE Director=1";
if(! ($rset = mysql_query($sql, $con))){
	putError("実行に失敗しました(step0)：" . mysql_error());
	return;
}

//--------------------------------------------
// 結果取り出し
//--------------------------------------------
$Count_Directors = mysql_num_rows($rset);
for($i=0; $i<$Count_Directors; $i++){
	$f_row = mysql_fetch_row($rset);
	$Account[$i]['userID'] = $f_row[0];
	$Account[$i]['characterID'] = $f_row[1];
	$Account[$i]['apiKey'] = $f_row[2];
}

//--------------------------------------------
// 結果集合を開放します
//--------------------------------------------
mysql_freeresult($rset);

//--------------------------------------------
// SQLインジェクション対策
//--------------------------------------------
$arg_itemID = mysql_real_escape_string($arg_itemID_raw);

//--------------------------------------------
// SQLを実行
// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
//--------------------------------------------
$sql = "SELECT timestamp FROM kernel_api_StarbaseDetail WHERE itemID=".$arg_itemID;
if(! ($rset = mysql_query($sql, $con))){
	putError("実行に失敗しました(Step1)：" . mysql_error());
	return;
}

if(mysql_num_rows($rset) == 1){
	$f_row = mysql_fetch_row($rset);
	$cachedUntil = $f_row[0]; 
}

if(mysql_num_rows($rset) > 1){
	//Todo: 複数存在するのは異常なのでtimestampが古いデータを全て削除する
	putError("Error: itemIDが一致する項目が複数見つかりました。");
	return;
}

if(mysql_num_rows($rset) == 0){
	//--------------------------------------------
	//新しく見つかったStarbaseなので問答無用でデータベースを更新しに行く
	//--------------------------------------------
	$API_OK = 0;
	for($i=0; $i<$Count_Directors; $i++){
		$url = 'http://api.eve-online.com/corp/StarbaseDetail.xml.aspx';
		$request = new HTTP_Request($url);
		$request->addQueryString('userID', $Account[$i]['userID']);
		$request->addQueryString('characterID', $Account[$i]['characterID']);
		$request->addQueryString('apiKey', $Account[$i]['apiKey']);
		$request->addQueryString('itemID', $arg_itemID_raw);
		$request->sendRequest();
		$xml = new SimpleXMLElement($request->getResponseBody());
	
		//--------------------------------------------
		//XML出力中のerror codeは$xml->error[0]['code']で取得できる
		//これがnullじゃない場合はエラーが出ていると判断する
		//--------------------------------------------
		if($xml->error[0]['code']){
			putError("取得に失敗しました:[" . $xml->error[0]['code']. "]" . $xml->error[0] ."<br />\n");
		}else{
			$j=0;
			foreach($xml->result[0]->rowset[0]->row as $xml_row){
				$StarbaseDetail_fuel[$j]['fuel_typeID'] = $xml_row['typeID'];
				$StarbaseDetail_fuel[$j]['fuel_qty'] = $xml_row['quantity'];
				// SQLを実行
				// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
				$sql = "SELECT purpose FROM eve_invControlTowerResources WHERE controlTowerTypeID = ".$KnownStarbase['typeID']." AND resourceTypeID = ".$StarbaseDetail_fuel[$j]['fuel_typeID'];
				if(! ($rset = mysql_query($sql, $con))){
					putError("実行に失敗しました(Step1)：" . mysql_error() . "<br>\n実行コマンド：\n".$sql);
					return;
				}
				$f_row = mysql_fetch_row($rset);
				$StarbaseDetail_fuel[$j]['purpose'] = $f_row[0];
				$j++;
			}

			//--------------------------------------------
			//TypeIDでソートする
			//--------------------------------------------
			$fuel_typeID = array();
			foreach($StarbaseDetail_fuel as $fuel) $fuel_typeID[] = $fuel['fuel_typeID'];
			array_multisort($fuel_typeID, SORT_ASC, SORT_NUMERIC, $StarbaseDetail_fuel);
		
			$sql =
			"REPLACE INTO kernel_api_StarbaseDetail\n".
			"(itemID, state, stateTimestamp, onlineTimestamp, usageFlags, deployFlags, allowCorporationMembers, allowAllianceMembers, onStandingDrop, onStatusDrop, onAggression, onCorporationWar, fuel1_typeID, fuel1_qty, fuel2_typeID, fuel2_qty, fuel3_typeID, fuel3_qty, fuel4_typeID, fuel4_qty, fuel5_typeID, fuel5_qty, fuel6_typeID, fuel6_qty, fuel7_typeID, fuel7_qty, fuel8_typeID, fuel8_qty, fuel9_typeID, fuel9_qty, timestamp)\n".
			"VALUES (".$KnownStarbase['itemID'].", ".
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
			
			//--------------------------------------------
			// SQLを実行
			// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
			//--------------------------------------------
			if(! ($rset = mysql_query($sql, $con))){
				putError("実行に失敗しました：" . mysql_error()."<br>\n実行コマンド：\n".$sql);
				return;
			}
			$API_OK = 1;
			break;
		}
	}
	if(!$API_OK){
		putError("APIによる取得に失敗：アクセス可能なDirectorが登録されていません");
		return;
	}
}

if(time() > (strtotime($cachedUntil) + 3600)){
	//前の更新から一時間経過しているのでAPIを読みに行く
	//--------------------------------------------
	// EVE API (StarbaseDetail.xml.aspx) 呼び出し
	//--------------------------------------------
	$API_OK = 0;
	for($i=0; $i<$Count_Directors; $i++){
		$url = 'http://api.eve-online.com/corp/StarbaseDetail.xml.aspx';
		$request = new HTTP_Request($url);
		$request->addQueryString('userID', $Account[$i]['userID']);
		$request->addQueryString('characterID', $Account[$i]['characterID']);
		$request->addQueryString('apiKey', $Account[$i]['apiKey']);
		$request->addQueryString('itemID', $arg_itemID_raw);
		$request->sendRequest();
		$xml = new SimpleXMLElement($request->getResponseBody());
	
		//--------------------------------------------
		//XML出力中のerror codeは$xml->error[0]['code']で取得できる
		//これがnullじゃない場合はエラーが出ていると判断する
		//--------------------------------------------
		if($xml->error[0]['code']){
			putError("取得に失敗しました:[" . $xml->error[0]['code']. "]" . $xml->error[0] ."<br />\n");
		}else{
			$j=0;
			foreach($xml->result[0]->rowset[0]->row as $xml_row){
				$StarbaseDetail_fuel[$j]['fuel_typeID'] = $xml_row['typeID'];
				$StarbaseDetail_fuel[$j]['fuel_qty'] = $xml_row['quantity'];
				//--------------------------------------------
				// SQLを実行
				// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
				//--------------------------------------------
				$sql = "SELECT purpose FROM eve_invControlTowerResources WHERE controlTowerTypeID = ".$KnownStarbase['typeID']." AND resourceTypeID = ".$StarbaseDetail_fuel[$j]['fuel_typeID'];
				if(! ($rset = mysql_query($sql, $con))){
					putError("実行に失敗しました(Step1)：" . mysql_error() . "<br>\n実行コマンド：\n".$sql);
					return;
				}
				$f_row = mysql_fetch_row($rset);
				$StarbaseDetail_fuel[$j]['purpose'] = $f_row[0];
				$j++;
			}
			
			//--------------------------------------------
			//TypeIDでソートする
			//--------------------------------------------
			$fuel_typeID = array();
			foreach($StarbaseDetail_fuel as $fuel) $fuel_typeID[] = $fuel['fuel_typeID'];
			array_multisort($fuel_typeID, SORT_ASC, SORT_NUMERIC, $StarbaseDetail_fuel);
		
			$sql =
			"REPLACE INTO kernel_api_StarbaseDetail\n".
			"(itemID, state, stateTimestamp, onlineTimestamp, usageFlags, deployFlags, allowCorporationMembers, allowAllianceMembers, onStandingDrop, onStatusDrop, onAggression, onCorporationWar, fuel1_typeID, fuel1_qty, fuel2_typeID, fuel2_qty, fuel3_typeID, fuel3_qty, fuel4_typeID, fuel4_qty, fuel5_typeID, fuel5_qty, fuel6_typeID, fuel6_qty, fuel7_typeID, fuel7_qty, fuel8_typeID, fuel8_qty, fuel9_typeID, fuel9_qty, timestamp)\n".
			"VALUES (".$KnownStarbase['itemID'].", ".
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
			
			//--------------------------------------------
			// SQLを実行
			// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
			//--------------------------------------------
			if(! ($rset = mysql_query($sql, $con))){
				putError("実行に失敗しました：" . mysql_error()."<br>\n実行コマンド：\n".$sql);
				return;
			}
			$API_OK = 1;
			break;
		}
	}
	if(!$API_OK){
		putError("APIによる更新に失敗：アクセス可能なDirectorが登録されていません");
		return;
	}
}

//--------------------------------------------
// SQLインジェクション対策
//--------------------------------------------
$arg_itemID = mysql_real_escape_string($arg_itemID_raw);

//--------------------------------------------
// SQLを実行
// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
//--------------------------------------------
$sql = "SELECT * FROM kernel_api_StarbaseDetail WHERE itemID=".$arg_itemID;
if(! ($rset = mysql_query($sql, $con))){
	putError("実行に失敗しました(Step21)：" . mysql_error());
	return;
}

//--------------------------------------------
// 結果取り出し
//--------------------------------------------
if(mysql_num_rows($rset) == 1){
	$f_row = mysql_fetch_row($rset);
	$StarbaseDetail['itemID'] = $f_row[0];
	$StarbaseDetail['state'] = $f_row[1];
	$StarbaseDetail['stateTimestamp'] = $f_row[2];
	$StarbaseDetail['onlineTimestamp'] = $f_row[3];
	$StarbaseDetail['usageFlags'] = $f_row[4];
	$StarbaseDetail['deployFlags'] = $f_row[5];
	$StarbaseDetail['allowCorporationMembers'] = $f_row[6];
	$StarbaseDetail['allowAllianceMembers'] = $f_row[7];
	$StarbaseDetail['onStandingDrop'] = $f_row[8];
	$StarbaseDetail['onStatusDrop'] = $f_row[9];
	$StarbaseDetail['onAggression'] = $f_row[10];
	$StarbaseDetail['onCorporationWar'] = $f_row[11];
	$StarbaseDetail_fuel[0]['fuel_typeID'] = $f_row[12];
	$StarbaseDetail_fuel[0]['fuel_qty'] = $f_row[13];
	$StarbaseDetail_fuel[1]['fuel_typeID'] = $f_row[14];
	$StarbaseDetail_fuel[1]['fuel_qty'] = $f_row[15];
	$StarbaseDetail_fuel[2]['fuel_typeID'] = $f_row[16];
	$StarbaseDetail_fuel[2]['fuel_qty'] = $f_row[17];
	$StarbaseDetail_fuel[3]['fuel_typeID'] = $f_row[18];
	$StarbaseDetail_fuel[3]['fuel_qty'] = $f_row[19];
	$StarbaseDetail_fuel[4]['fuel_typeID'] = $f_row[20];
	$StarbaseDetail_fuel[4]['fuel_qty'] = $f_row[21];
	$StarbaseDetail_fuel[5]['fuel_typeID'] = $f_row[22];
	$StarbaseDetail_fuel[5]['fuel_qty'] = $f_row[23];
	$StarbaseDetail_fuel[6]['fuel_typeID'] = $f_row[24];
	$StarbaseDetail_fuel[6]['fuel_qty'] = $f_row[25];
	$StarbaseDetail_fuel[7]['fuel_typeID'] = $f_row[26];
	$StarbaseDetail_fuel[7]['fuel_qty'] = $f_row[27];
	$StarbaseDetail_fuel[8]['fuel_typeID'] = $f_row[28];
	$StarbaseDetail_fuel[8]['fuel_qty'] = $f_row[29];
}

//--------------------------------------------
// 結果集合を開放します。
//--------------------------------------------
mysql_freeresult($rset);

//--------------------------------------------
// 情報を人間が読める形に翻訳する
// *読む人がRogueDroneならここの処理は無くてもよい
//--------------------------------------------
$Starbase['itemID'] = $KnownStarbase['itemID'];

$sql = "SELECT itemName FROM eve_mapDenormalize WHERE itemID=".$KnownStarbase['moonID'];
if(! ($rset = mysql_query($sql, $con))){
	putError("実行に失敗しました(Step3)：" . mysql_error());
	return;
}
$f_row = mysql_fetch_row($rset);
$Starbase['location'] = $f_row[0];

//--------------------------------------------
// 結果集合を開放します
//--------------------------------------------
mysql_freeresult($rset);

$sql = "SELECT typeID, typeName FROM eve_invtypes WHERE typeID=".$KnownStarbase['typeID'];
if(! ($rset = mysql_query($sql, $con))){
	putError("実行に失敗しました(Step4)：" . mysql_error());
	return;
}
$f_row = mysql_fetch_row($rset);
$Starbase['type'] = $f_row[1];

//--------------------------------------------
// 結果集合を開放します
//--------------------------------------------
mysql_freeresult($rset);

$sql = "SELECT id, characterID FROM kernel_auth WHERE characterID=".$KnownStarbase['ownerID'];
if(! ($rset = mysql_query($sql, $con))){
	putError("実行に失敗しました(Step5)：" . mysql_error());
	return;
}
if(mysql_num_rows($rset)){
	$f_row = mysql_fetch_row($rset);
	$Starbase['owner'] = $f_row[0];
}else{
	$Starbase['owner'] = "Corporation";
}	

//--------------------------------------------
// 結果集合を開放します
//--------------------------------------------
mysql_freeresult($rset);

$Starbase['purpose'] = $KnownStarbase['purpose'];

switch($StarbaseDetail['state']){
	case 0:
		$Starbase['state'] = "<span class=\"font_red\">Unanchored</span>";
		break;
	case 1:
		$Starbase['state'] = "<span class=\"font_orange\">Anchored</span>";
		break;
	case 2:
		$Starbase['state'] = "<span class=\"font_yellow\">Onlining...</span>(完了予定時刻:".$StarbaseDetail['onlineTimestamp'].")";
		break;
	case 3:
		$Starbase['state'] = "<span class=\"font_red\">Reinforced</span>(解除予定時刻:".$StarbaseDetail['stateTimestamp'].")";
		break;
	case 4:
		$Starbase['state'] = "<span class=\"font_green\">Online</span>(設置時刻:".$StarbaseDetail['onlineTimestamp'].")";
		break;
}

//--------------------------------------------
// 燃料の名前を読み込む
//--------------------------------------------
for($i=0;$i<9;$i++){
	$sql = "SELECT typeName FROM eve_invtypes WHERE typeID=".$StarbaseDetail_fuel[$i]['fuel_typeID'];
	if(! ($rset = mysql_query($sql, $con))){
		putError("実行に失敗しました(Step3)：" . mysql_error());
		return;
	}
	if(mysql_num_rows($rset)){
		$f_row = mysql_fetch_row($rset);
		$Starbase_fuel[$i]['typeName'] = $f_row[0];
	}else{
		$Starbase_fuel[$i]['typeName'] = "UnKnown";
	}
	
	$Starbase_fuel[$i]['qty'] = $StarbaseDetail_fuel[$i]['fuel_qty'];
}

//--------------------------------------------
// 燃料の残り時間を計算する
//--------------------------------------------
//$slip_time = time() - (strtotime($cachedUntil)-3600);
for($i=0;$i<9;$i++){
	$sql = "SELECT quantity FROM eve_invControlTowerResources WHERE controlTowerTypeID=".$KnownStarbase['typeID']. " AND resourceTypeID=". $StarbaseDetail_fuel[$i]['fuel_typeID'];
	if(! ($rset = mysql_query($sql, $con))){
			putError("実行に失敗しました(Step5)：" . mysql_error());
			return;
	}
	$f_row = mysql_fetch_row($rset);
	if($f_row[0]){
		$Starbase_fuel[$i]['req'] = sprintf("%d/h", $f_row[0]);
		$limit_hours = $StarbaseDetail_fuel[$i]['fuel_qty'] / $f_row[0];
		//$limit_hours = $limit_hours - $slip_time/3600;
		$limit_day = $limit_hours/24;
		if(0 < $limit_day && $limit_day < 3){
			$color_element_red  = 255;
		 	$color_element_green= $limit_day*(255/3);
		 	$color_element_blue = 0;	 	
		 	$color[$i] = sprintf("#%02x%02x%02x", $color_element_red, $color_element_green, $color_element_blue);
		}
		else if(3 <=$limit_day && $limit_day < 7){
			$color_element_red = 255 - ($limit_day-3)*(255/4);	
			$color_element_green = 255;
			$color_element_blue = 0;	 	
			$color[$i] = sprintf("#%02x%02x%02x", $color_element_red, $color_element_green, $color_element_blue);
		}
		else if(7 <=$limit_day && $limit_day <14){
			$color_element_green = 255 - ($limit_day-7)*(136/7);
			$color_element_red = ($limit_day-7)*(68/7);
			$color_element_blue= ($limit_day-7)*(255/7);	 	
			$color[$i] = sprintf("#%02x%02x%02x", $color_element_red, $color_element_green, $color_element_blue);
		}
		else{	
			$color[$i] = "#4477ff";
		}
		$limit_hours = $limit_hours%24;
		$Starbase_fuel[$i]['limit'] = sprintf("<span style=\"color:%s\">&nbsp;%dDays&nbsp;%dHours</span>", $color[$i], $limit_day, $limit_hours);
	}
}

//--------------------------------------------
// データベース接続を終了します
//--------------------------------------------
mysql_close($con);

// assign some content.
$smarty->assign('menu', $menu_item);
$smarty->assign('menu_internal', $menu_internal);
$smarty->assign('page', $page);
$smarty->assign('starbase', $Starbase);
$smarty->assign('starbase_fuel', $Starbase_fuel);	

$smarty->display('head.tpl');
$smarty->display('menu.tpl');
$smarty->display('menu_internal.tpl');
$smarty->display('internal_StarbaseDetail.tpl');
$smarty->display('copyright.tpl');
$smarty->display('foot.tpl');
exit;

?>