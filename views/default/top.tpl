{if $ustawienia.baner_top_1!=''}
	<div class="center reklama_duza">{$ustawienia.baner_top_1}</div>
{/if}
<div id="top">
	<div class="strona">
		<nav id="social_menu">
			{if $ustawienia.facebook!=''}<a href="{$ustawienia.facebook}" title="Nasza strona na Facebook-u" class="social_facebook" target="_blank"></a>{/if}
			{if $ustawienia.googleplus!=''}<a href="{$ustawienia.googleplus}" title="Nasza strona na Google Plus" class="social_googleplus" target="_blank"></a>{/if}
			{if $ustawienia.instagram!=''}<a href="{$ustawienia.instagram}" title="Nasza strona na Instagram" class="social_instagram" target="_blank"></a>{/if}
			{if $ustawienia.twitter!=''}<a href="{$ustawienia.twitter}" title="Nasza strona na Twitterze" class="social_twitter" target="_blank"></a>{/if}
		</nav>
		<p class="inline_block floatright"><a href="https://wyremski.pl/skrypt/notice" title="Skrypt NOTICE" target="_blank">Skrypt ogłoszeniowy NOTICE 1.9</a></p>
		<div class="clear"></div>
	</div>
</div>
<div id="top_logo" class="padding30">
	<div class="strona">
		<nav id="top_menu">
			{if isset($uzytkownik)}
				<a href="moje_ogloszenia" title="Twoje konto" class="topmenu"><img src="views/{$ustawienia.szablon}/images/ikona_konto.png" alt="Konto">{$uzytkownik.login}</a>
				<a href="?wyloguj" title="Wyloguj z serwisu" class="topmenu"><img src="views/{$ustawienia.szablon}/images/ikona_wyloguj.png" alt="Wyloguj">Wyloguj</a>
			{else}
				<a href="rejestracja" title="Zarejestruj się w naszym serwisie!" class="topmenu"><img src="views/{$ustawienia.szablon}/images/ikona_rejestracja.png" alt="Rejestracja">Rejestracja</a>
				<a href="logowanie" title="Zaloguj się na swoje konto" class="topmenu"><img src="views/{$ustawienia.szablon}/images/ikona_logowanie.png" alt="Logowanie">Logowanie</a>
			{/if}
			<br>
			<a href="dodaj" title="Dodaj ogłoszenie" class="link_banner">+ Dodaj ogłoszenie</a>
		</nav>
		{if $ustawienia.logo!=''}<a href="{$ustawienia.base_url}" title="{$ustawienia.tytul}"><img src="{$ustawienia.logo}" alt="{$ustawienia.tytul}" id="logo"></a>{/if}
		<h1>{$ustawienia.naglowek}</h1>
		<div class="clear"></div>
	</div>
</div>
<nav id="menu">
	<div class="strona">
		<ul>
			<li><a href="{$ustawienia.base_url}" title="{$ustawienia.tytul}">HOME</a></li>
			<li><a href="pomoc" title="Pomoc">POMOC</a></li>
			<li><a href="kontakt" title="Kontakt">KONTAKT</a></li>
			{if isset($panel_aktualnosci)}<li><a href="aktualnosci" title="Aktualności">AKTUALNOŚCI</a></li>{/if}
			<li><a href="dodaj" title="Dodaj ogłoszenie">DODAJ</a></li>
			{if isset($uzytkownik)}
				<li><a href="moje_ogloszenia" title="Moje ogłoszenia" class="border_white">MOJE KONTO</a></li>
			{else}
				<li><a href="rejestracja" title="Zarejestruj się w naszym serwisie!" class="border_white">REJESTRACJA</a></li>
				<li><a href="logowanie" title="Zaloguj się na swoje konto">LOGOWANIE</a></li>
			{/if}
		</ul>
		<form action="{$ustawienia.base_url}" method="get">
			<input type="text" name="szukaj" placeholder="Szukaj..." title="Wpisz hasło które chcesz wyszukać" required {if isset($smarty.get.szukaj)}value="{$smarty.get.szukaj}"{/if}>
			<input type="submit" value="">
		</form>
		<div class="clear"></div>
	</div>
</nav>
<nav id="nawigacja">
	<div class="strona">
		<span>Jesteś tutaj: </span>
		<ul>
			<li><a href="{$ustawienia.base_url}" title="Strona główna">Strona główna</a></li>
			{if isset($nawigacja)}
				{foreach from=$nawigacja item=item key=key}
					<li> -> <a href="{$item.url}" title="{$item.nazwa}">{$item.nazwa|truncate:32|replace:' ':'&nbsp;'}</a></li>
				{/foreach}
			{/if}
		</ul>
	</div>
</nav>
{if $ustawienia.baner_top_2!=''}
	<div class="center reklama_duza">{$ustawienia.baner_top_2}</div>
{/if}
{if isset($infobox)}
	<div class="strona">
		{foreach from=$infobox item=item key=key}
			<div class="infobox {$item.klasa}">
				<a href="#" title="Zamknij" class="zamknij"><img src="views/{$ustawienia.szablon}/images/ikona_zamknij.png" alt="Zamknij"></a>
				<p>{$item.tresc}</p>
			</div>
		{/foreach}
	</div>
{/if}