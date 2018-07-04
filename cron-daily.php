<?php

error_reporting(E_ALL);
error_reporting(0);

include(realpath(dirname(__FILE__)).'/config/db.php');
include(realpath(dirname(__FILE__)).'/php/funkcje.php');
include(realpath(dirname(__FILE__)).'/php/globalne.php');
include(realpath(dirname(__FILE__)).'/php/maile.php');

function cron_daily(){
	global $ustawienia, $prefiks_tabel;

	mysql_query('update '.$prefiks_tabel.'reset_hasla set aktywny = 0 where aktywny = 1 and data < (NOW() - INTERVAL 1 DAY)');
	
	mysql_query('delete from '.$prefiks_tabel.'uzytkownicy where aktywny = "0" and data<(CURDATE() - INTERVAL 1 DAY)');
	
	mysql_query('delete from '.$prefiks_tabel.'newsletter where aktywny = "0" and data<(CURDATE() - INTERVAL 1 DAY)');
	
	$q = mysql_query('select miniaturka, url from '.$prefiks_tabel.'zdjecia_temp where data<(CURDATE() - INTERVAL 1 DAY)');
	while($dane = mysql_fetch_assoc($q)){
		unlink(realpath(dirname(__FILE__)).'/'.$ustawienia['upload'].'/'.$dane['miniaturka']);
		unlink(realpath(dirname(__FILE__)).'/'.$ustawienia['upload'].'/'.$dane['url']);
	}
	mysql_query('delete from '.$prefiks_tabel.'zdjecia_temp where data<(CURDATE() - INTERVAL 1 DAY)');
		
	$q = mysql_query('select id from '.$prefiks_tabel.'ogloszenia where aktywna=1 and koniec<(CURDATE())');
	while($dane = mysql_fetch_assoc($q)){
		wyslij_mail('koniec','',array('id'=>$dane['id']));
		mysql_query('update '.$prefiks_tabel.'ogloszenia set aktywna=0, oplacona=0, oplacona_koszt=0 where id="'.$dane['id'].'" limit 1');
	}
	
	$q = mysql_query('select id from '.$prefiks_tabel.'ogloszenia where koniec<(CURDATE() - INTERVAL '.$ustawienia['dni_do_archiwum'].' DAY)');
	while($dane = mysql_fetch_assoc($q)){
		ogloszenie_do_archiwum($dane['id']);
	}
	
	mysql_query('delete from '.$prefiks_tabel.'sesje_logowania where data<(CURDATE() - INTERVAL 1 DAY)');
	
	mysql_query('delete from '.$prefiks_tabel.'sesje_dodawania where data<(CURDATE() - INTERVAL 1 DAY)');
	
	mysql_query('delete from '.$prefiks_tabel.'pp_polecenia where data<(CURDATE() - INTERVAL '.$ustawienia['pp_godzin_dezaktywacja'].' HOUR)');

	policz_podkategorie();
	
}
cron_daily();

include('php/sitemap_generator.php');

