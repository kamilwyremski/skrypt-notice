<?php

if(isset($cms_login)){
	
	function randomPassword() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); 
		$alphaLength = strlen($alphabet) - 1; 
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); 
	}

	if(isset($_POST['import_maili']) and is_uploaded_file($_FILES['plik']['tmp_name'])){
		$plik = file_get_contents($_FILES['plik']['tmp_name']);
		$plik = preg_replace('#\s+#',',',trim($plik));
		$arr = explode(',',trim($plik));
		foreach($arr as $email){
			if($email!=''){
				mysql_query('INSERT INTO `'.$prefiks_tabel.'newsletter`(`email`, `aktywny`, `kod`, `ip`, `data`) VALUES ("'.$email.'",1,"'.md5(randomPassword()).'","'.get_client_ip().'", "'.date("Y-m-d H:i:s").'")');
			}
		}
		$smarty->assign("import_komunikat", true);
	}
	
	$statystyki['uzytkownikow'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'uzytkownicy'));
	$statystyki['facebook'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'uzytkownicy where rejestracja_facebook = 1'));
	$statystyki['ogloszen'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'ogloszenia'));
	$statystyki['aktywnych'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'ogloszenia where aktywna = 1'));
	$statystyki['archiwum'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'archiwum_ogloszenia'));
	$statystyki['maile'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'logi_email'));
	$statystyki['logowan'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'logi_uzytkownicy'));
	$statystyki['wyswietlenia'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'logi_wyswietlenia'));
	$statystyki['zdjec'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'zdjecia'));
	$statystyki['newsletter'] = mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'newsletter where aktywny = 1'));
	$smarty->assign("statystyki", $statystyki);
}else{
	die('Brak dostepu!');
}
