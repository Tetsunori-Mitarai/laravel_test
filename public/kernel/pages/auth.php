<?php

	// assign some content.
	$smarty->assign('menu', $menu_item);
	$smarty->assign('menu_internal', $menu_internal);
	$smarty->assign('page', $page);	
	
	$smarty->display('head.tpl');
	$smarty->display('menu.tpl');
	$smarty->display('menu_internal.tpl');
	$smarty->display('auth.tpl');
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
	
	exit;

?>