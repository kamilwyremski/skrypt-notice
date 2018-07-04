<?php

error_reporting(0);

include 'class_przelewy24.php';
include('../config/db.php');
include('globalne.php');
include('funkcje.php');
include('maile.php');
include('logowanie.php');

if(isset($_GET['akcja']) and $_GET['akcja']=='zlec_zaplate' and isset($_GET['id_ogloszenia']) and $_GET['id_ogloszenia']>0){
	
	$id_ogloszenia = filtruj($_GET['id_ogloszenia']);
	$ogloszenie = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'ogloszenia where id="'.$id_ogloszenia.'" limit 1'));

	if($ogloszenie){
		list($oplacona,$oplata,$do_oplacenia,$zostalo_do_zaplacenia) = policz_koszt_ogloszenia($id_ogloszenia);
	
		$numer_transakcji = randomPassword();
		$przelewy24_md5sum = md5($numer_transakcji."|".$ustawienia['przelewy24_id_sprzedawcy']."|".($zostalo_do_zaplacenia*100)."|PLN|".$ustawienia['przelewy24_crc']);
		if(isset($uzytkownik)){$email=$uzytkownik['email'];}else{$email='';}
		mysql_query('INSERT INTO `'.$prefiks_tabel.'platnosci_przelewy24`(`numer_transakcji`, `przelewy24_id`, `kwota_oryginalna`, `id_ogloszenia`, `opis`, `email`, `tryb_testowy`, `md5sum`, `data`) VALUES ("'.$numer_transakcji.'", "'.$ustawienia['przelewy24_id_sprzedawcy'].'", "'.$zostalo_do_zaplacenia.'", "'.$ogloszenie['id'].'", "Aktywacja ogloszenia: '.$ogloszenie['id'].'", "'.$email.'", "'.$ustawienia['przelewy24_tryb_testowy'].'", "'.$przelewy24_md5sum.'", NOW())');
		
		if($ustawienia['przelewy24_tryb_testowy']){
			$form = '<form action="https://sandbox.przelewy24.pl/trnDirect" method="post" id="przelewy24-form">';
		}else{
			$form = '<form action="https://secure.przelewy24.pl/trnDirect" method="post" id="przelewy24-form">';
		}
		$form .='<input type="hidden" name="p24_sign" value="'.$przelewy24_md5sum.'" />
				<input type="hidden" name="p24_session_id" value="'.$numer_transakcji.'" />
				<input type="hidden" name="p24_merchant_id" value="'.$ustawienia['przelewy24_id_sprzedawcy'].'" />
				<input type="hidden" name="p24_pos_id" value="'.$ustawienia['przelewy24_id_sklepu'].'" />
				<input type="hidden" name="p24_amount" value="'.($zostalo_do_zaplacenia*100).'" />
				<input type="hidden" name="p24_currency" value="PLN" />
				<input type="hidden" name="p24_description" value="Aktywacja ogloszenia: '.$ogloszenie['id'].'" />';
		if(isset($uzytkownik)){
			$form .= '<input type="hidden" name="p24_email" value="'.$uzytkownik['email'].'" />';
		}else{
			$form .= '<input type="hidden" name="p24_email" value="'.$ogloszenie['email'].'" />';
		}
		$form .='<input type="hidden" name="p24_country" value="PL" />
				<input type="hidden" name="p24_url_return" value="'.$ustawienia['base_url'].'/'.$ogloszenie['id'].','.$ogloszenie['prosty_tytul'].'?podglad&status=przelewy24" />
				<input type="hidden" name="p24_url_status" value="'.$ustawienia['base_url'].'/php/platnosci_przelewy24.php" />
				<input type="hidden" name="p24_api_version" value="3.2" />
				<input type="submit" value="Zapłać przez Przelewy24"/>
			</form>';

		echo('<style>form{display:none}</style>');
		echo($form);
		echo('<script type="text/javascript">document.getElementById("przelewy24-form").submit();</script>');
		echo('<style>form{display:block}</style>');
	}
	
}elseif(($_SERVER['REMOTE_ADDR']=='91.216.191.181' or $_SERVER['REMOTE_ADDR']=='91.216.191.182' or $_SERVER['REMOTE_ADDR']=='91.216.191.183' or $_SERVER['REMOTE_ADDR']=='91.216.191.184' or $_SERVER['REMOTE_ADDR']=='91.216.191.185') && !empty($_POST)){ 

	$numer_transakcji = mysql_real_escape_string(trim($_POST['p24_session_id']));
	$numer_transakcji_przelewy24 = mysql_real_escape_string(trim($_POST['p24_order_id']));
	$id_sprzedawcy = mysql_real_escape_string(trim($_POST['p24_merchant_id']));
	$id_sklepu = mysql_real_escape_string(trim($_POST['p24_pos_id']));
	$kwota = mysql_real_escape_string(trim($_POST['p24_amount']));
	$dane_transakcji = mysql_fetch_assoc(mysql_query('select id, kwota_oryginalna, id_ogloszenia from '.$prefiks_tabel.'platnosci_przelewy24 where numer_transakcji="'.$numer_transakcji.'" and status_transakcji="" limit 1'));
	
	if(isset($dane_transakcji['id']) and $dane_transakcji['id']>0){
		
		$crc = $id_sklepu."|".$id_sprzedawcy."|".($dane_transakcji['kwota_oryginalna']*100)."|PLN|".$ustawienia['przelewy24_crc'];
		
		$P24 = new Przelewy24($id_sprzedawcy,$id_sklepu,md5($crc),$ustawienia['przelewy24_tryb_testowy']);
		
		foreach($_POST as $k=>$v) $P24->addValue($k,$v);  
		
		$P24->addValue('p24_currency','PLN');
		$P24->addValue('p24_amount',($dane_transakcji['kwota_oryginalna']*100));
		
		$res = $P24->trnVerify();

		if(isset($res["error"]) and $res["error"] ==0){
			
			$id_ogloszenia = $dane_transakcji['id_ogloszenia'];
			$kwota = $kwota/100;
			
			mysql_query('update '.$prefiks_tabel.'platnosci_przelewy24 set numer_transakcji_przelewy24="'.$numer_transakcji_przelewy24.'", status_transakcji = "1", kwota="'.$kwota.'" where id="'.$dane_transakcji['id'].'" limit 1');

			ogloszenie_zostalo_oplacone($id_ogloszenia,$kwota);
		
		}else{
			
			mysql_query('update '.$prefiks_tabel.'platnosci_przelewy24 set bledy = "'.$res["errorMessage"].'" where id="'.$dane_transakcji['id'].'" limit 1');

		}
	}
}
