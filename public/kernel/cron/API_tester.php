<?php
	require_once "HTTP/Request.php";

	$url = 'http://api.eve-online.com/corp/StarbaseList.xml.aspx';
	$request = new HTTP_Request($url);
	$request->addQueryString('userID', 3149457);
	$request->addQueryString('characterID', 1877244770);
	$request->addQueryString('apiKey', "518283E8155044BB9AA5DEDEF837A95FEF0C2176D45946C3911298C120BF2B3D");
	$request->sendRequest();
	$xml = new SimpleXMLElement($request->getResponseBody());
	$time = $xml->currentTime;
	
	//XML出力中のerror codeは$xml->error[0]['code']で取得できる
	//これがnullじゃない場合はエラーが出ていると判断する
	print("Target:".$url."<br>Result:<br>".$time."<br>XML:<br>".$xml->result[0]->rowset[0]->row[0]['itemID']);
?>