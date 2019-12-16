		<div class="master">
			<ul class="MENU" id="MAIN_MENU">
{section name=item loop=$menu}
				<li><a href="{$menu[item].url}">{$menu[item].text}</a></li>
{/section}
			</ul>
			<hr class="line" />
		</div>

