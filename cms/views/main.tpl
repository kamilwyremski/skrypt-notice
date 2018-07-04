<!doctype html>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="Keywords" content="CMS, Content Management System, System zarządzania treścią, edycja strony www, tworzenie stron www, strony internetowe">
	<meta name="Description" content="CMS - system zarządzania treścią dla Twojej strony internetowej. Tutaj możesz w łatwy sposób, bez znajomości języka HTML edytować treści Twojej strony internetowej. System stworzony przez: Kamil Wyremski - wyremski.pl">
	<meta name="author" content="Kamil Wyremski - wyremski.pl">
	<title>{$title|default:'CMS created by Kamil Wyremski'}</title>
	
	<link href='https://fonts.googleapis.com/css?family=Lato&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Ubuntu+Condensed&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Oregano&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="views/css/global.css">
	<link rel="stylesheet" type="text/css" href="views/css/slimmenu.css">
	<link rel="stylesheet" type="text/css" href="views/css/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="views/css/style.css">
	<link rel="shortcut icon" href="images/favicon.ico"/>
	
	<script type="text/javascript" src="js/jquery-2.1.4.min.js"></script> 
	<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/jquery.slimmenu.min.js"></script> 
	<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="js/engine_cms.js"></script>
	<script type="text/javascript" src="js/whcookies.js"></script>
</head>
<body>
<aside id="left">
	<div id="top_menu" class="center">
		<a href="http://wyremski.pl" title="Tworzenie stron internetowych" target="_blank"><img src="images/cms.png" alt="Logo" id="logo"/></a><br>
		<a href="?akcja=ustawienia_cms" title="Ustawienia systemu CMS"><span class="ikona ikona_ustawienia"></span>Ustawienia CMS</a><br>
		<a href="?wyloguj" title="Wyloguj z systemu CMS"><span class="ikona ikona_klodka"></span>Wyloguj</a>
	</div>
	<nav id="menu">
		<ul class="slimmenu">
			<li><a href="index.php" title="Home" class="link"><span class="ikona ikona_ok2"></span>HOME</a></li>
			<li><a href="?akcja=statystyki" title="Statystyki" class="link"><span class="ikona ikona_zegarek"></span>STATYSTYKI</a></li>
			<li><a href="?akcja=ogloszenia" title="Ogłoszenia" class="link"><span class="ikona ikona_zawieszka"></span>OGŁOSZENIA</a>
				<ul>
					<li><a href="?akcja=ogloszenia" class="green_menu" title="Wszystkie ogłoszenia">WSZYSTKIE</a></li>
					<li><a href="?akcja=ogloszenia&rodzaj=aktywne" title="Aktywne ogłoszenia">AKTYWNE</a></li>
					<li><a href="?akcja=ogloszenia&rodzaj=nieaktywne" title="Nieaktywne ogłoszenia">NIEAKTYWNE</a></li>
					<li><a href="?akcja=ogloszenia&rodzaj=oplacone" title="Opłacone ogłoszenia">OPŁACONE</a></li>
					<li><a href="?akcja=ogloszenia&rodzaj=nieoplacone" title="Nieopłacone ogłoszenia">NIEOPŁACONE</a></li>
					<li><a href="?akcja=ogloszenia&tabela=archiwum" class="green_menu" title="Archiwum ogłoszeń">ARCHIWUM</a></li>
				</ul>
			</li>
			<li><a href="?akcja=uzytkownicy" title="Użytkownicy" class="link"><span class="ikona ikona_twarz"></span>UŻYTKOWNICY</a></li>
			<li><a href="?akcja=program_partnerski" title="Program partnerski" class="link"><span class="ikona ikona_telefon"></span>PROGRAM PARTNERSKI</a>
				<ul>
					<li><a href="?akcja=program_partnerski" title="Program partnerski">USTAWIENIA PP</a></li>
					<li><a href="?akcja=pp_uzytkownicy" title="Użytkownicy programu partnerskiego">UŻYTKOWNICY</a></li>
					<li><a href="?akcja=pp_prowizje" title="Prowizje z programu partnerskiego">PROWIZJE</a></li>
					<li><a href="?akcja=pp_wyplaty" title="Wypłaty z programu partnerskiego">WYPŁATY</a></li>
				</ul>
			</li>
			<li><span class="link"><span class="ikona ikona_dokument"></span>STRONA</span>
				<ul>
					<li><a href="?akcja=kategorie" title="Kategorie" class="green_menu">KATEGORIE</a></li>
					<li><a href="?akcja=regiony" title="Regiony">REGIONY</a></li>
					<li><a href="?akcja=typy_ogloszen" title="Typy ogłoszeń">TYPY OGŁOSZEŃ</a></li>
					<li><a href="?akcja=opcje_dodatkowe" title="Opcje dodatkowe">OPCJE DODATKOWE</a></li>
					<li><a href="?akcja=tresci" title="Treści">TREŚCI</a></li>
					<li><a href="?akcja=aktualnosci" title="Aktualności na stronie">AKTUALNOŚCI</a></li>
					<li><a href="?akcja=slider" title="Slider">SLIDER</a></li>
					<li><a href="?akcja=banery_reklamowe" title="Banery reklamowe">BANERY REKLAMOWE</a></li>
				</ul>
			</li>
			<li><a href="?akcja=mailing" title="Mailing" class="link"><span class="ikona ikona_olowek"></span>MAILING</a></li>
			<li><a href="?akcja=czarna_lista" title="Czarna lista użytkowników" class="link"><span class="ikona ikona_klodka"></span>CZARNA LISTA</a></li>
			<li><a href="?akcja=ustawienia" title="Ustawienia" class="link"><span class="ikona ikona_ustawienia"></span>USTAWIENIA</a>
				<ul>
					<li><a href="?akcja=ustawienia_maile" title="Ustawienia maili">MAILE</a></li>
					<li><a href="?akcja=ustawienia_wyglad" title="Ustawienie wyglądu">WYGLĄD</a></li>
					<li><a href="?akcja=ustawienia_portale" title="Ustawienia portali społecznościowych">PORTALE SPOŁECZNO.</a></li>
					<li><a href="?akcja=ustawienia_czas" title="Ustawienia czasu ogłoszeń">CZAS OGŁOSZEŃ</a></li>
					<li><a href="?akcja=ustawienia_platnosci" title="Ustawienia płatności">PŁATNOŚCI</a></li>
					<li><a href="?akcja=ustawienia" title="Ustawienia" class="green_menu">OGÓLNE</a></li>
				</ul>
			</li>
			<li><span class="link"><span class="ikona ikona_klodka"></span>LOGI: PŁATNOŚCI</span>
				<ul>
					<li><a href="?akcja=logi_platnosci&rodzaj=payu" title="Logi płatności z PayU">PAYU</a></li>
					<li><a href="?akcja=logi_platnosci&rodzaj=dotpay" title="Logi płatności z Dotpay">DOTPAY</a></li>
					<li><a href="?akcja=logi_platnosci&rodzaj=tpay" title="Logi płatności z TPay">TPAY</a></li>
					<li><a href="?akcja=logi_platnosci&rodzaj=przelewy24" title="Logi płatności z Przelewy24">PRZELEWY24</a></li>
					<li><a href="?akcja=logi_platnosci&rodzaj=paypal" title="Logi płatności z PayPal">PAYPAL</a></li>
				</ul>
			</li>
			<li><span class="link"><span class="ikona ikona_olowek"></span>LOGI</span>
				<ul>
					<li><a href="?akcja=logi_maile" title="Logi maili">MAILE</a></li>
					<li><a href="?akcja=logi_uzytkownicy" title="Logi użytkowników">UŻYTKOWNICY</a></li>
					<li><a href="?akcja=logi_ogloszenia" title="Logi ogłoszeń">OGŁOSZENIA</a></li>
					<li><a href="?akcja=logi_reset_hasla" title="Logi resetu hasła">RESET HASŁA</a></li>
				</ul>
			</li>
		</ul>
	</nav>
	<footer class="center">
		<p>CMS v3.5 Copyright and project © 2015 by <a href="http://wyremski.pl" target="_blank" title="Tworzenie Stron Internetowych">Kamil Wyremski</a></p>
	</footer>
</aside>
<section id="strona">
	{include file="$strona.html"}
</section>
<footer class="center" id="footer_bottom">
	<p>CMS v3.5 Copyright and project © 2015 by <a href="http://wyremski.pl" target="_blank" title="Tworzenie Stron Internetowych">Kamil Wyremski</a></p>
</footer>
</body>
</html>
