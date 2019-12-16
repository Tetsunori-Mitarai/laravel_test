<!-- menu.tpl -->
<!--DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"-->

<!-- if you make custumizing for EVE BROWSER, please replace these lines to UTF-8 and XML1.1 -->

<html Dir="LTR" Lang="ja">
	<head>
		<meta name="robots" content="noarchive">
		<meta http-equiv="Content-Type" content="text/html"; charset="UTF-8">
		<!--link rel="shortcut icon" href="./img/manual.ico"-->
		<link rel="stylesheet" type="text/css" href="./css/default.css" />
		<title>Sylph Alliance</title>
	</head>
	<body>
		<table class="TABLE" width="800pixel" align="center">
			<tr>
				<td><img src="./img/sylph.jpg" width="640pixel" height="125pixel" /></td>
				<td>
					<table class="TEXT">
						<tr>
						<td width="100"><b>Charactor:</b></td>
						<td>{if $user_name}$user_name}{/if}</td>
						</tr>
						<tr>
						<td width="100"><b>Messages:</b></td>
						<td>{if $msg}$msg{/if}</td>
						</tr>
						<tr>
						<td width="100">Summary</td>
						<td>Settings</td>
						</tr>
						<tr>
						<td width="100" colspan="2">Logout</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div class="CENTER">
			<table class="TABLE">