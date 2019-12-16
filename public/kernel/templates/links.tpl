		<div class="master">
			<div class="text_chapter">Link</div>
			{section name=group loop=$linkGroup}
				<hr class="line" />
				<div class="text_section">{$linkGroup[group]}</div>
				<hr class="line" />
				<div class="text_default">
				{section name=body loop=$link[group]}
					{$link[group][body]}<br />
				{/section}
				</div>
			{/section}
			<hr class="line" />
			<div class="text_section">文責</div>
			<div class="text_default">Souzen Yurama</div>
			<hr class="line" />
		</div>