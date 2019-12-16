<?php /* Smarty version 2.6.21, created on 2011-11-26 20:32:26
         compiled from learning.tpl */ ?>
		<div class="INDEX">
			<img src="./img/jads.gif" width="60" height="60" alt="<?php echo $this->_tpl_vars['ticker']; ?>
のロゴマーク" class="CORPLOGO" />
			<?php echo $this->_tpl_vars['corporationName']; ?>

		</div>
 
 		<div class="master">
			<hr class="line" />
			<div class="text_chapter">Database / 資料室</div>
 
			<hr class="line" />
			<div class="text_section">Description / 概要</div>
			<div class="text_default">
				EVEの和訳した資料やガイド、JADSの旧資料を置いています。
				(Sorry, almost of these are written in Japanese language,<br />
				because This section is guidance and help for japanese EVE players.)
			</div>
			<hr class="LINE" />

			<div class="text_section">一覧</div>
			<div class="text_default">
			<?php unset($this->_sections['item']);
$this->_sections['item']['name'] = 'item';
$this->_sections['item']['loop'] = is_array($_loop=$this->_tpl_vars['category']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
				<a href="./index.php?a=learning&cid=<?php echo $this->_tpl_vars['category'][$this->_sections['item']['index']]['id']; ?>
"><?php echo $this->_tpl_vars['category'][$this->_sections['item']['index']]['title']; ?>
</a><br>
			<?php endfor; endif; ?>
			</div>
			<hr class="line" />
 
			<div class="text_section">文責</div>
			<div class="text_default">Souzen Yurama</div>
			<hr class="line" />
		</div>