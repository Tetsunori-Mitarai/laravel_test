<?php

if($auth_ok){
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
	$sql = "SELECT * FROM kernel_api_StarbaseList";
	if(! ($rset = mysql_query($sql, $con))){
		putError("実行に失敗しました：" . mysql_error());
		return;
	}
	
	// 結果取り出し
	$Count_Starbase = mysql_num_rows($rset);
	for($i=0; $i<$Count_Starbase; $i++){
		$f_row = mysql_fetch_row($rset);
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
		
		// SQLを実行
		// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
		$sql = "SELECT * FROM kernel_api_StarbaseDetail WHERE itemID=".$KnownStarbase[$i]['itemID'];
		if(! ($rset2 = mysql_query($sql, $con))){
			putError("実行に失敗しました(Step21)：" . mysql_error());
			return;
		}
		
		// 結果取り出し
		if(mysql_num_rows($rset2) == 1){
			$f_row = mysql_fetch_row($rset2);
			$StarbaseDetail[$i]['itemID'] = $f_row[0];
			$StarbaseDetail[$i]['state'] = $f_row[1];
			$StarbaseDetail[$i]['stateTimestamp'] = $f_row[2];
			$StarbaseDetail[$i]['onlineTimestamp'] = $f_row[3];
			$StarbaseDetail[$i]['usageFlags'] = $f_row[4];
			$StarbaseDetail[$i]['deployFlags'] = $f_row[5];
			$StarbaseDetail[$i]['allowCorporationMembers'] = $f_row[6];
			$StarbaseDetail[$i]['allowAllianceMembers'] = $f_row[7];
			$StarbaseDetail[$i]['onStandingDrop'] = $f_row[8];
			$StarbaseDetail[$i]['onStatusDrop'] = $f_row[9];
			$StarbaseDetail[$i]['onAggression'] = $f_row[10];
			$StarbaseDetail[$i]['onCorporationWar'] = $f_row[11];
			$StarbaseDetail_fuel[$i][0]['fuel_typeID'] = $f_row[12];
			$StarbaseDetail_fuel[$i][0]['fuel_qty'] = $f_row[13];
			$StarbaseDetail_fuel[$i][1]['fuel_typeID'] = $f_row[14];
			$StarbaseDetail_fuel[$i][1]['fuel_qty'] = $f_row[15];
			$StarbaseDetail_fuel[$i][2]['fuel_typeID'] = $f_row[16];
			$StarbaseDetail_fuel[$i][2]['fuel_qty'] = $f_row[17];
			$StarbaseDetail_fuel[$i][3]['fuel_typeID'] = $f_row[18];
			$StarbaseDetail_fuel[$i][3]['fuel_qty'] = $f_row[19];
			$StarbaseDetail_fuel[$i][4]['fuel_typeID'] = $f_row[20];
			$StarbaseDetail_fuel[$i][4]['fuel_qty'] = $f_row[21];
			$StarbaseDetail_fuel[$i][5]['fuel_typeID'] = $f_row[22];
			$StarbaseDetail_fuel[$i][5]['fuel_qty'] = $f_row[23];
			$StarbaseDetail_fuel[$i][6]['fuel_typeID'] = $f_row[24];
			$StarbaseDetail_fuel[$i][6]['fuel_qty'] = $f_row[25];
			$StarbaseDetail_fuel[$i][7]['fuel_typeID'] = $f_row[26];
			$StarbaseDetail_fuel[$i][7]['fuel_qty'] = $f_row[27];
			$StarbaseDetail_fuel[$i][8]['fuel_typeID'] = $f_row[28];
			$StarbaseDetail_fuel[$i][8]['fuel_qty'] = $f_row[29];
		}
		
		/* 結果集合を開放します。 */
		mysql_freeresult($rset2);
	}
	
	/* 結果集合を開放します。 */
	mysql_freeresult($rset);
	
	//情報を翻訳する
	for($i=0; $i<$Count_Starbase; $i++){
		$Starbase[$i]['itemID'] = $KnownStarbase[$i]['itemID'];
		$sql = "SELECT itemID, itemName FROM eve_mapDenormalize WHERE itemID=".$KnownStarbase[$i]['moonID'];
		if(! ($rset = mysql_query($sql, $con))){
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		$f_row = mysql_fetch_row($rset);
		$Starbase[$i]['location'] = $f_row[1];
		
		/* 結果集合を開放します。 */
		mysql_freeresult($rset);
	
		$sql = "SELECT typeID, typeName FROM eve_invtypes WHERE typeID=".$KnownStarbase[$i]['typeID'];
		if(! ($rset = mysql_query($sql, $con))){
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		$f_row = mysql_fetch_row($rset);
		$Starbase[$i]['type'] = $f_row[1];
		
		/* 結果集合を開放します。 */
		mysql_freeresult($rset);
	
		$sql = "SELECT id, characterID FROM kernel_auth WHERE characterID=".$KnownStarbase[$i]['ownerID'];
		if(! ($rset = mysql_query($sql, $con))){
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		if(mysql_num_rows($rset)){
			$f_row = mysql_fetch_row($rset);
			$Starbase[$i]['owner'] = $f_row[0];
		}else{
			$Starbase[$i]['owner'] = "Corporation";
		}	
		/* 結果集合を開放します。 */
		mysql_freeresult($rset);
		
		$Starbase[$i]['purpose'] = $KnownStarbase[$i]['purpose'];
		switch($KnownStarbase[$i]['state']){
			case 0:
				$Starbase[$i]['state'] = "<span class=\"font_red\">Unanchored</span>";
				break;
			case 1:
				$Starbase[$i]['state'] = "<span class=\"font_orange\">Anchored</span>";
				break;
			case 2:
				$Starbase[$i]['state'] = "<span class=\"font_yellow\">Onlining...</span>";
				break;
			case 3:
				$Starbase[$i]['state'] = "<span class=\"font_red\">Reinforced</span>";
				break;
			case 4:
				$Starbase[$i]['state'] = "<span class=\"font_green\">Online</span>";
				break;
		}
		
		$Starbase[$i]['alart'] = "Notice: nothing";
		//---- ---- ---- ---- ----
		// 燃料の名前を読み込む
		//---- ---- ---- ---- ----
		for($j=0;$j<9;$j++){
			$sql = "SELECT typeName FROM eve_invtypes WHERE typeID=".$StarbaseDetail_fuel[$i][$j]['fuel_typeID'];
			if(! ($rset = mysql_query($sql, $con))){
				putError("実行に失敗しました(Step3)：" . mysql_error());
				return;
			}
			if(mysql_num_rows($rset)){
				$f_row = mysql_fetch_row($rset);
				$Starbase_fuel[$i][$j]['typeName'] = $f_row[0];
			}else{
				$Starbase_fuel[$i][$j]['typeName'] = "UnKnown";
			}
			
			$Starbase_fuel[$i][$j]['qty'] = $StarbaseDetail_fuel[$i][$j]['fuel_qty'];
		}
		
		//---- ---- ---- ---- ----
		// 最短で無くなる燃料の残り時間を計算する
		//---- ---- ---- ---- ----
		$limit_hours=0;
		for($j=0;$j<9;$j++){
			$sql = "SELECT purpose, quantity FROM eve_invControlTowerResources WHERE controlTowerTypeID=".$KnownStarbase[$i]['typeID']. " AND resourceTypeID=". $StarbaseDetail_fuel[$i][$j]['fuel_typeID'];
			if(! ($rset = mysql_query($sql, $con))){
				putError("実行に失敗しました(Step1)：" . mysql_error() . "<br>\n実行コマンド：\n".$sql);
				return;
			}
			$f_row = mysql_fetch_row($rset);
			if($f_row[0]!=4 && $f_row[1]){
				$limit_hours_temp = $StarbaseDetail_fuel[$i][$j]['fuel_qty'] / $f_row[1];
				if($limit_hours == 0 || $limit_hours > $limit_hours_temp){
					$limit_hours = $limit_hours_temp;
					$Starbase[$i]['lowest_fuel'] = $Starbase_fuel[$i][$j]['typeName'];
				}
			}
		}
		if(0 < $limit_hours && $limit_hours < 24){
			$color_element_red  = 255;
		 	$color_element_green= 0;
		 	$color_element_blue = 0;
		 	$color = sprintf("#%02x%02x%02x", $color_element_red, $color_element_green, $color_element_blue);
			$Starbase[$i]['fuel'] = sprintf("<span style=\"color:%s\">WARNING</span>", $color, $limit_day, $limit_hours);
			$Starbase[$i]['alart'] = sprintf("<span style=\"color:%s\">Alart: Fuel low (%s :%2dhours)</span>", $color, $Starbase[$i]['lowest_fuel'], $limit_hours);
		}
		else if(24<=$limit_hours && $limit_hours < 72){
			$color_element_red = 255;	
			$color_element_green = 255;
			$color_element_blue = 0;
			$limit_days =  $limit_hours/24;
			$limit_hours =  $limit_hours%24;
		 	$color = sprintf("#%02x%02x%02x", $color_element_red, $color_element_green, $color_element_blue);
			$Starbase[$i]['fuel'] = sprintf("<span style=\"color:%s\">CAUTION</span>", $color, $limit_day, $limit_hours);
			$Starbase[$i]['alart'] = sprintf("<span style=\"color:%s\">Notice: Fuel low (%s :%2dDays %2dhours)</span>", $color, $Starbase[$i]['lowest_fuel'], $limit_days, $limit_hours);
		}
		else if(72<=$limit_hours && $limit_hours <336){
			$color_element_red = 0;
			$color_element_green = 255;
			$color_element_blue= 0;	
		 	$color = sprintf("#%02x%02x%02x", $color_element_red, $color_element_green, $color_element_blue);
			$Starbase[$i]['fuel'] = sprintf("<span style=\"color:%s\">VALID</span>", $color);
		}
		else{
			$color = "#4477ff";
			$Starbase[$i]['fuel'] = sprintf("<span style=\"color:%s\">OK</span>", $color);
		}
	}
	
	/* データベース接続を終了します。 */
	mysql_close($con);

	// assign some content.
	$smarty->assign('menu', $menu_item);
	$smarty->assign('menu_internal', $menu_internal);
	$smarty->assign('page', $page);
	$smarty->assign('starbase', $Starbase);	
	
	$smarty->display('head.tpl');
	$smarty->display('menu.tpl');
	$smarty->display('menu_internal.tpl');
	$smarty->display('internal_StarbaseList.tpl');
	$smarty->display('copyright.tpl');
	$smarty->display('foot.tpl');
	exit;
}else{
	include('./kernel/pages/auth.php');
}

?>