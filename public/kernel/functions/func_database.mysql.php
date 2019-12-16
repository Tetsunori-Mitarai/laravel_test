<?php
	// ---- ---- ---- ---- ---- ---- ----
	// データベースに接続
	// ---- ---- ---- ---- ---- ---- ----
	function ConnectDB(){
		$con = mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
		
		if($con == 0)
		{
			return($con);
		}
		else
		{
			//データベースを選択
			mysql_select_db(DB_NAME, $con);
			
			//データベースの動作文字コードをUTF-8にセット
			mysql_query("SET NAMES utf8",$con);
			return($con);
		}
	}
	
	function CloseDB($con)
	{
		if($con == 0)
		{
			return(1);
		}
		else
		{
			mysql_close($con);
			return(0);
		}	
	}

//--------------------------------------------
// Menu情報を読み出す
//--------------------------------------------
	function GetMenuFromDB($con, $auth_ok, $menu_item, $menu_internal)
	{
		//--------------------------------------------
		// SQLを実行
		//--------------------------------------------
		$sql = <<<SQL
		SELECT *
		  FROM kernel_menu_main
SQL;
		if(! ($rset = mysql_query($sql, $con)))
		{
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		//--------------------------------------------
		// 結果取り出し
		//--------------------------------------------
		$n_rows = mysql_num_rows($rset);
		$n_cols = mysql_num_fields($rset);
		$i=0;
		$j=0;
		while($j<$n_rows)
		{
			$f_row = mysql_fetch_row($rset); 
			
			if($f_row[2]&&!$f_row[3])
			{
				//--------------------------------------------
				// visibleのフラグが0でfalseの場合且つ
				// protectのフラグが1である場合は読み込まない
				//--------------------------------------------
				$menu_item[$i]['text']= $f_row[4];
				$menu_item[$i]['url'] = $f_row[5];
				$menu_item[$i]['alt'] = $f_row[6];
				$i++;
			}
			
			$j++;
		}
		
		$sql = "SELECT * FROM kernel_menu_internal";
		if(! ($rset = mysql_query($sql, $con)))
		{
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		//--------------------------------------------
		// 結果取り出し
		//--------------------------------------------
		$n_rows = mysql_num_rows($rset);
		$n_cols = mysql_num_fields($rset);
		$i=0;
		$j=0;
		while($j<$n_rows)
		{
			$f_row = mysql_fetch_row($rset); 
			//--------------------------------------------
			// 認証していない場合のメニュー読み込み
			//--------------------------------------------
			if(!$auth_ok&&$f_row[2]&&!$f_row[3])
			{
				//visibleのフラグが0でfalseの場合且つprotectのフラグが1である場合は読み込まない
				$menu_internal[$i]['text']= $f_row[4];
				$menu_internal[$i]['url'] = $f_row[5];
				$menu_internal[$i]['alt'] = $f_row[6];
				$i++;
			}
			
			//--------------------------------------------
			// 認証している場合のメニュー読み込み
			//--------------------------------------------
			if($auth_ok&&$f_row[2])
			{
				//visibleのフラグが0でfalseの場合は読み込まない
				$menu_internal[$i]['text']= $f_row[4];
				$menu_internal[$i]['url'] = $f_row[5];
				$menu_internal[$i]['alt'] = $f_row[6];
				$i++;
			}
			
			$j++;
		}
		
		//--------------------------------------------
		// MySQLの結果集合を開放します。
		//--------------------------------------------
		mysql_freeresult($rset);
		
		return;
	}


//--------------------------------------------
// Corporation情報を読み出す
//--------------------------------------------
	function GetCorpInfoFromDB($con, $corp_info){
		//--------------------------------------------
		// SQLを実行
		//--------------------------------------------
		$sql = <<<SQL
		SELECT *
		  FROM kernel_api_corp_cache
		 WHERE corporationID='633195178'
SQL;
		if(! ($rset = mysql_query($sql, $con)))
		{
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		//--------------------------------------------
		// 結果取り出し
		//--------------------------------------------
		$n_rows = mysql_num_rows($rset);
		for($i=0; $i<$n_rows; $i++)
		{ 
			$f_row = mysql_fetch_row($rset);
			$corp_info['corporationID'] = $f_row[2];
			$corp_info['corporationName'] = $f_row[3];
			$corp_info['ticker'] = $f_row[4];
			$corp_info['ceoID'] = $f_row[5];
			$corp_info['ceoName'] = $f_row[6];
			$corp_info['stationID'] = $f_row[7];
			$corp_info['stationName'] = $f_row[8];
			$corp_info['description'] = $f_row[9];
			$corp_info['url'] = $f_row[10];
			$corp_info['allianceID'] = $f_row[11];
			$corp_info['allianceName'] = $f_row[12];
			$corp_info['taxRate'] = $f_row[13];
			$corp_info['memberCount'] = $f_row[14];
			$corp_info['shares'] = $f_row[15];
			$corp_info['graphicID'] = $f_row[16];
			$corp_info['shape1'] = $f_row[17];
			$corp_info['shape2'] = $f_row[18];
			$corp_info['shape3'] = $f_row[19];
			$corp_info['color1'] = $f_row[20];
			$corp_info['color2'] = $f_row[21];
			$corp_info['color3'] = $f_row[22];
		}
		
		//--------------------------------------------
		// MySQLの結果集合を開放します。
		//--------------------------------------------
		mysql_freeresult($rset);
		
		return;
	}
	
//--------------------------------------------
// 資料ページのカテゴリ一覧を読み出す
//--------------------------------------------
	function GetCategoryFromDB($con, $category){
		//--------------------------------------------
		// SQLを実行
		//--------------------------------------------
		$sql = <<<SQL
		SELECT id, title
		FROM kernel_learning_index
		WHERE visible=2
SQL;
		if(! ($rset = mysql_query($sql, $con)))
		{
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		//--------------------------------------------
		// 結果取り出し
		//--------------------------------------------
		$n_rows = mysql_num_rows($rset);
		for($i=0; $i<$n_rows; $i++)
		{  
			$f_row = mysql_fetch_row($rset);
			$category[$i]['id'] = $f_row[0];
			$category[$i]['title'] = $f_row[1];
		}
		
		//--------------------------------------------
		// MySQLの結果集合を開放します。
		//--------------------------------------------
		mysql_freeresult($rset);
		
		return;
	}

//--------------------------------------------
// 資料ページのタイトル一覧を読み出す
//--------------------------------------------
	function GetContentsListFromDB($con, $Arg_CategoryId, $category_selected, $contents){
		//--------------------------------------------
		// SQLインジェクション対策
		//--------------------------------------------
		$Arg_CategoryId_secure = mysql_real_escape_string($Arg_CategoryId);

		//--------------------------------------------
		// SQLを実行
		//--------------------------------------------
		$sql = <<<SQL
		SELECT id, title
		FROM kernel_learning_index
		WHERE id=$Arg_CategoryId_secure AND visible=2
SQL;
		if(! ($rset = mysql_query($sql, $con)))
		{
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		//--------------------------------------------
		// 結果取り出し
		//--------------------------------------------
		$n_rows = mysql_num_rows($rset);
		if($n_rows == 1)
		{  
			$f_row = mysql_fetch_row($rset);
			$category_selected['id'] = $f_row[0];
			$category_selected['title'] = $f_row[1];
		}

		//--------------------------------------------
		// SQLを実行
		//--------------------------------------------		
		$sql = <<<SQL
		SELECT id, title
		FROM kernel_learning_contents
		WHERE id_category=$Arg_CategoryId_secure AND visible=2
SQL;
		if(! ($rset = mysql_query($sql, $con)))
		{
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		//--------------------------------------------
		// 結果取り出し
		//--------------------------------------------
		$n_rows = mysql_num_rows($rset);
		for($i=0; $i<$n_rows; $i++)
		{  
			$f_row = mysql_fetch_row($rset);
			$contents[$i]['id'] = $f_row[0];
			$contents[$i]['title'] = $f_row[1];
		}
		
		//--------------------------------------------
		// MySQLの結果集合を開放します。
		//--------------------------------------------
		mysql_freeresult($rset);
		
		return;
	}
	
//--------------------------------------------
// 資料ページの個別記事を読み出す
//--------------------------------------------
	function GetContentsBodyFromDB($con, $Arg_PageId, $contents_selected){
		//--------------------------------------------
		// SQLインジェクション対策
		//--------------------------------------------
		$Arg_PageId_secure = mysql_real_escape_string($Arg_PageId);
		
		//--------------------------------------------
		// SQLを実行
		//--------------------------------------------
		
		$sql = <<<SQL
		SELECT id, title, body
		FROM kernel_learning_contents
		WHERE id=$Arg_PageId_secure AND visible=2
SQL;
		if(! ($rset = mysql_query($sql, $con)))
		{
			putError("実行に失敗しました：" . mysql_error());
			return;
		}
		//--------------------------------------------
		// 結果取り出し
		//--------------------------------------------
		$n_rows = mysql_num_rows($rset);
		if($n_rows == 1)
		{  
			$f_row = mysql_fetch_row($rset);
			$contents_selected['id'] = $f_row[0];
			$contents_selected['title'] = $f_row[1];
			$contents_selected['body'] = $f_row[2];
		}
		
		//--------------------------------------------
		// MySQLの結果集合を開放します。
		//--------------------------------------------
		mysql_freeresult($rset);
		
		return;
	}

//--------------------------------------------
// エラー関数
// MySQLのエラーはファイル書き出しにする
//--------------------------------------------
	function putError($msg)
	{
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
?>