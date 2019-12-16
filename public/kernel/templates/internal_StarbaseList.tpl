		<div class="master">
			<div class="text_chapter">社有スターベースリスト</div>
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
				<table class="table_standard">
					<tr>
						<td>
							<div class="text_table">Type</div>
						</td>
						<td>
							<div class="text_table">Location</div>
						</td>
						<td>
							<div class="text_table">Status</div>
						</td>
						<td>
							<div class="text_table">Fuel</div>
						</td>
						<td>
							<div class="text_table">Owner</div>
						</td>
					</tr>
{section name=item loop=$starbase}
					<tr>
						<td rowspan="2">
							<div class="text_table">
								 [ + ] 
							</div>
						</td>
						<td>
							<div class="text_table">
								<a href="./index.php?a=internal_StarbaseDetail&amp;itemID={$starbase[item].itemID}">
									{$starbase[item].location} {$starbase[item].type}
								</a>
							</div>
						</td>
						<td>
							<div class="text_table">
								{$starbase[item].state}
							</div>
						</td>
						<td>
							<div class="text_table">
								{$starbase[item].fuel}
							</div>
						</td>
						<td>
							<div class="text_table">
								{$starbase[item].owner}
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="text_table">
								用途: {$starbase[item].purpose}
							</div>
						</td>
						<td colspan="3">
							<div class="text_table">
								{$starbase[item].alart}
							</div>
						</td>
					</tr>
{/section}
				</table>
			</div>
			<hr class="line" />
		</div>