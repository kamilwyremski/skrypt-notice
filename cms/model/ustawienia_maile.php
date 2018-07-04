<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_ustawienia_maile' and isset($_POST['rejestracja_tytul']) and isset($_POST['rejestracja_tresc']) and isset($_POST['rejestracja_fb_tytul']) and isset($_POST['rejestracja_fb_tresc']) and isset($_POST['reset_hasla_tytul']) and isset($_POST['reset_hasla_tresc']) and isset($_POST['smtp_email']) and isset($_POST['smtp_host']) and isset($_POST['smtp_uzytkownik']) and isset($_POST['smtp_haslo'])){
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['rejestracja_tytul']).'" where nazwa="rejestracja_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['rejestracja_tresc']).'" where nazwa="rejestracja_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['rejestracja_fb_tytul']).'" where nazwa="rejestracja_fb_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['rejestracja_fb_tresc']).'" where nazwa="rejestracja_fb_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['reset_hasla_tytul']).'" where nazwa="reset_hasla_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['reset_hasla_tresc']).'" where nazwa="reset_hasla_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['email_start_tytul']).'" where nazwa="email_start_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['email_start_tresc']).'" where nazwa="email_start_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['email_koniec_tytul']).'" where nazwa="email_koniec_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['email_koniec_tresc']).'" where nazwa="email_koniec_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_kontakt_tytul']).'" where nazwa="mail_kontakt_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_kontakt_tresc']).'" where nazwa="mail_kontakt_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_ogloszenie_tytul']).'" where nazwa="mail_ogloszenie_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_ogloszenie_tresc']).'" where nazwa="mail_ogloszenie_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_naduzycie_tytul']).'" where nazwa="mail_naduzycie_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_naduzycie_tresc']).'" where nazwa="mail_naduzycie_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_powiadomienie_tytul']).'" where nazwa="mail_powiadomienie_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_powiadomienie_tresc']).'" where nazwa="mail_powiadomienie_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_newsletter_potw_tytul']).'" where nazwa="mail_newsletter_potw_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_newsletter_potw_tresc']).'" where nazwa="mail_newsletter_potw_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_pp_wyplata_admin_tytul']).'" where nazwa="mail_pp_wyplata_admin_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_pp_wyplata_admin_tresc']).'" where nazwa="mail_pp_wyplata_admin_tresc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_pp_wyplata_user_tytul']).'" where nazwa="mail_pp_wyplata_user_tytul" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['mail_pp_wyplata_user_tresc']).'" where nazwa="mail_pp_wyplata_user_tresc" limit 1');
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['smtp']).'" where nazwa="smtp" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['smtp_email']).'" where nazwa="smtp_email" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['smtp_host']).'" where nazwa="smtp_host" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['smtp_uzytkownik']).'" where nazwa="smtp_uzytkownik" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['smtp_haslo']).'" where nazwa="smtp_haslo" limit 1');
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['pokaz_formularz_kontaktowy']).'" where nazwa="pokaz_formularz_kontaktowy" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['pokaz_formularz_kontaktowy_profil']).'" where nazwa="pokaz_formularz_kontaktowy_profil" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['maile_zalaczniki']).'" where nazwa="maile_zalaczniki" limit 1');
		
		pobierz_ustawienia();
	}
	
}else{
	die('Brak dostepu!');
}
