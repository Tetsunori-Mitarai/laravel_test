<?php /* Smarty version 2.6.21, created on 2010-05-26 19:56:24
         compiled from auth.tpl */ ?>
		<div class="master">
			<div class="text_section">ログイン</div>
			<hr class="line" />
 
 			<form action="./index.php?a=<?php echo $this->_tpl_vars['page']; ?>
&amp;t=1" method="post">
				<div class="text_default">
					Character Name<br />
					<input name="c" type="text" class="INPUT_TEXT" /><br />
					Password<br />
					<input name="p" type="password" class="INPUT_TEXT" /><br />
					<input type="submit" value="OK" class="INPUT_BUTTON" /><br />
					Passwordを忘れた場合は<a href="./index.php?a=reminder">Reminder</a>へ
				</div>
			</form>
			<hr class="line" />
		</div>