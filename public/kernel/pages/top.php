<?php

// assign some content.
$smarty->assign('menu', $menu_item);
$smarty->assign('page', $page);

/*
if (!isset($_SESSION['kernel']))
{				// kernel(統合WEBシステム)にアクセスするセッションが確立されているか検査
	$smarty->display('login.tpl');
}
else
{
	$smarty->display('whois.tpl');
}
*/
	GetCorpInfoFromDB($con, &$corp_info);
	
	//--------------------------------------------
	// データベース接続を終了します。
	//--------------------------------------------
	CloseDB($con);

	$smarty->display('head.tpl');
	$smarty->display('affiliate.tpl');
	$smarty->display('menu.tpl');
	$smarty->display('top.tpl');
	$smarty->display('banners.tpl');
	$smarty->display('copyright.tpl');
	
	// 処理終了時刻
	$endTime = microtime(true);
	$procTime = sprintf("%0.2f", ($endTime - $startTime)*1000 );
	$smarty->assign('procTime', $procTime);
	 
		// display it
	$smarty->display('procTime.tpl');
	$smarty->display('foot.tpl');

?>