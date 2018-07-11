<?php

error_reporting(0);

include('../config/db.php');
include('globalne.php');
include('maile.php');

if($_SERVER['REMOTE_ADDR']=='195.150.9.37' && !empty($_POST)){ 

	if(!empty($_POST['operation_type'])){
		$dotpay_id = mysql_real_escape_string(trim($_POST['id']));
		$id_ogloszenia = mysql_real_escape_string(trim($_POST['control']));
		$email_kupujacego = mysql_real_escape_string(trim($_POST['email']));
		$opis = mysql_real_escape_string(trim($_POST['description']));	
		$operation_type = mysql_real_escape_string(trim($_POST['operation_type']));	
		$operation_status = mysql_real_escape_string(trim($_POST['operation_status']));	
		$kwota = mysql_real_escape_string(trim($_POST['operation_amount']));	
		$oryginalna_kwota = mysql_real_escape_string(trim($_POST['operation_original_amount']));	
		$signature = mysql_real_escape_string(trim($_POST['signature']));	
		$data_transakcji = mysql_real_escape_string(trim($_POST['operation_datetime']));	
		$numer_transakcji = mysql_real_escape_string(trim($_POST['operation_number']));
		$sign = hash('sha256', $ustawienia['dotpay_pin'].$_POST['id'].$_POST['operation_number'].$_POST['operation_type'].$_POST['operation_status'].$_POST['operation_amount'].$_POST['operation_currency'].$_POST['operation_withdrawal_amount'].$_POST['operation_commission_amount'].$_POST['operation_original_amount'].$_POST['operation_original_currency'].$_POST['operation_datetime'].$_POST['operation_related_number'].$_POST['control'].$_POST['description'].$_POST['email'].$_POST['p_info'].$_POST['p_email'].$_POST['credit_card_issuer_identification_number'].$_POST['credit_card_masked_number'].$_POST['credit_card_brand_codename'].$_POST['credit_card_brand_code'].$_POST['credit_card_id'].$_POST['channel'].$_POST['channel_country'].$_POST['geoip_country']);	
		
		if($operation_type == 'payment' and $signature == $sign and $ustawienia['dotpay_id'] == $dotpay_id){
			if(!mysql_num_rows(mysql_query('SELECT * FROM `'.$prefiks_tabel.'platnosci_dotpay` WHERE numer_transakcji = "'.$numer_transakcji.'" and status_transakcji="WYKONANA" limit 1'))){
				mysql_query('INSERT INTO '.$prefiks_tabel.'platnosci_dotpay (dotpay_id, status_transakcji, numer_transakcji, id_ogloszenia, kwota, oryginalna_kwota, email_kupujacego, opis, data_transakcji, data_URLC) VALUES ("'.$dotpay_id.'", "'.$operation_status.'", "'.$numer_transakcji.'", "'.$id_ogloszenia.'", "'.$kwota.'", "'.$oryginalna_kwota.'", "'.$email_kupujacego.'", "'.$opis.'", "'.$data_transakcji.'", NOW());');
				if($operation_status == "completed"){
					ogloszenie_zostalo_oplacone($id_ogloszenia,$kwota);
				}
			}
			echo "OK";
		}else{     		
			echo "ERROR";	
		}
	}else{
		// PIN należy ustawić w panelu administracyjnym "Mój Dotpay" w sekcji "Ustawienia -> parametry URLC". Musi mieć dokładnie 16 znaków alfanumerycznych. 
		// Definiowane po stronie Dotpay
		$dotpay_id = mysql_real_escape_string(trim($_POST['id']));
		$status_transakcji = mysql_real_escape_string(trim($_POST['t_status']));
		$numer_transakcji = mysql_real_escape_string(trim($_POST['t_id']));
		$status_platnosci = mysql_real_escape_string(trim($_POST['status']));
		$oryginalna_kwota = mysql_real_escape_string(trim($_POST['orginal_amount']));
		$data_transakcji = mysql_real_escape_string(trim($_POST['t_date']));
		$dotpay_md5 = mysql_real_escape_string(trim($_POST['md5']));
		// Definiowane przez kontrahenta oraz klienta
		$service = mysql_real_escape_string(trim($_POST['service']));
		$kwota = mysql_real_escape_string(trim($_POST['amount']));
		$id_ogloszenia = mysql_real_escape_string(trim($_POST['control']));
		$email_kupujacego = mysql_real_escape_string(trim($_POST['email']));
		$opis = mysql_real_escape_string(trim($_POST['description']));
		//Modyfikacje parametrów
		switch ($status_transakcji) {
			case "1": $status_transakcji_opis = "NOWA";
				break;
			case "2": $status_transakcji_opis = "WYKONANA";
				break;
			case "3": $status_transakcji_opis = "ODRZUCONA";
				break;
			case "4": $status_transakcji_opis = "ANULOWANA";
				break;
			case "5":$status_transakcji_opis = "REKLAMACJA";
				break;
		}
		trim($status_transakcji_opis);
		// w analogiczny sposób MD5 jest wyliczna po stronie Dotpay i przesyłana w parametrze MD5
		$md5 = md5($ustawienia['dotpay_pin'].":".$_POST['id'].":".$_POST['control'].":".$_POST['t_id'].":".$_POST['amount'].":".$_POST['email'].":".$_POST['service'].":".$_POST['code'].":".$_POST['username'].":".$_POST['password'].":".$_POST['t_status']);
		if($md5 == $dotpay_md5 and $ustawienia['dotpay_id'] == $dotpay_id and ($ustawienia['dotpay_tryb_testowy'] || preg_match("#TST#si", strtoupper($numer_transakcji)) == 0)){
			if(!mysql_num_rows(mysql_query('SELECT * FROM `'.$prefiks_tabel.'platnosci_dotpay` WHERE numer_transakcji = "'.$numer_transakcji.'" and status_transakcji="WYKONANA" limit 1'))){
				mysql_query('INSERT INTO '.$prefiks_tabel.'platnosci_dotpay (dotpay_id, status_transakcji, numer_transakcji, status_platnosci, id_ogloszenia, kwota, oryginalna_kwota, email_kupujacego, opis, data_transakcji, data_URLC) VALUES ("'.$dotpay_id.'", "'.$status_transakcji_opis.'", "'.$numer_transakcji.'", "'.$status_platnosci.'", "'.$id_ogloszenia.'", "'.$kwota.'", "'.$oryginalna_kwota.'", "'.$email_kupujacego.'", "'.$opis.'", "'.$data_transakcji.'", "'.date("Y-m-d H:i:s").'");');
				if($status_transakcji_opis == "WYKONANA"){
					ogloszenie_zostalo_oplacone($id_ogloszenia,$kwota);
				}
			}
			echo "OK";
		}else{     
			echo "ERROR";
		}
	}
}


