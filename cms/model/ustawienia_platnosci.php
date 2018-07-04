<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_ustawienia_platnosci' and isset($_POST['koszt_ogloszenia']) and $_POST['koszt_ogloszenia']>=0 and isset($_POST['koszt_slider']) and $_POST['koszt_slider']>=0 and isset($_POST['koszt_promowana']) and $_POST['koszt_promowana']>=0 and isset($_POST['koszt_zdjecia']) and $_POST['koszt_zdjecia']>=0 and isset($_POST['zdjec_bezplatnie']) and $_POST['zdjec_bezplatnie']>=0 and isset($_POST['dotpay_id']) and isset($_POST['dotpay_pin']) and isset($_POST['dotpay_waluta']) and isset($_POST['tpay_id']) and isset($_POST['tpay_kod'])){
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.number_format(filtruj($_POST['koszt_ogloszenia']), 2, '.', ' ').'" where nazwa="koszt_ogloszenia" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.number_format(filtruj($_POST['koszt_slider']), 2, '.', ' ').'" where nazwa="koszt_slider" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.number_format(filtruj($_POST['koszt_promowana']), 2, '.', ' ').'" where nazwa="koszt_promowana" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.number_format(filtruj($_POST['koszt_zdjecia']), 2, '.', ' ').'" where nazwa="koszt_zdjecia" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['zdjec_bezplatnie']).'" where nazwa="zdjec_bezplatnie" limit 1');
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['platnosc_payu']).'" where nazwa="platnosc_payu" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['payu_id']).'" where nazwa="payu_id" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['payu_md5']).'" where nazwa="payu_md5" limit 1');
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['platnosc_dotpay']).'" where nazwa="platnosc_dotpay" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['dotpay_nowy_formularz']).'" where nazwa="dotpay_nowy_formularz" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['dotpay_id']).'" where nazwa="dotpay_id" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['dotpay_pin']).'" where nazwa="dotpay_pin" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['dotpay_waluta']).'" where nazwa="dotpay_waluta" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['dotpay_tryb_testowy']).'" where nazwa="dotpay_tryb_testowy" limit 1');
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['platnosc_tpay']).'" where nazwa="platnosc_tpay" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['tpay_id']).'" where nazwa="tpay_id" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['tpay_kod']).'" where nazwa="tpay_kod" limit 1');
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['platnosc_przelewy24']).'" where nazwa="platnosc_przelewy24" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['przelewy24_id_sprzedawcy']).'" where nazwa="przelewy24_id_sprzedawcy" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['przelewy24_id_sklepu']).'" where nazwa="przelewy24_id_sklepu" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['przelewy24_crc']).'" where nazwa="przelewy24_crc" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['przelewy24_tryb_testowy']).'" where nazwa="przelewy24_tryb_testowy" limit 1');
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['platnosc_paypal']).'" where nazwa="platnosc_paypal" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['paypal_email']).'" where nazwa="paypal_email" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['paypal_lokalizacja']).'" where nazwa="paypal_lokalizacja" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['paypal_waluta']).'" where nazwa="paypal_waluta" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['paypal_tryb_testowy']).'" where nazwa="paypal_tryb_testowy" limit 1');
		
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.isset($_POST['platnosc_checkout']).'" where nazwa="platnosc_checkout" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['checkout_id']).'" where nazwa="checkout_id" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['checkout_secret']).'" where nazwa="checkout_secret" limit 1');
		
		pobierz_ustawienia();
	}

}else{
	die('Brak dostepu!');
}
