<?php /* Smarty version 2.6.21, created on 2011-11-29 23:07:09
         compiled from internal_StarbaseList.tpl */ ?>
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
<?php unset($this->_sections['item']);
$this->_sections['item']['name'] = 'item';
$this->_sections['item']['loop'] = is_array($_loop=$this->_tpl_vars['starbase']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['item']['show'] = true;
$this->_sections['item']['max'] = $this->_sections['item']['loop'];
$this->_sections['item']['step'] = 1;
$this->_sections['item']['start'] = $this->_sections['item']['step'] > 0 ? 0 : $this->_sections['item']['loop']-1;
if ($this->_sections['item']['show']) {
    $this->_sections['item']['total'] = $this->_sections['item']['loop'];
    if ($this->_sections['item']['total'] == 0)
        $this->_sections['item']['show'] = false;
} else
    $this->_sections['item']['total'] = 0;
if ($this->_sections['item']['show']):

            for ($this->_sections['item']['index'] = $this->_sections['item']['start'], $this->_sections['item']['iteration'] = 1;
                 $this->_sections['item']['iteration'] <= $this->_sections['item']['total'];
                 $this->_sections['item']['index'] += $this->_sections['item']['step'], $this->_sections['item']['iteration']++):
$this->_sections['item']['rownum'] = $this->_sections['item']['iteration'];
$this->_sections['item']['index_prev'] = $this->_sections['item']['index'] - $this->_sections['item']['step'];
$this->_sections['item']['index_next'] = $this->_sections['item']['index'] + $this->_sections['item']['step'];
$this->_sections['item']['first']      = ($this->_sections['item']['iteration'] == 1);
$this->_sections['item']['last']       = ($this->_sections['item']['iteration'] == $this->_sections['item']['total']);
?>
					<tr>
						<td rowspan="2">
							<div class="text_table">
								 [ + ] 
							</div>
						</td>
						<td>
							<div class="text_table">
								<a href="./index.php?a=internal_StarbaseDetail&amp;itemID=<?php echo $this->_tpl_vars['starbase'][$this->_sections['item']['index']]['itemID']; ?>
">
									<?php echo $this->_tpl_vars['starbase'][$this->_sections['item']['index']]['location']; ?>
 <?php echo $this->_tpl_vars['starbase'][$this->_sections['item']['index']]['type']; ?>

								</a>
							</div>
						</td>
						<td>
							<div class="text_table">
								<?php echo $this->_tpl_vars['starbase'][$this->_sections['item']['index']]['state']; ?>

							</div>
						</td>
						<td>
							<div class="text_table">
								<?php echo $this->_tpl_vars['starbase'][$this->_sections['item']['index']]['fuel']; ?>

							</div>
						</td>
						<td>
							<div class="text_table">
								<?php echo $this->_tpl_vars['starbase'][$this->_sections['item']['index']]['owner']; ?>

							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="text_table">
								用途: <?php echo $this->_tpl_vars['starbase'][$this->_sections['item']['index']]['purpose']; ?>

							</div>
						</td>
						<td colspan="3">
							<div class="text_table">
								<?php echo $this->_tpl_vars['starbase'][$this->_sections['item']['index']]['alart']; ?>

							</div>
						</td>
					</tr>
<?php endfor; endif; ?>
				</table>
			</div>
			<hr class="line" />
		</div>