<?php

header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL);
error_reporting(0);

ob_start();

if(phpversion()<5.4 or phpversion()>=7){
	die('Nieprawidlowa wersja PHP na serwerze. Obslugiwane: 5.4 - 5.6');
}

$install = true;

include('../config/db.php');

if(isset($ustawienia['base_url'])){
	header_remove();
	header('location: '.$ustawienia['base_url'].'/cms');
}

if (isset($_POST['url']) and $_POST['url']!='' and isset($_POST['serwer']) and $_POST['serwer']!='' and isset($_POST['port']) and $_POST['port']!='' and isset($_POST['uzytkownik']) and $_POST['uzytkownik']!='' and isset($_POST['nazwa']) and $_POST['nazwa']!='' and isset($_POST['logincms']) and $_POST['logincms']!='' and isset($_POST['haslocms']) and $_POST['haslocms']!='' and isset($_POST['haslocms_powtorz']) and $_POST['haslocms_powtorz']!='' and isset($_POST['email']) and $_POST['email']!='' and isset($_POST['prefiks_tabel']) and isset($_POST['password_salt'])){

	if($_POST['haslocms']!=$_POST['haslocms_powtorz']){
		$error = "Błąd! Wpisane hasła do systemu CMS są różne!";
	}else{
		$connect = mysql_connect($_POST['serwer'].':'.$_POST['port'], $_POST['uzytkownik'], $_POST['haslo']);
		if (!$connect) {
			$error = "Błąd! Nie można połączyć z wybranym serwerem.";
		}else{
			$db_selected = @mysql_select_db($_POST['nazwa']);
			if (!$db_selected) {
				$error = "Błąd! Niewłaściwa nazwa bazy danych.";
			}else{
				$dir = '../config/db.php';
				if (!file_exists($dir) ) {
					fwrite($dir,'');
				}else{
					chmod($dir, 0777);
				}
	 
				$prefiks_tabel = $_POST['prefiks_tabel'];
				
				file_put_contents('../config/db.php', '<?php

$mysql_server = "'.$_POST['serwer'].':'.$_POST['port'].'";
$mysql_user = "'.$_POST['uzytkownik'].'"; 
$mysql_pass = "'.$_POST['haslo'].'"; 
$mysql_db = "'.$_POST['nazwa'].'"; 
@mysql_connect($mysql_server, $mysql_user, $mysql_pass);
mysql_query("SET NAMES utf8");
@mysql_select_db($mysql_db);
mysql_query("SET GLOBAL time_zone = \"Europe/Warsaw\"");
mysql_query("SET session sql_mode = \"NO_ENGINE_SUBSTITUTION\";");

$prefiks_tabel = "'.$prefiks_tabel.'";
$password_salt = "'.$_POST['password_salt'].'";

function pobierz_ustawienia(){
	global $ustawienia, $prefiks_tabel;
	$q = mysql_query(\'select nazwa, wartosc from \'.$prefiks_tabel.\'ustawienia\');
	while($dane = mysql_fetch_assoc($q)){
		$ustawienia[$dane[\'nazwa\']] = htmlspecialchars_decode($dane[\'wartosc\']);
	}
}
pobierz_ustawienia();

?>');		
				mysql_query("CREATE DATABASE IF NOT EXISTS `".$_POST['nazwa']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;");
				mysql_query("SET NAMES utf8");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."aktualnosci` (`id` int(11) NOT NULL  AUTO_INCREMENT,  `tytul` varchar(64) COLLATE utf8_polish_ci NOT NULL,  `prosty_tytul` varchar(64) COLLATE utf8_polish_ci NOT NULL,  `miniaturka` varchar(256) COLLATE utf8_polish_ci NOT NULL,  `krotki_opis` varchar(1024) COLLATE utf8_polish_ci NOT NULL, `tresc` text COLLATE utf8_polish_ci NOT NULL,  `keywords` varchar(1024) COLLATE utf8_polish_ci NOT NULL,  `description` varchar(1024) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."archiwum_ogloszenia` ( `id` int(11) NOT NULL AUTO_INCREMENT,  `aktywna` int(1) NOT NULL,  `oplacona` int(1) NOT NULL,  `oplacona_koszt` decimal(11,2) NOT NULL,  `tytul` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `prosty_tytul` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `id_uzytkownika` int(11) NOT NULL,  `kategoria` int(11) NOT NULL,  `slider` int(1) NOT NULL,  `promowana` int(1) NOT NULL,  `typ` int(11) NOT NULL,  `start` DATETIME NOT NULL,  `koniec` date NOT NULL,  `czas_ogloszenia` int(11) NOT NULL,  `cena` decimal(11,2) NOT NULL,  `cena_do_negocjacji` int(1) NOT NULL, `za_darmo` int(1) NOT NULL, `opis` mediumtext COLLATE utf8_polish_ci NOT NULL,  `stan` int(1) NOT NULL,  `lokalizacja` varchar(256) COLLATE utf8_polish_ci NOT NULL,  `mapa_google` int(1) NOT NULL,  `region` int(11) NOT NULL, `region2` int(11) NOT NULL,  `email` varchar(64) COLLATE utf8_polish_ci NOT NULL,  `telefon` varchar(16) COLLATE utf8_polish_ci NOT NULL, `formularz_kontaktowy` int(1) NOT NULL, `ip` varchar(40) COLLATE utf8_polish_ci NOT NULL, `data` datetime NOT NULL,  `ile_wyswietlen` int(11) NOT NULL,  `opcje_dodatkowe` text COLLATE utf8_polish_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."cms` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `login` varchar(32) COLLATE utf8_polish_ci DEFAULT NULL,  `haslo` varchar(32) COLLATE utf8_polish_ci DEFAULT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=2;");
				mysql_query("INSERT INTO `".$prefiks_tabel."cms` (`id`, `login`, `haslo`) VALUES(1, '".$_POST['logincms']."', md5('".$_POST['haslocms']."'))");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."cms_logi` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `login` varchar(32) COLLATE utf8_polish_ci DEFAULT NULL,  `zalogowal` int(1) DEFAULT NULL,  `data` datetime DEFAULT NULL,  `ip` varchar(40) COLLATE utf8_polish_ci DEFAULT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."czarna_lista` ( `email` varchar(64) COLLATE utf8_polish_ci NOT NULL, `data` datetime NOT NULL,  PRIMARY KEY (`email`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."czas_ogloszen` ( `id` int(11) NOT NULL AUTO_INCREMENT, `dlugosc` int(3) NOT NULL, `cena` decimal(11,2) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."kategorie` (`id` int(11) NOT NULL AUTO_INCREMENT,  `nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `prosta_nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `pozycja` int(11) NOT NULL, `kategoria` int(11) NOT NULL,  `miniaturka` varchar(256) COLLATE utf8_polish_ci NOT NULL,  `ile` int(11) NOT NULL,  `podkategorie` text COLLATE utf8_polish_ci NOT NULL,  `oplata` decimal(11,2) NOT NULL,  `keywords` varchar(1024) COLLATE utf8_polish_ci NOT NULL,  `description` varchar(1024) COLLATE utf8_polish_ci NOT NULL, `glowna_opis` TEXT COLLATE utf8_polish_ci NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."logi_email` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `adresat` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `akcja` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `ip` varchar(40) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."logi_uzytkownicy` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_uzytkownika` int(11) NOT NULL,  `ip` varchar(40) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."logi_wyswietlenia` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `id_ogloszenia` int(11) NOT NULL,  `id_uzytkownika` int(11) NOT NULL,  `ip` varchar(40) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."newsletter` (`email` varchar(32) COLLATE utf8_polish_ci NOT NULL, `aktywny` int(1) NOT NULL, `kod` varchar(32) COLLATE utf8_polish_ci NOT NULL, `ip` varchar(40) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
				mysql_query("ALTER TABLE `".$prefiks_tabel."newsletter`  ADD PRIMARY KEY (`email`);");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."ogloszenia` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `aktywna` int(1) NOT NULL,  `oplacona` int(1) NOT NULL,  `oplacona_koszt` decimal(11,2) NOT NULL,  `tytul` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `prosty_tytul` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `id_uzytkownika` int(11) NOT NULL,  `kategoria` int(11) NOT NULL,  `slider` int(1) NOT NULL,  `promowana` int(1) NOT NULL,  `typ` int(11) NOT NULL,  `start` DATETIME NOT NULL, `koniec` date NOT NULL,  `czas_ogloszenia` int(11) NOT NULL,  `cena` decimal(11,2) NOT NULL,  `cena_do_negocjacji` int(1) NOT NULL, `za_darmo` int(1) NOT NULL, `opis` mediumtext COLLATE utf8_polish_ci NOT NULL,  `stan` int(1) NOT NULL,  `lokalizacja` varchar(256) COLLATE utf8_polish_ci NOT NULL,  `mapa_google` int(1) NOT NULL,  `region` int(11) NOT NULL, `region2` int(11) NOT NULL,  `email` varchar(64) COLLATE utf8_polish_ci NOT NULL,  `telefon` varchar(16) COLLATE utf8_polish_ci NOT NULL, `formularz_kontaktowy` int(1) NOT NULL, `ip` varchar(40) COLLATE utf8_polish_ci NOT NULL, `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."opcje_dodatkowe` ( `id` int(11) NOT NULL AUTO_INCREMENT,  `nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL, `pozycja` int(1) NOT NULL,   `rodzaj` varchar(16) COLLATE utf8_polish_ci NOT NULL,  `wymagane` int(1) NOT NULL,  `kategorie` text COLLATE utf8_polish_ci NOT NULL,  `wszystkie` int(1) NOT NULL,  `opcje_wyboru` varchar(1024) COLLATE utf8_polish_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("INSERT INTO `".$prefiks_tabel."opcje_dodatkowe`(`id`, `nazwa`, `wszystkie`) VALUES ('0','cena','1');");
				mysql_query("UPDATE `".$prefiks_tabel."opcje_dodatkowe` SET `id`=0");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."platnosci_dotpay` ( `id` int(11) NOT NULL AUTO_INCREMENT,  `dotpay_id` varchar(7) COLLATE utf8_polish_ci NOT NULL,  `status_transakcji` varchar(10) COLLATE utf8_polish_ci NOT NULL,  `numer_transakcji` varchar(15) COLLATE utf8_polish_ci NOT NULL,  `status_platnosci` varchar(4) COLLATE utf8_polish_ci NOT NULL,  `id_ogloszenia` int(11) NOT NULL,  `kwota` varchar(10) COLLATE utf8_polish_ci NOT NULL,  `oryginalna_kwota` varchar(14) COLLATE utf8_polish_ci NOT NULL,  `email_kupujacego` varchar(50) COLLATE utf8_polish_ci NOT NULL,  `opis` varchar(255) COLLATE utf8_polish_ci NOT NULL,  `data_transakcji` datetime NOT NULL,  `data_URLC` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."platnosci_paypal` (`id` int(6) NOT NULL AUTO_INCREMENT, `txnid` varchar(20) NOT NULL,  `kwota` decimal(7,2) NOT NULL,  `status` varchar(25) NOT NULL,  `id_ogloszenia` int(11) NOT NULL, `email_kupujacego` VARCHAR(64) NOT NULL, `data` datetime NOT NULL,    PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."platnosci_payu` (`id` int(11) NOT NULL AUTO_INCREMENT,  `id_transakcji` varchar(32) NOT NULL,  `payu_id` int(11) NOT NULL,  `payu_md5` varchar(32) NOT NULL,  `email` varchar(64) NOT NULL,  `id_ogloszenia` int(11) NOT NULL,  `opis` text NOT NULL,  `kwota` varchar(10) NOT NULL,  `status` int(1) NOT NULL,  `data_koniec` datetime NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."platnosci_przelewy24` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `numer_transakcji` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `numer_transakcji_przelewy24` int(11) NOT NULL,  `przelewy24_id` int(11) NOT NULL,  `status_transakcji` varchar(16) COLLATE utf8_polish_ci NOT NULL,  `kwota_oryginalna` varchar(10) COLLATE utf8_polish_ci NOT NULL,  `kwota` varchar(10) COLLATE utf8_polish_ci NOT NULL,  `id_ogloszenia` int(11) NOT NULL,  `opis` text COLLATE utf8_polish_ci NOT NULL,  `email` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `tryb_testowy` int(1) NOT NULL,  `bledy` varchar(256) COLLATE utf8_polish_ci NOT NULL,  `md5sum` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."platnosci_tpay` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `tpay_id` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `status_transakcji` varchar(10) COLLATE utf8_polish_ci NOT NULL,  `kwota` varchar(10) COLLATE utf8_polish_ci NOT NULL,  `oryginalna_kwota` varchar(10) COLLATE utf8_polish_ci NOT NULL,  `id_ogloszenia` int(11) NOT NULL,  `opis` varchar(255) COLLATE utf8_polish_ci NOT NULL,  `znacznik_bledu` varchar(10) COLLATE utf8_polish_ci NOT NULL,  `email` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `md5sum` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `test_mode` int(1) NOT NULL,  `data_transakcji` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."pp_polecenia` (`ip` varchar(40) COLLATE utf8_polish_ci NOT NULL,`id_uzytkownika` int(11) NOT NULL, `data` datetime NOT NULL, PRIMARY KEY (`ip`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."pp_prowizje` (`id_ogloszenia` int(11) NOT NULL, `id_uzytkownika` int(11) NOT NULL, `id_polecajacego` int(11) NOT NULL, `kwota` decimal(11,2) NOT NULL, `procent` int(3) NOT NULL, `prowizja` decimal(11,2) NOT NULL,  `data` datetime NOT NULL, PRIMARY KEY (`id_ogloszenia`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."pp_wyplaty` ( `id` int(11) NOT NULL AUTO_INCREMENT, `id_uzytkownika` int(11) NOT NULL, `status` enum('oczekujacy','zrealizowany','odmowa') COLLATE utf8_polish_ci NOT NULL, `kwota` decimal(11,2) NOT NULL, `data` datetime NOT NULL, `data_realizacji` datetime NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."regiony` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `prosta_nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=17 ;");
			mysql_query("INSERT INTO `".$prefiks_tabel."regiony` (`id`, `nazwa`, `prosta_nazwa`) VALUES
(1, 'Dolnośląskie', 'dolnoslaskie'),
(2, 'Kujawsko-pomorskie', 'kujawsko-pomorskie'),
(3, 'Lubelskie', 'lubelskie'),
(4, 'Lubuskie', 'lubuskie'),
(5, 'Łodzkie', 'lodzkie'),
(6, 'Małopolskie', 'malopolskie'),
(7, 'Mazowieckie', 'mazowieckie'),
(8, 'Opolskie', 'opolskie'),
(9, 'Podkarpackie', 'podkarpackie'),
(10, 'Podlaskie', 'podlaskie'),
(11, 'Pomorskie', 'pomorskie'),
(12, 'Śląskie', 'slaskie'),
(13, 'Świętokrzyskie', 'swietokrzyskie'),
(14, 'Warmińsko-mazurskie', 'warminsko-mazurskie'),
(15, 'Wielkopolskie', 'wielkopolskie'),
(16, 'Zachodniopomorskie', 'zachodniopomorskie');");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."regiony2` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `nazwa` VARCHAR(32) COLLATE utf8_polish_ci NOT NULL , `prosta_nazwa` VARCHAR(32) COLLATE utf8_polish_ci NOT NULL , `region_id` INT(11) NOT NULL , PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."reset_hasla` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `id_uzytkownika` int(11) NOT NULL,  `wykorzystany` int(1) NOT NULL,  `aktywny` int(1) NOT NULL,  `kod` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."schowek` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `id_uzytkownika` int(11) NOT NULL,  `id_ogloszenia` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE `".$prefiks_tabel."sesje_dodawania` ( `id` INT NOT NULL AUTO_INCREMENT , `id_uzytkownika` INT NOT NULL , `kod` VARCHAR(32) NOT NULL , `ip` VARCHAR(40) NOT NULL , `data` DATETIME NOT NULL,  PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."sesje_logowania` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `id_uzytkownika` int(11) NOT NULL,  `kod` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `ip` varchar(40) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."slider` ( `id` int(11) NOT NULL AUTO_INCREMENT, `aktywne` int(1) NOT NULL,  `tresc` text COLLATE utf8_polish_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=8 ;");
				mysql_query("INSERT INTO `".$prefiks_tabel."slider` (`id`, `aktywne`, `tresc`) VALUES(1, 0, ''),(2, 0, ''),(3, 0, ''),(4, 0, ''),(5, 0, ''),(6, 0, ''),(7, 0, '');");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."tresci` (`prosta_nazwa` varchar(64) COLLATE utf8_polish_ci NOT NULL,  `nazwa` varchar(64) COLLATE utf8_polish_ci NOT NULL,  `tresc` text COLLATE utf8_polish_ci NOT NULL,  `keywords` varchar(1024) COLLATE utf8_polish_ci NOT NULL,  `description` varchar(1024) COLLATE utf8_polish_ci NOT NULL, PRIMARY KEY (`prosta_nazwa`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");				
				mysql_query("INSERT INTO `".$prefiks_tabel."tresci` (`prosta_nazwa`, `nazwa`, `tresc`, `keywords`, `description`) VALUES
('regulamin', 'Regulamin', '&lt;ol&gt;\r\n	&lt;li&gt;Korzystanie z serwisu ogłoszeniowego jest r&amp;oacute;wnoznaczne z akceptacją i przestrzeganiem wszystkich punkt&amp;oacute;w niniejszego regulaminu.&lt;/li&gt;\r\n	&lt;li&gt;Przeznaczeniem serwisu jest umożliwienie osobom prywatnym i firmom publikowanie&amp;nbsp;oraz bezpłatne&amp;nbsp;przeglądanie ogłoszeń drobnych za pomocą sieci Internet. Serwis nie bierze udziału w transakcjach dokonywanych przez jego użytkownik&amp;oacute;w, a jego rola ogranicza się jedynie do umożliwienia nawiązania kontaktu między kontrahentami. Serwis nie ponosi jakiejkolwiek odpowiedzialności za treść ogłoszenia, zawartość link&amp;oacute;w podanych w treści ogłoszenia, szkody powstałe w wyniku realizacji transakcji. Użytkownik powinien mieć ograniczone zaufanie do treści i autora ogłoszenia. Użytkownik korzysta z serwisu na własną odpowiedzialność.&lt;/li&gt;\r\n	&lt;li&gt;Z serwisu mogą korzystać tylko osoby pełnoletnie.&lt;/li&gt;\r\n	&lt;li&gt;Aby opublikować ogłoszenie na łamach serwisu należy posiadać działającą skrzynkę e-mail, kt&amp;oacute;rej poprawny adres należy podać przy rejestracji konta.&lt;br /&gt;\r\n	Użytkownik podając dane takie jak adres e-mail, numer telefonu itp. zgadza się i chce żeby zostały opublikowane w serwisie. Użytkownik sam decyduje o celach i środkach przetwarzania ww. danych, użytkownik jest administratorem danych w tym zakresie. Rola serwisu ogranicza się jedynie do umożliwienia przechowania podanych danych w celu udostępnienia ich innym użytkownikom serwisu. Podane dane dostępne są na stronach serwisu do momentu skasowania ogłoszenia, w kt&amp;oacute;rego treści były zawarte.&lt;br /&gt;\r\n	Użytkownik w każdej chwili ma możliwość samodzielnego skasowania swojego ogłoszenia.&lt;/li&gt;\r\n	&lt;li&gt;Serwis ogłoszeniowy zastrzega sobie możliwość rozsyłania komunikat&amp;oacute;w technicznych związanych z jego działaniem na adresy e-mail, kt&amp;oacute;re zostały wykorzystane do publikacji ogłoszeń. Serwis nie wysyła do swoich użytkownik&amp;oacute;w komercyjnych mailing&amp;oacute;w z reklamami produkt&amp;oacute;w lub usług.&lt;br /&gt;\r\n	Serwis zastrzega sobie prawo do publikacji wybranych ogłoszeń w mediach (prasa, radio itp.).&lt;/li&gt;\r\n	&lt;li&gt;Każde ogłoszenie należy przyporządkować do właściwej kategorii. Ogłoszenia komercyjne (pochodzące od firm) należy dodawać tak, aby nie zniechęcać użytkownik&amp;oacute;w do korzystania z serwisu. Treść ogłoszenia powinna być czytela i nie może wprowadzać użytkownik&amp;oacute;w w błąd.&lt;/li&gt;\r\n	&lt;li&gt;Zabrania się zamieszczania ogłoszeń naruszających prawo, dobre obyczaje, zasady moralne przyjęte przez społeczeństwo polskie, uczucia, prawa i dobra osobiste os&amp;oacute;b trzecich, netykietę (np. kilka ogłoszeń o identycznej treści), wątpliwej wartości,&amp;nbsp;reklamujących inne strony www (w szczeg&amp;oacute;lności reklamujących inne serwisy ogłoszeniowe), przedstawiających ofertę sklep&amp;oacute;w, zawierających płatne numery telefon&amp;oacute;w, dodawanych z tymczasowych skrzynek e-mail.&amp;nbsp;&lt;br /&gt;\r\n	W szczeg&amp;oacute;lności zabrania się oferowania:\r\n	&lt;ul&gt;\r\n		&lt;li&gt;pisania prac maturalnych i dyplomowych&lt;/li&gt;\r\n		&lt;li&gt;zarabiania w internecie przez odbieranie emaili, oglądanie reklam&lt;/li&gt;\r\n		&lt;li&gt;zarabiania w internecie przez wypełnianie ankiet&lt;/li&gt;\r\n		&lt;li&gt;zarabiania przez uczestniczenie w piramidach finansowych&lt;/li&gt;\r\n		&lt;li&gt;zarabiania przez uczestniczenie grach hazardowych&lt;/li&gt;\r\n		&lt;li&gt;pracy w agencjach towarzyskich i usług świadczonych przez nie&lt;/li&gt;\r\n		&lt;li&gt;usług i ofert o charakterze erotycznym (spotkania sponsorowane itp.)&lt;/li&gt;\r\n		&lt;li&gt;lek&amp;oacute;w i produkt&amp;oacute;w farmaceutycznych&lt;/li&gt;\r\n		&lt;li&gt;usług ezoterycznych&lt;/li&gt;\r\n		&lt;li&gt;rzeczy pochodzących z kradzieży&lt;/li&gt;\r\n		&lt;li&gt;wyrob&amp;oacute;w tytoniowych (w tym e-papieros&amp;oacute;w) i alkoholowych&lt;/li&gt;\r\n		&lt;li&gt;kopii materiał&amp;oacute;w chronionych prawem autorskim&lt;/li&gt;\r\n		&lt;li&gt;materiał&amp;oacute;w niebezpiecznych dla zdrowia i życia&lt;/li&gt;\r\n		&lt;li&gt;materiał&amp;oacute;w pornograficznych&lt;/li&gt;\r\n		&lt;li&gt;broni (włączając noże, wiatr&amp;oacute;wki, broń pneumatyczną i paralizatory)&lt;/li&gt;\r\n		&lt;li&gt;amunicji i materiał&amp;oacute;w pirotechnicznych&lt;/li&gt;\r\n		&lt;li&gt;narkotyk&amp;oacute;w i innych substancji psychoaktywnych&lt;/li&gt;\r\n	&lt;/ul&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;Zabrania się wykorzystywania podanych przez użytkownik&amp;oacute;w serwisu adres&amp;oacute;w e-mail, numer&amp;oacute;w telefon&amp;oacute;w itd. w celu innym niż wynika to z treści ogłoszenia. W szczeg&amp;oacute;lności zabrania się wysyłania spamu tj. nie zam&amp;oacute;wionych ofert handlowych, reklam serwis&amp;oacute;w internetowych, wirus&amp;oacute;w i trojan&amp;oacute;w itd. Użytkownik serwisu, kt&amp;oacute;ry w odpowiedzi na swoje ogłoszenie otrzymał spampowinien o tym fakcie powiadomić administratora serwisu.&lt;/li&gt;\r\n	&lt;li&gt;Odpowiedzialność za treść ogłoszenia i dołączonych zdjęć ponosi wyłącznie autor ogłoszenia.&lt;br /&gt;\r\n	W szczeg&amp;oacute;lności serwis nie ponosi odpowiedzialności za:\r\n	&lt;ul&gt;\r\n		&lt;li&gt;za to w jaki spos&amp;oacute;b i do jakich cel&amp;oacute;w dane tj. adresy e-mail, numery telefon&amp;oacute;w itp. opublikowane w serwisie przez ogłaszającego, zostaną wykorzystane przez osoby i firmy trzecie. Użytkownik powinien mieć świadomość, że treść każdego opublikowanego ogłoszenia (w tym podane numery telefon&amp;oacute;w lub w spos&amp;oacute;b jawny adresy e-mail) zostanie zindeksowana przez wyszukiwarki internetowe, kt&amp;oacute;re mogą przez pewien czas przechowywać i udostępniać w wynikach wyszukiwań kopię ogłoszenia nawet po usunięciu ogłoszenia z serwisu&lt;/li&gt;\r\n		&lt;li&gt;umieszczenie przez ogłaszającego danych niezgodnych z prawdą lub fałszywych&lt;/li&gt;\r\n	&lt;/ul&gt;\r\n	&lt;/li&gt;\r\n	&lt;li&gt;Konsekwencje prawne wszelkich czynności dokonywanych w ramach niniejszej witryny ponosi wyłącznie użytkownik. Dotyczy to w szczeg&amp;oacute;lności odpowiedzialności za wady fizyczne i prawne oferowanych produkt&amp;oacute;w oraz konsekwencji prawnych publikacji (składania) ofert.&amp;nbsp;&lt;/li&gt;\r\n	&lt;li&gt;Serwis zastrzega sobie prawo do edycji i usuwania ogłoszeń bez podawania przyczyn.&lt;/li&gt;\r\n	&lt;li&gt;Zabrania się kopiowania ogłoszeń (w całości lub części) w celu umieszczenia ich w innym serwisie ogłoszeniowym.&lt;/li&gt;\r\n	&lt;li&gt;Ciasteczka (cookie)&amp;nbsp;- serwis ogłoszeniowy wykorzystuje pliki cookie (ciasteczka). Użytkownik korzystając z serwisu wyraża zgodnę&amp;nbsp;na zapisywanie na swoim terminalu (komputerze, telefonie itp.) plik&amp;oacute;w cookie oraz akceptuje politykę prywatności serwisu ogłoszeniowego.&lt;br /&gt;\r\n	Wsp&amp;oacute;łczesne przeglądarki internetowe umożliwiają zarządzanie plikami cookie, a w szczeg&amp;oacute;lności pozwalają na ich usuwanie lub na całkowite wyłączenie ich obsługi. Przeglądarki internetowe oferują r&amp;oacute;wnież pracę w tzw. &amp;quot;trybie prywatności (incognito)&amp;quot;, w kt&amp;oacute;rym pliki cookie zapisywane są tylko tymczasowo tj. na czas trwania sesji przeglądarki. Nie wyłączenie obsługi plik&amp;oacute;w cookie w przeglądarce internetowej serwis ogłoszeniowy traktuje jako świadomą zgodę użytkownika na zapisywanie plik&amp;oacute;w cookie, co ma&amp;nbsp;umożliwić poprawne działanie serwisu.&lt;/li&gt;\r\n	&lt;li&gt;Zastrzegamy sobie prawo do zmiany regulaminu. Użytkownik powinien systematycznie sprawdzać czy regulamin nie uległ zmianie.&amp;nbsp;&lt;/li&gt;\r\n&lt;/ol&gt;\r\n', '', ''),
('polityka_prywatnosci', 'Polityka prywatności', '&lt;p&gt;Oto nasze stanowisko w sprawie gromadzenia, przetwarzania i wykorzystywania, wprowadzonych przez użytkownik&amp;oacute;w serwisu, adres&amp;oacute;w e-mail i numer&amp;oacute;w telefon&amp;oacute;w.&lt;/p&gt;\r\n\r\n&lt;p&gt;Jaka jest polityka serwisu dotycząca adres&amp;oacute;w e-mail?&lt;br /&gt;\r\nPodany podczas dodawania ogłoszenia adres e-mail służy do weryfikacji osoby zamieszczającej ogłoszenie, oraz jest adresem kontaktowym, na kt&amp;oacute;ry zostają odesłane oferty od os&amp;oacute;b zainteresowanych ogłoszeniem. Serwis przechowuje adresy e-mail, numery telefon&amp;oacute;w itp. związane tylko z aktualnie dostępnymi ogłoszeniami, oraz adresy e-mail os&amp;oacute;b, kt&amp;oacute;re wielokrotnie zamieszczały ogłoszenia niezgodne z regulaminem serwisu, aby uniemożliwić im dodawanie kolejnych ogłoszeń.&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;Czy adresy e-mail i numery telefon&amp;oacute;w są udostępniane innym osobom, lub firmom?&lt;br /&gt;\r\nNie udostępniamy takich danych&amp;nbsp;osobom trzecim lub firmom. Jednak należy mieć na uwadze, że podane w treści ogłoszenia dane (numery telefon&amp;oacute;w, adresy e-mail) mogą zostać &amp;quot;zapamiętane&amp;quot; przez inne osoby lub firmy w okresie, w kt&amp;oacute;rym ogłoszenie jest widoczne w serwisie, w celu p&amp;oacute;źniejszego ich wykorzystania niezgodnie z przeznaczeniem.&lt;/p&gt;\r\n\r\n&lt;p&gt;Ciasteczka (pliki cookie) i sygnalizatory WWW (web beacon)&lt;br /&gt;\r\nZastrzegamy sobie możliwość do wykorzystania plik&amp;oacute;w cookie (ciasteczek) do zapamiętania daty Twoich odwiedzin w naszym serwisie, preferencji w korzystaniu z niego, ustawień itp. Ciasteczka zapisywane są na Twoim komputerze. Większość serwis&amp;oacute;w WWW korzysta z ciasteczek i sygnalizator&amp;oacute;w.&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;Serwis wyświetla reklamy pochodzące od zewnętrznych dostawc&amp;oacute;w. Dostawcy reklam (np. Google) mogą używać ciasteczek i sygnalizator&amp;oacute;w WWW, mogą uzyskać informację o Twoim adresie IP i typie używanej przeglądarki, sprawdzić czy zainstalowany jest dodatek Flash itp. Dzięki ciasteczkom, sygnalizatorom i znajomości adresu IP dostawcy reklam mogą decydować o treści reklam.&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;Przeglądarki internetowe, oraz niekt&amp;oacute;re Firewalle, umożliwiają wyłączenie obsługi ciasteczek i sygnalizator&amp;oacute;w, lub jej ograniczenie dla wszystkich lub tylko dla wybranych stron WWW. Jednak wyłączenie obsługi ciasteczek i sygnalizator&amp;oacute;w może uniemożliwić poprawne działanie naszego serwisu.&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;Wsp&amp;oacute;łczesne przeglądarki umożliwiają przeglądanie stron www tzw. &amp;quot;trybie prywatnym (incognito)&amp;quot; co zazwyczaj oznacza, że wszystkie odwiedzone strony www nie zostaną zapamiętane w lokalnej historii przeglądarki, a pobrane ciasteczka zostaną skasowane po zakończeniu pracy z przeglądarką. Szczeg&amp;oacute;łowy opis &amp;quot;trybu prywatnego&amp;quot; jest dostępny w pomocy przeglądarki.&lt;/p&gt;\r\n', '', ''),
('pomoc', 'Pomoc', '', '', ''),
('program_partnerski', 'Program partnerski', '&lt;ol&gt;
	&lt;li&gt;Każdy zarejestrowany w &lt;strong&gt;Serwisie&lt;/strong&gt;&amp;nbsp;&lt;strong&gt;Użytkownik&lt;/strong&gt; może uczestniczyć w &lt;strong&gt;Programie Partnerskim&lt;/strong&gt; &lt;strong&gt;Serwisu&lt;/strong&gt;.&lt;/li&gt;
	&lt;li&gt;Każdy zarejestrowany &lt;strong&gt;Użytkownik&lt;/strong&gt; posiada własny link referencyjny - tzw. reflink,&amp;nbsp;kt&amp;oacute;ry umożliwia mu promowanie &lt;strong&gt;Serwisu&lt;/strong&gt;. Reflink ten dostępny jest po zalogowaniu, wybraniu zakładki &amp;quot;Moje konto&amp;quot; oraz kliknięciu na link &amp;quot;Program Partnerski&amp;quot;&lt;/li&gt;
	&lt;li&gt;Zarejestrowanie i potwierdzenie rejestracji innej osoby&amp;nbsp;przy wykorzystaniu reflinka danego &lt;strong&gt;Użytkownika&lt;/strong&gt; spowoduje stałe przypisanie jej do tego &lt;strong&gt;Użytkownika&lt;/strong&gt;. Od tej pory jest to &lt;strong&gt;Osoba polecona&lt;/strong&gt; tego&amp;nbsp;&lt;strong&gt;Użytkownika &lt;/strong&gt;a on jest jej&lt;strong&gt; Polecającym&lt;/strong&gt;. Po wybraniu poprzez&amp;nbsp;opisany w punkcie 2&amp;nbsp;spos&amp;oacute;b linku &amp;quot;Program Partnerski&amp;quot;,&amp;nbsp;login &lt;strong&gt;Osoby poleconej&lt;/strong&gt; wraz z datą rejestracji&amp;nbsp;jest widoczny&amp;nbsp;w części &amp;quot;Osoby polecone&amp;quot;. Kliknięcie loginu powoduje wyświetlenie podstawowych wiadomości o &lt;strong&gt;Osobie poleconej&lt;/strong&gt;.&lt;/li&gt;
	&lt;li&gt;Wykupienie kt&amp;oacute;regokolwiek z &lt;strong&gt;Ogłoszeń&lt;/strong&gt; płatnych przez &lt;strong&gt;Osobę poleconą&lt;/strong&gt;&amp;nbsp;powoduje naliczenie &lt;strong&gt;Prowizji&lt;/strong&gt; &lt;strong&gt;Polecającemu&lt;/strong&gt;. Usługodawca zastrzega sobie prawo do zmiany wysokości &lt;strong&gt;Prowizji&lt;/strong&gt;.&lt;/li&gt;
	&lt;li&gt;W momencie gdy&amp;nbsp;&lt;strong&gt;Osoba polecona&lt;/strong&gt;&amp;nbsp;dopłaci do &lt;strong&gt;Ogłoszenia &lt;/strong&gt;płatnego a w międzyczasie nastąpiła&amp;nbsp;zmiana&amp;nbsp;&lt;strong&gt;Prowizji&lt;/strong&gt;&amp;nbsp;(niezależnie czy na mniejszą czy większą) to dla tego &lt;strong&gt;Ogłoszenia &lt;/strong&gt;procent wysokości &lt;strong&gt;Prowizji&lt;/strong&gt;&amp;nbsp;zostanie wyświetlony&amp;nbsp;jako&amp;nbsp;średnia.&amp;nbsp;&lt;/li&gt;
	&lt;li&gt;&lt;strong&gt;Polecający&lt;/strong&gt; może śledzić wszystkie &lt;strong&gt;Ogłoszenia &lt;/strong&gt;wprowadzane&amp;nbsp;przez wszystkie jego&lt;strong&gt; Osoby polecone &lt;/strong&gt;w części widocznej pod &amp;quot;Osoby polecone&amp;quot; a zatytułowanej &amp;quot;Statystyki ogłoszeń&amp;quot;. Domyślnie są wyświetlane statystyki z ostatniego miesiąca, w&amp;nbsp;celu sprawdzenia innego okresu, &lt;strong&gt;Polecający&lt;/strong&gt; musi wpisać odpowiedni zakres dat w polach kalendarza i nacisnąć przycisk &amp;quot;Generuj&amp;quot;.&amp;nbsp;&lt;span style=&quot;font-size: 10pt; line-height: 115%; font-family: Arial, sans-serif; background-image: initial; background-attachment: initial; background-size: initial; background-origin: initial; background-clip: initial; background-position: initial; background-repeat: initial;&quot;&gt;Kliknięcie pozycji danego&lt;span class=&quot;apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;/span&gt;&lt;strong&gt;&lt;span arial=&quot;&quot; style=&quot;font-family:&quot;&gt;Ogłoszenia&amp;nbsp;&lt;/span&gt;&lt;/strong&gt;powoduje jego wyświetlenie.&lt;/li&gt;
	&lt;li&gt;W części &amp;quot;Suma prowizji&amp;quot; widoczne jest podsumowanie &lt;strong&gt;Prowzji&lt;/strong&gt; &lt;strong&gt;Polecającego&lt;/strong&gt; od momentu rejestracji, w poprzednim roku, w bieżącym roku, wypłaconych i do wypłaty.&amp;nbsp;&lt;/li&gt;
	&lt;li&gt;W momencie uzyskania&amp;nbsp;przez&lt;strong&gt; Polecającego&lt;/strong&gt; sumy&lt;strong&gt; Prowizji&lt;/strong&gt; r&amp;oacute;wnej minimalnej kwocie wypłaty w części &amp;quot;Wypłata środk&amp;oacute;w&amp;quot; pojawia się przycisk &amp;quot;Wypłać środki&amp;quot;. Po jego naciśnięciu pojawia się formularz, zawierający niezbędne do dokonania przelewu dane,&amp;nbsp;&lt;span style=&quot;line-height: 20.8px;&quot;&gt;kt&amp;oacute;ry&amp;nbsp;&lt;/span&gt;&lt;strong style=&quot;line-height: 20.8px;&quot;&gt;Polecający&lt;/strong&gt;&lt;span style=&quot;line-height: 20.8px;&quot;&gt;&amp;nbsp;&lt;/span&gt;musi uważnie wypełnić.&lt;/li&gt;
	&lt;li&gt;Po wypełnieniu p&amp;oacute;l i zatwierdzeniu do &lt;strong&gt;Polecającego &lt;/strong&gt;na adres e-mail przypisany do&lt;strong&gt; Konta &lt;/strong&gt;jest wysyłana wiadomość potwierdzająca zlecenie wypłaty.Pozycja z wypłatą&amp;nbsp;staje się widoczna w części &amp;quot;Wypłaty&amp;quot; (niewidocznej do momentu pierwszego zlecenia)&amp;nbsp;i ma status &amp;quot;Zlecona&amp;quot;. Jeśli podane dane są prawidłowe, jej&amp;nbsp;realizacja następuje jak najszybciej.&amp;nbsp;W momencie przelania środk&amp;oacute;w na podane przez &lt;strong&gt;Polecającego&lt;/strong&gt; konto bankowe, status wypłaty zmienia się na &amp;quot;Zrealizowana&amp;quot; a środki dodane do pozycji &amp;quot;Wypłacone&amp;quot; w części &amp;quot;Suma prowzji&amp;quot;.&lt;/li&gt;
	&lt;li&gt;W momencie podania nieprawidłowych danych lub innych niejasności &lt;strong&gt;Usługodawca&lt;/strong&gt; zmieni status wypłaty na &amp;quot;Odmowa&amp;quot; i drogą mailową powiadomi &lt;strong&gt;Użytkownika&lt;/strong&gt; o przyczynie odmowy wypłaty. Jeśli problem polegal jedynie na podaniu złych danych do przelewu, &lt;strong&gt;Użytkownik&lt;/strong&gt; po ponownym zleceniu wypłaty i tym razem prawidłowym podaniu&amp;nbsp;otrzyma środki. Jeśli jednak problem jest bardziej złożony (np. podejrzenie o nieuczciwość), &lt;strong&gt;Usługodawca&lt;/strong&gt; może wymagać od &lt;strong&gt;Polecającego&lt;/strong&gt; dalszych wyjaśnień i w razie ich braku odm&amp;oacute;wić wypłaty.&lt;/li&gt;
	&lt;li&gt;&lt;strong&gt;Użytkownik&lt;/strong&gt; może wypłacić sumę nie mniejszą niż minimalna kwota wypłaty,&amp;nbsp;&lt;span style=&quot;line-height: 20.8px;&quot;&gt;ale nie musi zlecać wypłaty zaraz po jej osiągnięciu.&lt;/span&gt;&lt;/li&gt;
	&lt;li&gt;&lt;strong&gt;Usługodawca &lt;/strong&gt;zastrzega sobie prawo do zmiany minimalnej kwoty wypłaty.&lt;/li&gt;
	&lt;li&gt;Obecnie wypłata środk&amp;oacute;w może nastąpić jedynie na konto w polskim banku.&lt;/li&gt;
&lt;/ol&gt;
', '', ''),
('onas', 'O nas', '', '', ''),
('kontakt', 'Kontakt', '', '', 'Formularz kontaktowy. W razie jakichkolwiek pytań lub wątpliwości zachęcamy do kontaktu z nami!'),
('platnosci', 'Płatności', '', '', '');");				
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."typy_ogloszen` ( `id` int(11) NOT NULL AUTO_INCREMENT, `nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL, `prosta_nazwa` varchar(32) COLLATE utf8_polish_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=7 ;");
				mysql_query("INSERT INTO `".$prefiks_tabel."typy_ogloszen` (`id`, `nazwa`, `prosta_nazwa`) VALUES(1, 'Sprzedam', 'sprzedam'),(2, 'Kupie', 'kupie'),(3, 'Zamienie', 'zamienie'),(4, 'Oddam', 'oddam'),(5, 'Wynajmę', 'wynajme'),(6, 'Usługi', 'uslugi');");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."ustawienia` ( `nazwa` varchar(64) COLLATE utf8_polish_ci NOT NULL, `wartosc` text COLLATE utf8_polish_ci NOT NULL,  UNIQUE KEY `nazwa` (`nazwa`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
				
				$base_url = $_POST['url'];
				if(substr($base_url, 0, 7) != "http://" and substr($base_url, 0, 8) != "https://" ) {
					$base_url = 'http://'.$base_url;
				}
				if(substr($base_url, -1)=='/'){
					$base_url = substr($base_url,0,-1);
				}
				
				$szablon = 'default';
				if (!file_exists('../views/'.$szablon) ) {
					$dirs = array_filter(glob('../views/*'), 'is_dir');
					$szablon = substr($dirs[0],9);
				}
				
				mysql_query("INSERT INTO `".$prefiks_tabel."ustawienia` (`nazwa`, `wartosc`) VALUES
('analytics', ''),
('baner_bottom_1', ''),
('baner_bottom_2', ''),
('baner_panel_1', ''),
('baner_panel_2', ''),
('baner_panel_3', ''),
('baner_panel_4', ''),
('baner_top_1', ''),
('baner_top_2', ''),
('base_url', '".$base_url."'),
('checkout_id', ''),
('checkout_secret', ''),
('czarna_lista_ip',''),
('description', ''),
('dni_do_archiwum', '60'),
('dotpay_id', ''),
('dotpay_nowy_formularz',''),
('domyslny_czas', '30'),
('dotpay_pin', ''),
('dotpay_tryb_testowy', ''),
('dotpay_waluta', 'PLN'),
('email', '".$_POST['email']."'),
('email_koniec_tresc', '&lt;p&gt;Witaj {login}!&lt;/p&gt;&lt;p&gt;Twoje ogłoszenie&lt;strong&gt;&amp;nbsp;{tytul_ogloszenie} &lt;/strong&gt;przestało być aktywne w serwisie &lt;strong&gt;{tytul}&lt;/strong&gt; w dniu&amp;nbsp;&lt;strong&gt;{data}&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;Aby je ponownie aktywować zaloguj się na swoje konto:&amp;nbsp;&lt;strong&gt;&lt;a href=&quot;{base_url}/moje_ogloszenia&quot;&gt;{base_url}/moje_ogloszenia&lt;/a&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;Dziękujemy za zainteresowanie naszym serwisem&lt;/p&gt;&lt;p&gt;&lt;br /&gt;Pozdrawiamy&lt;br /&gt;{tytul}&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;{link_logo}&lt;/p&gt;'),
('email_koniec_tytul', 'Zakończenie wyświetlania ogłoszenia {tytul_ogloszenie} - {tytul}'),
('email_start_tresc', '&lt;p&gt;Witaj {login}!&lt;/p&gt;\r\n\r\n&lt;p&gt;Twoje ogłoszenie&amp;nbsp;&lt;strong&gt;{tytul_ogloszenie}&lt;/strong&gt; zostało poprawnie aktywowane w serwisie &lt;strong&gt;{tytul}&lt;/strong&gt; w dniu&lt;strong&gt;&amp;nbsp;{data}&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Oto link do ogłoszenia:&amp;nbsp;{link_ogloszenie}&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;Dziękujemy za zainteresowanie naszym serwisem&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\nPozdrawiamy&lt;br /&gt;\r\n{tytul}&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;{link_logo}&lt;/p&gt;\r\n'),
('email_start_tytul', 'Aktywacja ogłoszenia {tytul_ogloszenie} - {tytul}'),
('facebook', ''),
('facebook_api', ''),
('facebook_like', '1'),
('facebook_likebox', ''),
('facebook_panel', '1'),
('facebook_secret', ''),
('favicon', '/images/favicon.ico'),
('footer_text','<p>Project &copy; 2015 - 2017 by <a href=\"http://wyremski.pl\" target=\"_blank\" title=\"Tworzenie Stron Internetowych\">Kamil Wyremski</a></p>'),
('glowna_opis', ''),
('google_plus_like', '1'),
('googleplus', ''),
('ile_na_strone', '10'),
('instagram', ''),
('keywords', ''),
('kod_body', ''),
('kod_css', ''),
('kod_head', ''),
('komentowanie_profilu','1'),
('koszt_ogloszenia', '0.00'),
('koszt_promowana', '4.99'),
('koszt_slider', '9.99'),
('koszt_zdjecia', '0.19'),
('limit_zdjec', '10'),
('lk', ''),
('ln', ''),
('logo', '/images/logo.png'),
('logo_facebook', '/images/logo_facebook.png'),
('logo_stopka', '/images/logo.png'),
('logowanie_facebook', ''),
('mail_kontakt_tresc', '&lt;p&gt;Witaj!&lt;br /&gt;\r\nZostała do Ciebie wysłana wiadomość ze strony {base_url}&amp;nbsp;od:&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;table&gt;\r\n	&lt;tbody&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;Imię i nazwisko:&lt;/td&gt;\r\n			&lt;td&gt;&lt;b&gt;{imie}&lt;/b&gt;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;Adres e-mail:&lt;/td&gt;\r\n			&lt;td&gt;&lt;b&gt;{email}&lt;/b&gt;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;Temat:&lt;/td&gt;\r\n			&lt;td&gt;{temat}&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;Wiadomość:&lt;/td&gt;\r\n			&lt;td&gt;{wiadomosc}&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n	&lt;/tbody&gt;\r\n&lt;/table&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;'),
('mail_kontakt_tytul', 'Wiadomość z formularza kontaktowego na {tytul} - {temat}'),
('mail_naduzycie_tresc', '&lt;p&gt;Witaj!&lt;br /&gt;\r\nZostało zgłoszone nadużycie w ogłoszeniu: {link_ogloszenie}&amp;nbsp;przez użytkownika podpisanego adresem email: {email}&lt;/p&gt;\r\n\r\n&lt;p&gt;Treść zgłoszenia: {wiadomosc}&lt;/p&gt;'),
('mail_naduzycie_tytul', 'Nadużycie w ogłoszeniu: {tytul_ogloszenie}'),
('mail_newsletter_potw_tresc', '&lt;p&gt;Witaj!&lt;br /&gt;\r\nAby potwierdzić chęć zapisu do newslettera w serwisie {tytul}&amp;nbsp;kliknij w link: {link_newsletter}&lt;/p&gt;\r\n\r\n&lt;p&gt;Jeśli nie Ty zapisywałeś się do newslettera to zignoruj tą wiadomość.&lt;/p&gt;\r\n\r\n&lt;p&gt;Link potwierdzający jest ważny przez 24 godziny.&lt;/p&gt;\r\n\r\n&lt;p&gt;Pozdrawiamy&lt;br /&gt;\r\nZesp&amp;oacute;ł {tytul}&lt;/p&gt;'),
('mail_newsletter_potw_tytul', 'Potwierdź chęc zapisu do newslettera na {tytul}'),
('mail_ogloszenie_tresc', '&lt;p&gt;Witaj!&lt;br /&gt;\r\nZostała do Ciebie wysłana wiadomość ze strony {tytul}&amp;nbsp;dotycząca ogłoszenia: {link_ogloszenie}&amp;nbsp;od:&lt;/p&gt;\r\n\r\n&lt;table&gt;\r\n	&lt;tbody&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;Imię i nazwisko:&lt;/td&gt;\r\n			&lt;td&gt;&lt;b&gt;{imie}&lt;/b&gt;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;Adres e-mail:&lt;/td&gt;\r\n			&lt;td&gt;&lt;b&gt;{email}&lt;/b&gt;&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr&gt;\r\n			&lt;td&gt;Wiadomość:&lt;/td&gt;\r\n			&lt;td&gt;{wiadomosc}&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n	&lt;/tbody&gt;\r\n&lt;/table&gt;'),
('mail_ogloszenie_tytul', 'Wiadomość z ogłoszenia - {tytul_ogloszenie}'),
('mail_powiadomienie_tresc', '&lt;p&gt;{wiadomosc}&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a alt=&quot;Ogłoszenie: {tytul}&quot; href=&quot;{link_ogloszenie}&quot;&gt;&lt;/a&gt;{link_ogloszenie}&lt;/p&gt;'),
('mail_powiadomienie_tytul', 'Powiadomienie o ogłoszeniu: {tytul_ogloszenie}'),
('mail_pp_wyplata_admin_tresc', '&lt;p&gt;Witaj!&lt;br /&gt;\r\nZostała zgłoszona prośba o wypłatę środk&amp;oacute;w z programu partnerskiego na stronie {base_url]&lt;/p&gt;\r\n\r\n&lt;p&gt;Oto dane użytkownika:&lt;br /&gt;\r\nLogin: {login}&lt;br /&gt;\r\nAdres email: {email}&lt;br /&gt;\r\nImię i nazwisko / nazwa firmy:&amp;nbsp;&lt;span style=&quot;line-height: 20.8px;&quot;&gt;{pp_imie}&lt;/span&gt;&lt;br /&gt;\r\nAdres:&amp;nbsp;&lt;span style=&quot;line-height: 20.8px;&quot;&gt;{pp_adres}&lt;/span&gt;&lt;br /&gt;\r\nNumer konta bankowego: {pp_numer_konta}&lt;br /&gt;\r\nKwota: {kwota}&lt;/p&gt;\r\n\r\n&lt;p&gt;Zaloguj się jako administrator w CMS-ie {base_url}/cms?akcja=pp_wyplaty aby potwierdzić dane.&lt;/p&gt;'),
('mail_pp_wyplata_admin_tytul', 'Prośba o wypłacenie środków - {tytul}'),
('mail_pp_wyplata_user_tresc', '&lt;p&gt;Witaj {login}!&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;Przyjęliśmy Twoją prośbę o wypłatę środk&amp;oacute;w z programu partnerskiego serwisu {base_url}&lt;/p&gt;\r\n\r\n&lt;p&gt;Kwota {kwota}&amp;nbsp;zostanie wypłacona w najbliższym czasie na wskazane przez Ciebie konto bankowe&lt;/p&gt;\r\n\r\n&lt;p&gt;Aby zobaczyć szczeg&amp;oacute;ły zaloguj się w serwisie {base_url}/moj_program_partnerski&lt;/p&gt;\r\n\r\n&lt;p&gt;W razie pytań lub wątpliwości zachęcamy do kontaktu.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;Pozdrawiamy&lt;br /&gt;\r\nZesp&amp;oacute;ł {tytul}&lt;/p&gt;'),
('mail_pp_wyplata_user_tytul', 'Zlecenie wypłaty środków z programu partnerskiego {tytul}'),
('maile_zalaczniki', '1'),
('naglowek', '- skrypt ogłoszeniowy'),
('newsletter', '1'),
('newsletter_panel', '1'),
('nk_like', '1'),
('ocenianie_profilu','1'),
('odswiezanie_dni', '30'),
('odswiezanie_dni_przed', '5'),
('ogloszenia_bez_rejestracji', '0'),
('paypal_email', ''),
('paypal_lokalizacja', 'PL'),
('paypal_tryb_testowy', ''),
('paypal_waluta', 'PLN'),
('payu_id', ''),
('payu_md5', ''),
('platnosc_checkout', ''),
('platnosc_dotpay', ''),
('platnosc_paypal', ''),
('platnosc_payu', ''),
('platnosc_przelewy24', ''),
('platnosc_tpay', ''),
('podkategorie_obowiazkowe','0'),
('pokaz_formularz_kontaktowy', '1'),
('pokaz_formularz_kontaktowy_profil','1'),
('pokaz_gmaps', '1'),
('pole_nowe_uzywane', '1'),
('powiadomienia_z_ogloszen', '1'),
('pp_deaktywacja_po_rejestracji', ''),
('pp_email', ''),
('pp_godzin_dezaktywacja', '24'),
('pp_minimalna_kwota', '0.00'),
('pp_prowizja', '10'),
('program_partnerski', '0'),
('przelewy24_crc', ''),
('przelewy24_id_sklepu', ''),
('przelewy24_id_sprzedawcy', ''),
('przelewy24_tryb_testowy', ''),
('regiony_nazwa', 'Województwo'),
('regiony2_nazwa','Miasto'),
('rejestracja_fb_tresc', '&lt;p&gt;Witaj na stronie&amp;nbsp;&lt;a href=&quot;{base_url}&quot;&gt;{tytul}&lt;/a&gt;!&amp;nbsp;&lt;br /&gt;\r\n&lt;br /&gt;\r\nDziękujemy za rejestrację w naszym serwisie z ogłoszeniami za pomocą konta &lt;a href=&quot;http://facebook.com&quot;&gt;Facebook&lt;/a&gt;.&lt;/p&gt;\r\n\r\n&lt;p&gt;Oto Twoje dane do logowania:&lt;/p&gt;\r\n\r\n&lt;p&gt;Adres email: &lt;strong&gt;{email}&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Login: &lt;strong&gt;{login}&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Wygenerowane hasło (możesz je zmienić po zalogowaniu się na konto):&lt;strong&gt; {haslo}&lt;/strong&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Zaloguj się na stronie: {base_url}&lt;br /&gt;\r\n&lt;br /&gt;\r\nPozdrawiamy&lt;br /&gt;\r\n{tytul}&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\n{link_logo}&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n'),
('rejestracja_fb_tytul', 'Witamy na stronie {tytul}'),
('rejestracja_tresc', '&lt;p&gt;Witaj na stronie &lt;a href=&quot;{base_url}&quot;&gt;{tytul}&lt;/a&gt;!&amp;nbsp;&lt;br /&gt;\r\n&lt;br /&gt;\r\nDziękujemy za rejestrację w naszym serwisie z ogłoszeniami.&lt;br /&gt;\r\n&lt;br /&gt;\r\nŻeby ją dokończyć i potwierdzić sw&amp;oacute;j adres email&amp;nbsp;kliknij w link: &lt;a href=&quot;{link_aktywacyjny}&quot;&gt;{link_aktywacyjny}&lt;/a&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\nInformujemy że link aktywacyjny jest ważny 24 godziny, po tym czasie nieaktywowane konta zostaną usunięte.&lt;br /&gt;\r\nJeśli to nie Ty się rejestrowałeś to zignoruj tą wiadomość.&lt;br /&gt;\r\n&lt;br /&gt;\r\nPozdrawiamy&lt;br /&gt;\r\n{tytul}&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\n{link_logo}&lt;/p&gt;\r\n'),
('rejestracja_tytul', 'Witamy na stronie {tytul}'),
('reset_hasla_tresc', '&lt;p&gt;Witaj {login}!&lt;br /&gt;\r\n&lt;br /&gt;\r\nAby zresetować swoje hasło do serwisu &lt;a href=&quot;{base_url}&quot;&gt;{tytul}&lt;/a&gt; kliknij w następujący link: &lt;a href=&quot;{link_reset}&quot;&gt;{link_reset}&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;Jeśli nie zresetujesz hasła, nadal będziesz m&amp;oacute;gł się logować za pomocą poprzedniego.&lt;/p&gt;\r\n\r\n&lt;p&gt;Link jest ważny przez 24 godziny, po tym czasie zostaje dezaktywowany.&lt;br /&gt;\r\n&lt;br /&gt;\r\nPozdrawiamy&lt;br /&gt;\r\n{tytul}&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;{link_logo}&lt;/p&gt;\r\n'),
('reset_hasla_tytul', 'Reset hasła - {tytul}'),
('rozmiar_zdjec', '2048'),
('rss', '1'),
('slider_dol', '1'),
('smtp', ''),
('smtp_email', ''),
('smtp_haslo', ''),
('smtp_host', ''),
('smtp_uzytkownik', ''),
('stopka_dol', ''),
('stopka_opis', ''),
('szablon', '".$szablon."'),
('title', 'Ogłoszenia NOTICE'),
('tpay_id', ''),
('tpay_kod', ''),
('twitter', ''),
('twitter_like', '1'),
('tytul', 'NOTICE'),
('ukryj_liczbe_wyswietlen',''),
('ukrywaj_email', ''),
('ukrywaj_telefon', ''),
('upload', 'upload'),
('waluta', 'zł'),
('wykop_like', '1'),
('zdjec_bezplatnie', '5'),
('zezwalaj_zdjecia', '1'),
('znak_wodny_url', '/images/znak_wodny.png'),
('znak_wodny_wlacz', '');");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."uzytkownicy` ( `id` int(11) NOT NULL AUTO_INCREMENT,  `login` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `email` varchar(64) COLLATE utf8_polish_ci NOT NULL,  `awatar` varchar(256) COLLATE utf8_polish_ci NOT NULL, `admin` int(1) NOT NULL,  `haslo` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `telefon` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `lokalizacja` varchar(256) COLLATE utf8_polish_ci NOT NULL,  `region` int(11) NOT NULL,   `region2` int(11) NOT NULL,  `dane_faktura` varchar(1024) COLLATE utf8_polish_ci NOT NULL,  `aktywny` int(1) NOT NULL,  `kod_aktywacyjny` varchar(32) COLLATE utf8_polish_ci NOT NULL,  `rejestracja_facebook` int(1) NOT NULL, `o_mnie` text COLLATE utf8_polish_ci NOT NULL, `notatka` varchar(1024) COLLATE utf8_polish_ci NOT NULL,  `pp_polecajacy` int(11) NOT NULL,  `pp_procent` int(2) NOT NULL,  `pp_imie` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `pp_numer_konta` varchar(64) COLLATE utf8_polish_ci NOT NULL,  `pp_adres` varchar(256) COLLATE utf8_polish_ci NOT NULL, `data_aktywacji` datetime NOT NULL,  `ip_aktywacji` varchar(40) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`))  ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1;");
				mysql_query("CREATE TABLE `".$prefiks_tabel."uzytkownicy_oceny` (`id` int(11) NOT NULL AUTO_INCREMENT,`id_profilu` int(11) NOT NULL, `ocena` int(11) NOT NULL, `ip` varchar(40) NOT NULL, `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."wartosci_opcji` (  `ogloszenie_id` int(11) NOT NULL,  `opcja_id` int(11) NOT NULL,  `wartosc` varchar(256) COLLATE utf8_polish_ci NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."zdjecia` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `id_ogloszenia` int(11) NOT NULL,  `miniaturka` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `url` varchar(128) COLLATE utf8_polish_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");
				mysql_query("CREATE TABLE IF NOT EXISTS `".$prefiks_tabel."zdjecia_temp` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `id_uzytkownika` int(11) NOT NULL,  `miniaturka` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `url` varchar(128) COLLATE utf8_polish_ci NOT NULL,  `data` datetime NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=1 ;");

				chmod("../cache", 0777);
				chmod("../cms/cache", 0777);
				chmod("../cms/tmp", 0777);
				chmod("../images", 0777);
				chmod("../upload", 0777);
				chmod("../tmp", 0777);
				chmod("../sitemap.xml", 0777);
				chmod("../config/db.php", 0644);
				
				array_map('unlink', glob("../tmp/*"));
				array_map('unlink', glob("../cms/tmp/*"));
		
				header('location: ../cms');
			}
		}
	}
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
	<meta name="author" content="Kamil Wyremski - wyremski.pl">
	<title>Instalator skryptu</title>
	<link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
	<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="engine.js"></script>
</head>
<body>
<div id="strona">
	<a href="http://wyremski.pl" title="Tworzenie stron www"><img src="../cms/images/cms.png" alt="CMS Kamil Wyremski" id="logo"/></a>
	<h1 style="display:none">Instalator skryptu created by wyremski.pl</h1>
	<h2>Witaj w programie instalacyjnym!<br>Prosimy o wypełnienie poniższych pól<br>aby wstępnie skonfigurować stronę.</h2>
	<?php
		if(isset($error)){
			echo('<h3>'.$error.'</h3>');
		}
	?>
	<form method="post" action="">
		<table>
			<tr>
				<td>Adres URL strony:</td>
				<td><input type="text" name="url" placeholder="Adres URL" value="<?php if(isset($_POST['url'])){echo($_POST['url']);}?>" required title="Tu wpisz adres www witryny"/></td>
			</tr>
			<tr>
				<td>Serwer bazy danych:</td>
				<td><input type="text" name="serwer" placeholder="Serwer mysql" value="<?php if(isset($_POST['serwer'])){echo($_POST['serwer']);}else{echo('localhost');}?>" required title="Tu wpisz adres serwera bazy danych - domyślnie: localhost"/></td>
			</tr>
			<tr>
				<td>Port serwera bazy danych:</td>
				<td><input type="text" name="port" placeholder="Port serwera" value="<?php if(isset($_POST['port'])){echo($_POST['port']);}else{echo('3306');}?>" required title="Tu wpisz port serwera bazy danych - domyślnie: 3306 albo 3307"/></td>
			</tr>
			<tr>
				<td>Nazwa użytkownika bazy danych:</td>
				<td><input type="text" name="uzytkownik" placeholder="Użytkownik" value="<?php if(isset($_POST['uzytkownik'])){echo($_POST['uzytkownik']);}?>" required title="Tu wpisz nazwę użytkownika bazy danych"/></td>
			</tr>
			<tr>
				<td>Nazwa bazy danych:</td>
				<td><input type="text" name="nazwa" placeholder="Nazwa bazy" value="<?php if(isset($_POST['nazwa'])){echo($_POST['nazwa']);}?>" required title="Tu wpisz nazwę bazy danych"/></td>
			</tr>
			<tr>
				<td>Hasło do bazy danych:</td>
				<td><input type="password" name="haslo" placeholder="Hasło do bazy" value="<?php if(isset($_POST['haslo'])){echo($_POST['haslo']);}?>" title="Tu wpisz hasło do bazy danych"/></td>
			</tr>
			<tr>
				<td>Login do systemu CMS:</td>
				<td><input type="text" name="logincms" placeholder="Login do CMS" value="<?php if(isset($_POST['logincms'])){echo($_POST['logincms']);}else{echo('administrator');}?>" required title="Tu wpisz login jakim chcesz się logować do systemu CMS - domyślnie: administrator"/></td>
			</tr>
			<tr>
				<td>Hasło do systemu CMS:</td>
				<td><span class="red">Podane hasła są różne</span><input type="password" name="haslocms" placeholder="Hasło do CMS" value="<?php if(isset($_POST['haslocms'])){echo($_POST['haslocms']);}?>" required pattern="[A-Za-z0-9-_]{3,32}" title="Tutaj hasło do systemu CMS - minimum 3 znaki, możesz użyć cyfr, liter (tylko angielski alfabet) oraz znaków - _" /></td>
			</tr>
			<tr>
				<td>Powtórz hasło do systemu CMS:</td>
				<td><input type="password" name="haslocms_powtorz" placeholder="Hasło do CMS" required pattern="[A-Za-z0-9-_]{3,32}" title="Tutaj wpisz hasło jeszcze raz"/></td>
			</tr>
			<tr>
				<td>Email administratora:</td>
				<td><input type="email" name="email" placeholder="Email administratora" value="<?php if(isset($_POST['email'])){echo($_POST['email']);}?>" title="Tu wpisz adres email administratora serwisu" required/></td>
			</tr>
			<tr>
				<td>Prefiks tabel w bazie danych:</td>
				<td><input type="text" name="prefiks_tabel" placeholder="Prefiks tabel" value="<?php if(isset($_POST['prefiks_tabel'])){echo($_POST['prefiks_tabel']);}?>" title="W przypadku gdy wiele stron korzysta z jednej bazy tutaj możesz ustalić prefiks tabel, w innym przypadku zostaw to pole puste" pattern="[a-z_]*"/></td>
			</tr>
			<tr>
				<td>Sól do haseł w systemie:</td>
				<td><input type="text" name="password_salt" placeholder="Sól do haseł" value="<?php if(isset($_POST['password_salt'])){echo($_POST['password_salt']);}?>" title="Dodatkowe zabezpieczenie haseł użytkowników - wpisz dowolny ciąg znaków"/></td>
			</tr>
		</table>
		<input type="submit" value="Zapisz"/>
	</form>
	<p style="text-align: left">W razie problemów z instalacją zmień uprawnienia poniższych plików i folderów na wartość 0777:
	<br>/cache
	<br>/cms/cache
	<br>/cms/tmp
	<br>/images
	<br>/upload
	<br>/tmp
	<br>/sitemap.xml
	<br>/config/db.php - w tym ostatnim po zakończonej instalacji zmień na 0644</p>
</div>
<br><br><br>
<footer>CMS v3.5 Copyright and project © 2014 - 2016 by <a href="http://wyremski.pl" target="_blank" title="Tworzenie Stron Internetowych">Kamil Wyremski</a>. All rights reserved.</footer>
</body>
</html>
