<?php /* Smarty version 2.6.21, created on 2010-05-26 21:57:46
         compiled from links.tpl */ ?>
		<div class="master">
			<div class="text_chapter">Link</div>
			<?php unset($this->_sections['group']);
$this->_sections['group']['name'] = 'group';
$this->_sections['group']['loop'] = is_array($_loop=$this->_tpl_vars['linkGroup']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['group']['show'] = true;
$this->_sections['group']['max'] = $this->_sections['group']['loop'];
$this->_sections['group']['step'] = 1;
$this->_sections['group']['start'] = $this->_sections['group']['step'] > 0 ? 0 : $this->_sections['group']['loop']-1;
if ($this->_sections['group']['show']) {
    $this->_sections['group']['total'] = $this->_sections['group']['loop'];
    if ($this->_sections['group']['total'] == 0)
        $this->_sections['group']['show'] = false;
} else
    $this->_sections['group']['total'] = 0;
if ($this->_sections['group']['show']):

            for ($this->_sections['group']['index'] = $this->_sections['group']['start'], $this->_sections['group']['iteration'] = 1;
                 $this->_sections['group']['iteration'] <= $this->_sections['group']['total'];
                 $this->_sections['group']['index'] += $this->_sections['group']['step'], $this->_sections['group']['iteration']++):
$this->_sections['group']['rownum'] = $this->_sections['group']['iteration'];
$this->_sections['group']['index_prev'] = $this->_sections['group']['index'] - $this->_sections['group']['step'];
$this->_sections['group']['index_next'] = $this->_sections['group']['index'] + $this->_sections['group']['step'];
$this->_sections['group']['first']      = ($this->_sections['group']['iteration'] == 1);
$this->_sections['group']['last']       = ($this->_sections['group']['iteration'] == $this->_sections['group']['total']);
?>
				<hr class="line" />
				<div class="text_section"><?php echo $this->_tpl_vars['linkGroup'][$this->_sections['group']['index']]; ?>
</div>
				<hr class="line" />
				<div class="text_default">
				<?php unset($this->_sections['body']);
$this->_sections['body']['name'] = 'body';
$this->_sections['body']['loop'] = is_array($_loop=$this->_tpl_vars['link'][$this->_sections['group']['index']]) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['body']['show'] = true;
$this->_sections['body']['max'] = $this->_sections['body']['loop'];
$this->_sections['body']['step'] = 1;
$this->_sections['body']['start'] = $this->_sections['body']['step'] > 0 ? 0 : $this->_sections['body']['loop']-1;
if ($this->_sections['body']['show']) {
    $this->_sections['body']['total'] = $this->_sections['body']['loop'];
    if ($this->_sections['body']['total'] == 0)
        $this->_sections['body']['show'] = false;
} else
    $this->_sections['body']['total'] = 0;
if ($this->_sections['body']['show']):

            for ($this->_sections['body']['index'] = $this->_sections['body']['start'], $this->_sections['body']['iteration'] = 1;
                 $this->_sections['body']['iteration'] <= $this->_sections['body']['total'];
                 $this->_sections['body']['index'] += $this->_sections['body']['step'], $this->_sections['body']['iteration']++):
$this->_sections['body']['rownum'] = $this->_sections['body']['iteration'];
$this->_sections['body']['index_prev'] = $this->_sections['body']['index'] - $this->_sections['body']['step'];
$this->_sections['body']['index_next'] = $this->_sections['body']['index'] + $this->_sections['body']['step'];
$this->_sections['body']['first']      = ($this->_sections['body']['iteration'] == 1);
$this->_sections['body']['last']       = ($this->_sections['body']['iteration'] == $this->_sections['body']['total']);
?>
					<?php echo $this->_tpl_vars['link'][$this->_sections['group']['index']][$this->_sections['body']['index']]; ?>
<br />
				<?php endfor; endif; ?>
				</div>
			<?php endfor; endif; ?>
			<hr class="line" />
			<div class="text_section">文責</div>
			<div class="text_default">Souzen Yurama</div>
			<hr class="line" />
		</div>