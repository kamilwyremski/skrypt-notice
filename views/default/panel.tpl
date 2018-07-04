	<div id="panel" class="hidden_print">
		{if $ustawienia.baner_panel_1!=''}
			<div class="panel center reklama">
				{$ustawienia.baner_panel_1}
			</div>
		{/if}
		{if isset($kategorie)}
		<div class="panel">
			<nav id="kategorie">
				<h2>
					Kategorie <a href="#" id="pokaz_menu_kategorie" title="Pokaż / ukryj menu"><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/3lines.png" alt="Menu"></a>
					{if isset($menu_kategoria)}<a href="{$menu_kategoria.url}{if $gets!=''}?{$gets}{/if}" title="{$menu_kategoria.nazwa}"><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/wgore.png" alt="W górę"></a>{/if}
					<a href="{$ustawienia.base_url}" title="Strona główna"><img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/home.png" alt="Home"></a>
				</h2>
				<ul>
				{foreach from=$kategorie item=item key=key}
					<li>
						<a href="kategoria,{$item.id},{$item.prosta_nazwa}{if isset($gets) && $gets!=''}?{$gets}{/if}" title="Kategoria: {$item.nazwa}">{if $item.miniaturka!=''}<img src="{$item.miniaturka}" alt="{$item.nazwa}" class="kategoria_miniaturka">{/if}{$item.nazwa}{if !isset($smarty.get.szukaj)}<span>{$item.ile}</span>{/if}</a>
					</li>
				{/foreach}
				</ul>
			</nav>
		</div>
		{/if}
		{if $ustawienia.baner_panel_2!=''}
			<div class="panel center reklama">
				{$ustawienia.baner_panel_2}
			</div>
		{/if}
		{if $ustawienia.facebook_likebox && $ustawienia.facebook!=''}
			<div class="panel panel_facebook">
				<h2>Facebook</h2>
				<div class="fb-like-box" data-href="{$ustawienia.facebook}" data-width="250" data-height="250" data-show-faces="true" data-stream="true" data-header="false"></div>
			</div>
		{/if}
		{if isset($panel_aktualnosci)}
			<div class="panel" id="panel_aktualnosci">
				<h2>Aktualności</h2>
				<nav>
					<ul>
						{foreach from=$panel_aktualnosci item=item key=key}
							<li><a href="aktualnosc,{$item.id},{$item.prosty_tytul}" title="Aktualności: {$item.tytul}">{$item.tytul|truncate:30}<span>{$item.data|date_format:"%d-%m-%Y"}</span></a></li>
						{/foreach}
					</ul>
				</nav>
			</div>
		{/if}
		{if $ustawienia.baner_panel_3!=''}
			<div class="panel center reklama">
				{$ustawienia.baner_panel_3}
			</div>
		{/if}
		{if $ustawienia.newsletter}
			<div class="panel panel_newsletter">
				<h2>Newsletter</h2>
				<div class="newsletter_box center">
					<p>Dzięki zapisaniu się do newslettera będziesz informowany o nowościach i promocjach!</p>
					<form action="" method="post">
						<input type="hidden" name="akcja" value="zapisz_newsletter">
						<input type="email" name="email" title="Tutaj wpisz swój adres email" placeholder="moj-adres@domena.pl" required><br>
						<label><input type="checkbox" required name="regulamin">Akceptuje <a href="regulamin" title="Regulamin serwisu" target="_blank">Regulamin</a> serwisu oraz zapoznałem się z <a href="polityka_prywatnosci" title="Polityka prywatności" target="_blank">Polityką prywatności</a>.</label>
						<input type="submit" value="Zapisz!">
					</form>
				</div>
			</div>
		{/if}		
		{if $ustawienia.baner_panel_4!=''}
			<div class="panel center reklama">
				{$ustawienia.baner_panel_4}
			</div>
		{/if}
	</div>

	