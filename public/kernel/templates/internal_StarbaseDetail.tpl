		<div class="master">
			<div class="text_chapter">社有スターベース詳細</div>
			<hr class="line" />
			<div id="MENU_SIDE">
				<div class="text_default">
	 				<a href="./index.php?a=internal_StarbaseList">スターベースリスト</a>
	 			</div>
				<div class="text_default">
	 				<!-- a href="./index.php?a=internal_WalletViewer" -->財務/会計システム<br />（開発中&hearts;）
	 			</div>
				<div class="text_default">
	 				<!-- a href="./index.php?a=internal_WalletViewer" -->作戦解説システム<br />（開発中&hearts;）
	 			</div>
	 		</div>
			<div id="CONTENTS">
				<div class="text_section">場所と種類</div>
				<div class="text_default">{$starbase.location} {$starbase.type}</div>
			
				<div class="text_section">稼動状態</div>
				<div class="text_default">
					{$starbase.state}
				</div>
				<div class="text_section">
					燃料の状況
				</div>
				<table class="table_standard">
					<tr>
						<td><div class="text_table">燃料種別</div></td>
						<td><div class="text_table">燃料名</div></td>
						<td><div class="text_table">残量</div></td>
						<td><div class="text_table">消費量</div></td>
						<td><div class="text_table">稼動可能日数</div></td>
					</tr>
					<tr>
						<td rowspan="6"><div class="text_table">ONLINE</div></td>
						<td><div class="text_table">{$starbase_fuel[0].typeName}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[0].qty}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[0].req}</div></td>
						<td><div class="text_table">{$starbase_fuel[0].limit}</div></td>
					</tr>
					<tr>
						<td><div class="text_table">{$starbase_fuel[1].typeName}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[1].qty}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[1].req}</div></td>
						<td><div class="text_table">{$starbase_fuel[1].limit}</div></td>
					</tr>
					<tr>
						<td><div class="text_table">{$starbase_fuel[2].typeName}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[2].qty}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[2].req}</div></td>
						<td><div class="text_table">{$starbase_fuel[2].limit}</div></td>
					</tr>
					<tr>
						<td><div class="text_table">{$starbase_fuel[3].typeName}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[3].qty}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[3].req}</div></td>
						<td><div class="text_table">{$starbase_fuel[3].limit}</div></td>
					</tr>
					<tr>
						<td><div class="text_table">{$starbase_fuel[4].typeName}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[4].qty}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[4].req}</div></td>
						<td><div class="text_table">{$starbase_fuel[4].limit}</div></td>
					</tr>
					<tr>
						<td><div class="text_table">{$starbase_fuel[5].typeName}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[5].qty}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[5].req}</div></td>
						<td><div class="text_table">{$starbase_fuel[5].limit}</div></td>
					</tr>
					<tr>
						<td><div class="text_table">POWERGRID</div></td>
						<td><div class="text_table">{$starbase_fuel[6].typeName}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[6].qty}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[6].req}</div></td>
						<td><div class="text_table">{$starbase_fuel[6].limit}</div></td>
					</tr>
					<tr>
						<td><div class="text_table">CPU</div></td>
						<td><div class="text_table">{$starbase_fuel[7].typeName}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[7].qty}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[7].req}</div></td>
						<td><div class="text_table">{$starbase_fuel[7].limit}</div></td>
					</tr>
					<tr>
						<td><div class="text_table">REINFORCED</div></td>
						<td><div class="text_table">{$starbase_fuel[8].typeName}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[8].qty}</div></td>
						<td><div class="text_align_right">{$starbase_fuel[8].req}</div></td>
						<td><div class="text_table">{$starbase_fuel[8].limit}</div></td>
					</tr>
				</table>
				
				<div class="text_section">
					防空設定 *診断する
				</div>
				<div class="text_default">
					[Yes]Standingが{$starbase.onStandingDrop}以下のターゲットが接近したら射撃開始<br />
	      			[Yes]SecurityStatusが{$starbase.onStatusDrop}以下のターゲットが接近したら射撃開始<br />
	      			[Yes]アグレッションが発生したら射撃開始<br />
	      			[Yes]戦争相手を発見したら射撃開始<br />
				</div>
	
				<div class="text_section">
					権限設定 *診断する
				</div>
				<div class="text_default">
					[Yes]設備の使用権限<br />
	      			[Yes]設備の設置/撤収権限<br />
	      			[Yes]Corporationの艦艇はフォースフィールド内に駐留可能<br />
	      			[Yes]Allianceの艦艇はフォースフィールド内に駐留可能
				</div>
			</div>
			<hr class="line" />
		</div>