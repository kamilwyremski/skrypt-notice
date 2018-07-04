<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_program_partnerski' and isset($_POST['pp_prowizja']) and isset($_POST['pp_email']) and isset($_POST['pp_godzin_dezaktywacja']) and isset($_POST['pp_minimalna_kwota'])){
		
		if(isset($_POST['program_partnerski'])){$program_partnerski=1;}else{$program_partnerski=0;}
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.$program_partnerski.'" where nazwa="program_partnerski" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['pp_prowizja']).'" where nazwa="pp_prowizja" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['pp_email']).'" where nazwa="pp_email" limit 1');
		if(isset($_POST['pp_deaktywacja_po_rejestracji'])){$pp_deaktywacja_po_rejestracji=1;}else{$pp_deaktywacja_po_rejestracji=0;}
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.$pp_deaktywacja_po_rejestracji.'" where nazwa="pp_deaktywacja_po_rejestracji" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['pp_godzin_dezaktywacja']).'" where nazwa="pp_godzin_dezaktywacja" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.number_format(filtruj($_POST['pp_minimalna_kwota']), 2, '.', ' ').'" where nazwa="pp_minimalna_kwota" limit 1');
		
		pobierz_ustawienia();
	}
	
}else{
	die('Brak dostepu!');
}
