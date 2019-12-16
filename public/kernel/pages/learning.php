<?php
	GetCorpInfoFromDB($con, &$corp_info);

	$category = array();
	
	//--------------------------------------------
	// カテゴリ一覧表示
	//--------------------------------------------
	if($Arg_CategoryId == '' && $Arg_PageId == '')
	{		
		GetCategoryFromDB($con, &$category);
		
		//--------------------------------------------
		// データベース接続を終了します。
		//--------------------------------------------
		CloseDB($con);
	
		// assign some content.
		$smarty->assign('menu', $menu_item);
		$smarty->assign('page', $page);
		$smarty->assign('ticker', $corp_info['ticker']);
		$smarty->assign('corporationName', $corp_info['corporationName']);
		$smarty->assign('category', $category);
		 
		// display it
		$smarty->display('head.tpl');
		$smarty->display('menu.tpl');
		$smarty->display('learning_category.tpl');
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
	}
	//--------------------------------------------
	// ページ一覧表示
	//--------------------------------------------
	elseif($Arg_PageId == '')
	{
		$contents = array();

		GetContentsListFromDB($con, $Arg_CategoryId, &$category_selected, &$contents);
		
		//--------------------------------------------
		// データベース接続を終了します。
		//--------------------------------------------
		CloseDB($con);
	
		// assign some content.
		$smarty->assign('menu', $menu_item);
		$smarty->assign('page', $page);
		$smarty->assign('ticker', $corp_info['ticker']);
		$smarty->assign('corporationName', $corp_info['corporationName']);
		$smarty->assign('category', $category_selected);
		$smarty->assign('contents', $contents);
		 
		// display it
		$smarty->display('head.tpl');
		$smarty->display('menu.tpl');
		$smarty->display('learning_list.tpl');
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
	}
	//--------------------------------------------
	// ページ一覧表示　＋　ページ内容表示
	//--------------------------------------------
	else
	{
		$contents = array();
	
		GetContentsListFromDB($con, $Arg_CategoryId, &$category_selected, &$contents);
		GetContentsBodyFromDB($con, $Arg_PageId, &$contents_selected);
		
		//--------------------------------------------
		// データベース接続を終了します。
		//--------------------------------------------
		CloseDB($con);
	
		// assign some content.
		$smarty->assign('menu', $menu_item);
		$smarty->assign('page', $page);
		$smarty->assign('ticker', $corp_info['ticker']);
		$smarty->assign('corporationName', $corp_info['corporationName']);
		$smarty->assign('category', $category_selected);
		$smarty->assign('contents', $contents);
		$smarty->assign('contents_selected', $contents_selected);
		 
		// display it
		$smarty->display('head.tpl');
		$smarty->display('menu.tpl');
		$smarty->display('learning_body.tpl');
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
	}
?>