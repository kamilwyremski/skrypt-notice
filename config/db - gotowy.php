<?php

$mysql_server = ""; // serwer bazy danych (jeśli niestandardowy port dopisz :numer_portu
$mysql_user = "";   // nazwa użytkownika w bazie danych
$mysql_pass = "";   // hasło do bazy danych
$mysql_db = ""; 	// nazwa bazy danych
@mysql_connect($mysql_server, $mysql_user, $mysql_pass);
mysql_query("SET NAMES utf8");
@mysql_select_db($mysql_db);
mysql_query('SET GLOBAL time_zone = "Europe/Warsaw"');
mysql_query("SET session sql_mode = 'NO_ENGINE_SUBSTITUTION';");

$prefiks_tabel = ''; // prefiks tabel w bazie danych
$password_salt = ''; // sól do haseł użytkowników serwisu

function pobierz_ustawienia(){
	global $ustawienia, $prefiks_tabel;
	$q = mysql_query('select nazwa, wartosc from '.$prefiks_tabel.'ustawienia');
	while($dane = mysql_fetch_assoc($q)){
		$ustawienia[$dane['nazwa']] = htmlspecialchars_decode($dane['wartosc']);
	}
}
pobierz_ustawienia();

