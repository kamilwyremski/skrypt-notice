<?php

error_reporting(0);

include('../config/db.php');
include('globalne.php');
include('funkcje.php');
include('maile.php');

if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"]) && isset($_POST['item_number']) && isset($_POST['kwota'])){
	
	$dane_ogloszenia = mysql_fetch_assoc(mysql_query('select id, tytul, prosty_tytul from '.$prefiks_tabel.'ogloszenia where id="'.filtruj($_POST['item_number']).'" limit 1'));
	$item_name = 'Aktywacja ogloszenia: '.$dane_ogloszenia['tytul'];
	$item_amount = filtruj($_POST['kwota']);
	$return_url = $ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'?podglad&status=ok';
	$cancel_url = $ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'?podglad&status=error';
	
	$querystring = "?business=".urlencode($ustawienia['paypal_email'])."&";
	$querystring .= "item_name=".urlencode($item_name)."&";
	$querystring .= "amount=".urlencode($item_amount)."&";
	
	foreach($_POST as $key => $value){
		$value = urlencode(stripslashes($value));
		$querystring .= "$key=$value&";
	}
	
	$querystring .= "return=".urlencode(stripslashes($return_url))."&";
	$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
	$querystring .= "notify_url=".urlencode($ustawienia['base_url'].'/php/platnosci_paypal.php');
	
	if($ustawienia['paypal_tryb_testowy']){
		header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
	}else{
		header('location:https://www.paypal.com/cgi-bin/webscr'.$querystring);
	}
	exit();
} else {

	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);
		$req .= "&$key=$value";
	}

	$data['item_name']			= $_POST['item_name'];
	$data['item_number'] 		= $_POST['item_number'];
	$data['payment_status'] 	= $_POST['payment_status'];
	$data['payment_amount'] 	= $_POST['mc_gross'];
	$data['payment_currency']	= $_POST['mc_currency'];
	$data['txn_id']				= $_POST['txn_id'];
	$data['receiver_email'] 	= $_POST['receiver_email'];
	$data['payer_email'] 		= $_POST['payer_email'];
	$data['custom'] 			= $_POST['custom'];
	
	$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	if($ustawienia['paypal_tryb_testowy']){
		$header .= "Host: www.sandbox.paypal.com\r\n";  
	}else{
		$header .= "Host: www.paypal.com\r\n"; 
	}
	$header .= "Content-Length: " . strlen($req) . "\r\n";
	$header .= "Connection: close\r\n\r\n";
	
	if($ustawienia['paypal_tryb_testowy']){
		$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
	}else{
		$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
	}
	
	if ($fp) {
		fputs($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			if (stripos($res, "VERIFIED") !== false) {
				if(!mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'platnosci_paypal where txnid="'.$data['txn_id'].'" and status="Completed" limit 1'))){
					
					mysql_query("INSERT INTO `".$prefiks_tabel."platnosci_paypal` (txnid, kwota, status, id_ogloszenia, email_kupujacego, data) VALUES ('".$data['txn_id']."', '".$data['payment_amount']."', '".$data['payment_status']."', '".$data['item_number']."', '".$data['payer_email'] ."', '".date("Y-m-d H:i:s")."')");

					if ($data['payment_status']=='Completed'){
						ogloszenie_zostalo_oplacone($data['item_number'],$data['payment_amount']);
					}
				}
			}else if (stripos($res, "INVALID") !== false) {
				
				mysql_query("INSERT INTO `".$prefiks_tabel."platnosci_paypal` (txnid, kwota, status, id_ogloszenia, email_kupujacego, data) VALUES ('".$data['txn_id']."', '".$data['payment_amount']."', '".$data['payment_status']."', '".$data['item_number']."', '".$data['payer_email'] ."', '".date("Y-m-d H:i:s")."')");
				
			}
		}
		fclose ($fp);
	}
	
}
