<?php

session_start(); 

include('../config/db.php');

include('funkcje.php');
include('globalne.php');
include('maile.php');
include('logowanie.php');

if(isset($uzytkownik) and isset($_POST['data'])){
	$post = $_POST['data'];
	if($post['akcja']=='zakoncz' and isset($post['id'])){
		$id = filtruj($post['id']);
		mysql_query('update '.$prefiks_tabel.'ogloszenia set aktywna = 0, koniec="'.date("Y-m-d H:i:s").'"  where id="'.$id.'" and id_uzytkownika="'.$uzytkownik['id'].'" limit 1');
		if(mysql_affected_rows()){
			wyslij_mail('koniec','',array('id'=>$id));
			policz_podkategorie();
		}
	}elseif($post['akcja']=='usun' and isset($post['id'])){
		$id = filtruj($post['id']);
		if(($uzytkownik['admin'] and mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'ogloszenia where id="'.$id.'" limit 1'))) or mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'ogloszenia where id="'.$id.'" and id_uzytkownika="'.$uzytkownik['id'].'" limit 1'))){
			wyslij_mail('koniec','',array('id'=>$id));
			ogloszenie_do_archiwum($id);
			policz_podkategorie();
		}
	}elseif($post['akcja']=='usun_awatar' and $uzytkownik['awatar']!=''){
		mysql_query('update '.$prefiks_tabel.'uzytkownicy set awatar="" where id="'.$uzytkownik['id'].'" limit 1');
		unlink('../'.$ustawienia['upload'].'/'.$uzytkownik['awatar']);
	}elseif($post['akcja']=='odswiez' and isset($post['id'])){
		mysql_query('update '.$prefiks_tabel.'ogloszenia set aktywna = 1, slider = 0, promowana = 0, start=NOW(), koniec=CURDATE() + INTERVAL '.$ustawienia['odswiezanie_dni'].' DAY where id="'.filtruj($post['id']).'" and id_uzytkownika="'.$uzytkownik['id'].'" and oplacona = 1 limit 1');
	}
}elseif($ustawienia['ocenianie_profilu'] and isset($_POST['akcja']) and $_POST['akcja']=='ocena_profilu' and isset($_POST['ocena']) and $_POST['ocena']>0 and $_POST['ocena']<=5 and isset($_POST['id']) and $_POST['id']>0){
	$id_profilu = filtruj($_POST['id']);
	$ocena = filtruj($_POST['ocena']);
	$ip = get_client_ip();
	if(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'uzytkownicy_oceny where id_profilu="'.$id_profilu.'" and ip="'.$ip.'" limit 1'))){
		echo json_encode(false);
	}else{
		mysql_query('INSERT INTO `'.$prefiks_tabel.'uzytkownicy_oceny`(`id_profilu`, `ocena`, `ip`, `data`) VALUES ("'.$id_profilu.'", "'.$ocena.'", "'.$ip.'", NOW())');
		echo json_encode(pobierz_oceny_profilu($id_profilu));
	}
}else{
	die('Brak dostępu!');
}
