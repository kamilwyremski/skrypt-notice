<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_ustawienia_wyglad' and isset($_POST['szablon']) and $_POST['szablon']!='' and isset($_POST['tytul']) and isset($_POST['logo']) and isset($_POST['logo_stopka']) and isset($_POST['logo_facebook']) and isset($_POST['favicon']) and isset($_POST['naglowek']) and isset($_POST['ile_na_strone']) and $_POST['ile_na_strone']>0 and isset($_POST['stopka_opis']) and isset($_POST['stopka_dol']) and isset($_POST['kod_css']) and isset($_POST['kod_head']) and isset($_POST['kod_body'])){

		if($ustawienia['szablon'] != $_POST['szablon']){
			array_map('unlink', glob("../tmp/*")); 
		}
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['szablon']).'" where nazwa="szablon" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['tytul']).'" where nazwa="tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['logo']).'" where nazwa="logo" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['logo_stopka']).'" where nazwa="logo_stopka" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['logo_facebook']).'" where nazwa="logo_facebook" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['favicon']).'" where nazwa="favicon" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['znak_wodny_wlacz']).'" where nazwa="znak_wodny_wlacz" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['znak_wodny_url']).'" where nazwa="znak_wodny_url" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['naglowek']).'" where nazwa="naglowek" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['ile_na_strone']).'" where nazwa="ile_na_strone" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['pokaz_gmaps']).'" where nazwa="pokaz_gmaps" limit 1');
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['slider_dol']).'" where nazwa="slider_dol" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['glowna_opis']).'" where nazwa="glowna_opis" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['stopka_opis']).'" where nazwa="stopka_opis" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['stopka_dol']).'" where nazwa="stopka_dol" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['kod_css']).'" where nazwa="kod_css" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['kod_head']).'" where nazwa="kod_head" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['kod_body']).'" where nazwa="kod_body" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['pole_nowe_uzywane']).'" where nazwa="pole_nowe_uzywane" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['podkategorie_obowiazkowe']).'" where nazwa="podkategorie_obowiazkowe" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['ukryj_liczbe_wyswietlen']).'" where nazwa="ukryj_liczbe_wyswietlen" limit 1');
		
		pobierz_ustawienia();
	}
	
	// pobieranie listy szablonów
	$path = '../views/';
	$results = scandir($path);
	$szablony = array();
	foreach ($results as $result) {
		if ($result === '.' or $result === '..') continue;
		if (is_dir($path . '/' . $result)) {
		   $szablony[] .= $result;
		}
	}
	$smarty->assign("szablony", $szablony);
	
}else{
	die('Brak dostepu!');
}
