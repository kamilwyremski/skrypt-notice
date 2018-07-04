{if $ustawienia.baner_bottom_1!=''}
	<div class="center reklama_duza">{$ustawienia.baner_bottom_1}</div>
{/if}
{if isset($slider_dol)}
	<div class="strona padding20 center" id="slider_dol_box">
		<div id="slider_dol">
			{foreach from=$slider_dol item=item key=key}
				<div class="slider_dol inline_block {if $item.promowana}promowana{/if}">
					<a href="{$item.id},{$item.prosty_tytul}" title="Oferta: {$item.tytul}"><img src="{if $item.miniaturka!=''}{$ustawienia.upload}/{$item.miniaturka}{else}{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/brak_obrazka.png{/if}" onerror="this.src='{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/brak_obrazka.png'" alt="Zdjęcie produktu"></a>
					<h4><a href="{$item.id},{$item.prosty_tytul}" title="Ogłoszenie: {$item.tytul}">{$item.tytul|truncate:40}</a></h4>
					{if $item.cena!='0.00'}<p class="cena">{$item.cena|number_format:2:",":" "}&nbsp;{$ustawienia.waluta}</p>{/if}
					{if $item.za_darmo}
						<p class="cena">Za darmo</p>
					{elseif $item.cena!='0.00'}
						<p class="cena">{$item.cena|number_format:2:",":" "}&nbsp;{$ustawienia.waluta}</p>
					{/if}
				</div>
			{/foreach}
		</div>
	</div>
{/if}
<footer class="padding20">
	<div class="strona center">
		<div class="stopka inline_block">
			<a href="{$ustawienia.base_url}" title="{$ustawienia.tytul}">{if $ustawienia.logo_stopka!=''}<img src="{$ustawienia.logo_stopka}" alt="{$ustawienia.tytul}">{else}<h2>{$ustawienia.tytul}</h2>{/if}</a>
		</div>
		<div class="stopka inline_block left">
			{$ustawienia.stopka_opis}
		</div>
		<div class="stopka inline_block left hidden_print">
			<ul>
				<li><a href="{$ustawienia.base_url}" title="Strona główna">Strona główna</a></li>
				<li><a href="rejestracja" title="Zarejestruj się">Rejestracja</a></li>
				<li><a href="logowanie" title="Zaloguj się">Logowanie</a></li>
				<li><a href="reset_hasla" title="Zresetuj hasło w przypadku gdy zapomniałeś">Reset hasła</a></li>
				<li><a href="dodaj" title="Dodaj nowe ogłoszenie">Dodaj ogłoszenie</a></li>
				<li><a href="moje_ogloszenia" title="Zobacz swoje ogłoszenia">Moje ogłoszenia</a></li>
				<li><a href="ustawienia" title="Ustawienia konta">Ustawienia konta</a></li>
				{if $ustawienia.rss}<li><a href="{$ustawienia.base_url}/php/rss.php{if isset($rss_kategoria)}?kategoria={$rss_kategoria.id}&nazwa={$rss_kategoria.nazwa}{/if}" title="Nasz kanał RSS" target="_blank" id="ikona_rss">Kanał RSS</a></li>{/if}
			</ul>
			<ul>
				{if isset($panel_aktualnosci)}<li><a href="aktualnosci" title="Aktualności">Aktualności</a></li>{/if}
				<li><a href="pomoc" title="Pomoc dotycząca serwisu">Pomoc</a></li>
				<li><a href="onas" title="Parę słów o serwisie">O nas</a></li>
				<li><a href="regulamin" title="Regulamin serwisu">Regulamin</a></li>
				<li><a href="polityka_prywatnosci" title="Polityka prywatności serwisu">Polityka prywatności</a></li>
				<li><a href="platnosci" title="Płatności w serwisie">Płatności</a></li>
				{if $ustawienia.program_partnerski}<li><a href="program_partnerski" title="Program partnerski w serwisie">Program partnerski</a></li>{/if}
				<li><a href="kontakt" title="Kontakt z nami">Kontakt</a></li>
			</ul>
		</div>
		<br><br>
		{$ustawienia.stopka_dol}
	</div>
</footer>
{if $ustawienia.baner_bottom_2!=''}
	<div class="center reklama_duza">{$ustawienia.baner_bottom_2}</div>
{/if}
{if $ustawienia.facebook!='' or $ustawienia.facebook_like or $ustawienia.komentowanie_profilu}
	{literal}
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/pl_PL/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	</script>
	{/literal}
	{if $ustawienia.facebook!='' && $ustawienia.facebook_panel}
	<div id="facebook2_2">
		<img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/facebook.png" alt="Facebook" width="33" height="101">  
		<div class="fb-like-box" data-href="{$ustawienia.facebook}" data-width="300" data-height="350" data-show-faces="true" data-stream="true" data-header="false"></div>
	</div>
	{/if}
{/if}
{if $ustawienia.newsletter_panel}
	<div id="newsletter_panel" class="center">
		<img src="{$ustawienia.base_url}/views/{$ustawienia.szablon}/images/newsletter.png" alt="Newsletter" width="33" height="101" class="img"> 
		<div id="newsletter_inside">
			<h2>Newsletter</h2>
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
