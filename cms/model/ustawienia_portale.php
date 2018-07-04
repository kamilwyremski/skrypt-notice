<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_ustawienia_portale' and isset($_POST['facebook']) and isset($_POST['googleplus']) and isset($_POST['instagram']) and isset($_POST['twitter']) and isset($_POST['facebook_api']) and isset($_POST['facebook_secret'])){
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['facebook']).'" where nazwa="facebook" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['googleplus']).'" where nazwa="googleplus" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['instagram']).'" where nazwa="instagram" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['twitter']).'" where nazwa="twitter" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['facebook_like']).'" where nazwa="facebook_like" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['twitter_like']).'" where nazwa="twitter_like" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['google_plus_like']).'" where nazwa="google_plus_like" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['wykop_like']).'" where nazwa="wykop_like" limit 1');	
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['nk_like']).'" where nazwa="nk_like" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['facebook_panel']).'" where nazwa="facebook_panel" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['facebook_likebox']).'" where nazwa="facebook_likebox" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['logowanie_facebook']).'" where nazwa="logowanie_facebook" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['facebook_api']).'" where nazwa="facebook_api" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['facebook_secret']).'" where nazwa="facebook_secret" limit 1');
		pobierz_ustawienia();
	}
	
}else{
	die('Brak dostepu!');
}
