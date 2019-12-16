<?php

if($auth_ok) {
	// assign some content.
	$smarty->assign('menu', $menu_item);
	$smarty->assign('menu_internal', $menu_internal);
	$smarty->assign('page', $page);	
	
	$smarty->display('head.tpl');
	$smarty->display('menu.tpl');
	$smarty->display('menu_internal.tpl');
	$smarty->display('internal.tpl');
	$smarty->display('copyright.tpl');
	$smarty->display('foot.tpl');
	exit;
}else{
	include('./kernel/pages/auth.php');
}

?>