<?php

error_reporting(0);

include('../config/db.php');
include('globalne.php');
include('maile.php');

$ipTable = array('195.149.229.109', '148.251.96.163', '178.32.201.77', '46.248.167.59', '46.29.19.106', '176.119.38.175');

if (in_array($_SERVER['REMOTE_ADDR'], $ipTable) && !empty($_POST)) {

	$sellerID = mysql_real_escape_string(trim($_POST['id']));
	$status_transakcji = mysql_real_escape_string(trim($_POST['tr_status']));
	$tpay_id = mysql_real_escape_string(trim($_POST['tr_id']));
	$oryginalna_kwota = mysql_real_escape_string(trim($_POST['tr_amount']));
	$kwota = mysql_real_escape_string(trim($_POST['tr_paid']));
	$znacznik_bledu = mysql_real_escape_string(trim($_POST['tr_error']));
	$data_transakcji = mysql_real_escape_string(trim($_POST['tr_date']));
	$opis = mysql_real_escape_string(trim($_POST['tr_desc']));
	$id_ogloszenia = mysql_real_escape_string(trim($_POST['tr_crc']));
	$email = mysql_real_escape_string(trim($_POST['tr_email']));
	$md5sum = mysql_real_escape_string(trim($_POST['md5sum']));
	$test_mode = mysql_real_escape_string(trim($_POST['test_mode'])); 

	if(!mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'platnosci_tpay where tpay_id="'.$tpay_id.'" and status_transakcji="TRUE" limit 1'))){
		
		mysql_query('INSERT INTO `'.$prefiks_tabel.'platnosci_tpay`(`tpay_id`, `status_transakcji`, `kwota`, `oryginalna_kwota`, `id_ogloszenia`, `opis`, `znacznik_bledu`, `email`, `md5sum`, `test_mode`, `data_transakcji`) VALUES ("'.$tpay_id.'", "'.$status_transakcji.'", "'.$kwota.'", "'.$oryginalna_kwota.'", "'.$id_ogloszenia.'", "'.$opis.'", "'.$znacznik_bledu.'", "'.$email.'", "'.$md5sum.'", "'.$test_mode.'", "'.$data_transakcji.'")');
		
		if ($status_transakcji=='TRUE' && $znacznik_bledu=='none') {
		
			if($md5sum == md5($sellerID . $tpay_id . $kwota . $id_ogloszenia . $ustawienia['tpay_kod'])){
				ogloszenie_zostalo_oplacone($id_ogloszenia,$kwota);
				echo 'TRUE';
			}else{
				echo 'FALSE';
			}		
		}else {
			echo 'FALSE';
		}
	}else {
		echo 'FALSE';
	}
	
} else {
	echo 'FALSE';
}
