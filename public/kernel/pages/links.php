<?php
	// SQLを実行
	// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
	$sql = "SELECT groupID FROM kernel_link_group";
	if(! ($result_linkGroup = mysql_query($sql, $con))){
		putError("実行に失敗しました：" . mysql_error());
		return;
	}
	
	// 結果取り出し
	$Count_linkGroup = mysql_num_rows($result_linkGroup);
	for($item=0; $item<$Count_linkGroup; $item++){
		$f_row = mysql_fetch_row($result_linkGroup);
		$groupID_tgt = $f_row[0];
		
		// SQLを実行
		// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
		$sql = "SELECT groupName_jpn FROM kernel_link_group WHERE groupID =".$groupID_tgt;
		if(! ($result_linkGroupName = mysql_query($sql, $con))){
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		
		$f_row = mysql_fetch_row($result_linkGroupName);
		$linkGroup[$item] = $f_row[0];
		
		// SQLを実行
		// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
		$sql = "SELECT linkID FROM kernel_link WHERE groupID =".$groupID_tgt;
		if(!($result_linkID = mysql_query($sql, $con))){
			putError("実行に失敗しました：". mysql_error());
			return;
		}
		// 結果取り出し
		$Count_linkID = mysql_num_rows($result_linkID);
		if(!$Count_linkID){
			$link[$item][0] = "None";
		}
		for($item2=0; $item2<$Count_linkID; $item2++){
			$f_row = mysql_fetch_row($result_linkID);
			$linkID_tgt = $f_row[0];
			
			// SQLを実行
			// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
			$sql = "SELECT text FROM kernel_link WHERE linkID =".$linkID_tgt;
			if(! ($result_linkText = mysql_query($sql, $con))){
				putError("実行に失敗しました：" . mysql_error());
				return;
			}
			$f_row = mysql_fetch_row($result_linkText);
			$link_tgt_text = $f_row[0];

			// SQLを実行
			// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
			$sql = "SELECT url FROM kernel_link WHERE linkID =".$linkID_tgt;
			if(! ($result_linkURL = mysql_query($sql, $con))){
				putError("実行に失敗しました：" . mysql_error());
				return;
			}
			$f_row = mysql_fetch_row($result_linkURL);
			$link_tgt_url = $f_row[0];
			
			// SQLを実行
			// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
			$sql = "SELECT alt FROM kernel_link WHERE linkID =".$linkID_tgt;
			if(! ($result_linkAlt = mysql_query($sql, $con))){
				putError("実行に失敗しました：" . mysql_error());
				return;
			}
			$f_row = mysql_fetch_row($result_linkAlt);
			$link_tgt_alt = $f_row[0];
			
			// SQLを実行
			// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
			$sql = "SELECT alive FROM kernel_link WHERE linkID =".$linkID_tgt;
			if(! ($result_linkAlive = mysql_query($sql, $con))){
				putError("実行に失敗しました：" . mysql_error());
				return;
			}
			$f_row = mysql_fetch_row($result_linkAlive);
			$link_tgt_alive = $f_row[0];
			
			// SQLを実行
			// MySQLでは、表の名前の大文字、小文字は区別されるので正確に指定する
			$sql = "SELECT visible FROM kernel_link WHERE linkID =".$linkID_tgt;
			if(! ($result_linkVisible = mysql_query($sql, $con))){
				putError("実行に失敗しました：" . mysql_error());
				return;
			}
			$f_row = mysql_fetch_row($result_linkVisible);
			$link_tgt_visible = $f_row[0];
			
			if($link_tgt_alive && $link_tgt_visible){
				$link[$item][$item2] = sprintf("<a href=\"%s\">%s</a>", $link_tgt_url, $link_tgt_text);
			}
			if(!$link_tgt_alive && $link_tgt_visible){
				$link[$item][$item2] ="$link_tgt_text";
			}
		}
	}
	
	//--------------------------------------------
	// データベース接続を終了します。
	//--------------------------------------------
	CloseDB($con);
	
	// assign some content.
	$smarty->assign('menu', $menu_item);
	$smarty->assign('page', $page);
	$smarty->assign('linkGroup', $linkGroup);
	$smarty->assign('link', $link);
	
	$smarty->display('head.tpl');
	$smarty->display('menu.tpl');
	$smarty->display('links.tpl');
	$smarty->display('copyright.tpl');
	$smarty->display('banners.html5.tpl');
	$smarty->display('affiliate.tpl');
	
 	// 処理終了時刻
	$endTime = microtime(true);
	$procTime = sprintf("%0.2f", ($endTime - $startTime)*1000 );
	$smarty->assign('procTime', $procTime);
 
 	// display it
	$smarty->display('procTime.tpl');
	$smarty->display('foot.tpl');
?>