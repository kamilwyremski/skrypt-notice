<?php

error_reporting(0);

include('../config/db.php');
include('globalne.php');
include('maile.php');

$ip_table=array('195.149.229.109', '148.251.96.163', '178.32.201.77', '46.248.167.59','46.29.19.106');

if(in_array($_SERVER['REMOTE_ADDR'], $ip_table) && !empty($_POST)){

	$tpay_id = mysql_real_escape_string(trim($_POST['tr_id'])); //id transakcji od tpay
	$status_transakcji = mysql_real_escape_string(trim($_POST['tr_status']));  //TRUE or FALSE
	$kwota = mysql_real_escape_string(trim($_POST['tr_paid'])); // kwota zapłacona
	$oryginalna_kwota = mysql_real_escape_string(trim($_POST['tr_amount'])); // kwota właściwa
	$id_ogloszenia = mysql_real_escape_string(trim($_POST['tr_crc'])); //id ogłoszenia
	$opis = mysql_real_escape_string(trim($_POST['tr_desc'])); //opis transakcji
	$znacznik_bledu = mysql_real_escape_string(trim($_POST['tr_error'])); 
	$email = mysql_real_escape_string(trim($_POST['tr_email'])); 
	$md5sum = mysql_real_escape_string(trim($_POST['md5sum'])); //md5sum - należy to sprawdzić
	$test_mode = mysql_real_escape_string(trim($_POST['test_mode'])); 
	$data_transakcji = mysql_real_escape_string(trim($_POST['tr_date'])); 

	if(!mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'platnosci_tpay where tpay_id="'.$tpay_id.'" and status_transakcji="TRUE" limit 1'))){
		
		mysql_query('INSERT INTO `'.$prefiks_tabel.'platnosci_tpay`(`tpay_id`, `status_transakcji`, `kwota`, `oryginalna_kwota`, `id_ogloszenia`, `opis`, `znacznik_bledu`, `email`, `md5sum`, `test_mode`, `data_transakcji`) VALUES ("'.$tpay_id.'", "'.$status_transakcji.'", "'.$kwota.'", "'.$oryginalna_kwota.'", "'.$id_ogloszenia.'", "'.$opis.'", "'.$znacznik_bledu.'", "'.$email.'", "'.$md5sum.'", "'.$test_mode.'", "'.$data_transakcji.'")');
		
		if($status_transakcji=='TRUE' or $znacznik_bledu=='overpay'){ 			
		
			ogloszenie_zostalo_oplacone($id_ogloszenia,$kwota);
		
		}
	}
	
	echo('TRUE');
}else{
	echo('FALSE');
}

