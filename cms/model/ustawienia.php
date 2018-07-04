<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_ustawienia' and isset($_POST['base_url']) and $_POST['base_url']!='' and isset($_POST['email']) and $_POST['email']!='' and isset($_POST['title']) and $_POST['title']!='' and isset($_POST['keywords']) and isset($_POST['description']) and isset($_POST['analytics']) and isset($_POST['rozmiar_zdjec']) and $_POST['rozmiar_zdjec']>0 and isset($_POST['upload']) and $_POST['upload']!='' and isset($_POST['waluta'])){
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.adres_www(filtruj($_POST['base_url'])).'" where nazwa="base_url" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['email']).'" where nazwa="email" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['title']).'" where nazwa="title" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['keywords']).'" where nazwa="keywords" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['description']).'" where nazwa="description" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['analytics']).'" where nazwa="analytics" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['ogloszenia_bez_rejestracji']).'" where nazwa="ogloszenia_bez_rejestracji" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['powiadomienia_z_ogloszen']).'" where nazwa="powiadomienia_z_ogloszen" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['zezwalaj_zdjecia']).'" where nazwa="zezwalaj_zdjecia" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['limit_zdjec']).'" where nazwa="limit_zdjec" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['rozmiar_zdjec']).'" where nazwa="rozmiar_zdjec" limit 1');
		$upload = filtruj($_POST['upload']);
		if(substr($upload, 0, 1) == "/"){
			$upload = substr($upload, 1);
		} 
		if(substr($upload, -1) == "/") {
			$upload = substr($upload, 0, -1);
		}
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.$upload.'" where nazwa="upload" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['waluta']).'" where nazwa="waluta" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['dni_do_archiwum']).'" where nazwa="dni_do_archiwum" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['newsletter']).'" where nazwa="newsletter" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['newsletter_panel']).'" where nazwa="newsletter_panel" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['rss']).'" where nazwa="rss" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['ukrywaj_email']).'" where nazwa="ukrywaj_email" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['ukrywaj_telefon']).'" where nazwa="ukrywaj_telefon" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['komentowanie_profilu']).'" where nazwa="komentowanie_profilu" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['ocenianie_profilu']).'" where nazwa="ocenianie_profilu" limit 1');
		pobierz_ustawienia();
	}
	
}else{
	die('Brak dostepu!');
}
