<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and isset($_POST['id'])){
		if($_POST['akcja']=='notatka_uzytkownika' and isset($_POST['notatka'])){
			mysql_query('update '.$prefiks_tabel.'uzytkownicy set notatka="'.htmlspecialchars($_POST['notatka']).'" where id="'.filtruj($_POST['id']).'" limit 1');
		}elseif($_POST['akcja']=='usun_uzytkownika'){
			$id = filtruj($_POST['id']);
			$q = mysql_query('select id from '.$prefiks_tabel.'ogloszenia where id_uzytkownika="'.$id.'"');
			while($dane = mysql_fetch_assoc($q)){
				ogloszenie_do_archiwum($dane['id']);
			}
			$awatar = mysql_fetch_assoc(mysql_query('select awatar from '.$prefiks_tabel.'uzytkownicy where id="'.$id.'" limit 1'))['awatar'];
			if($awatar!=''){
				unlink('../'.$ustawienia['upload'].'/'.$awatar);
			}
			mysql_query('delete from '.$prefiks_tabel.'logi_uzytkownicy where id_uzytkownika="'.$id.'"');
			mysql_query('delete from '.$prefiks_tabel.'reset_hasla where id_uzytkownika="'.$id.'"');
			mysql_query('delete from '.$prefiks_tabel.'schowek where id_uzytkownika="'.$id.'"');
			mysql_query('delete from '.$prefiks_tabel.'uzytkownicy_oceny where id_profilu="'.$id.'"');
			mysql_query('delete from '.$prefiks_tabel.'uzytkownicy where id="'.$id.'" limit 1');
			policz_podkategorie();
		}
	}
	
	$ile_na_strone = 100;
	
	$wyszukaj = ' true ';
	
	if(isset($_GET['wyszukaj'])){
		if(isset($_GET['email']) and $_GET['email']!=''){
			$wyszukaj .= ' and email like "%'.filtruj($_GET['email']).'%" ';
		}
		if(isset($_GET['login']) and $_GET['login']!=''){
			$wyszukaj .= ' and login like "%'.filtruj($_GET['login']).'%" ';
		}
	}

	$q = mysql_query('select id, login, email, dane_faktura, admin, aktywny, data, notatka, ip_aktywacji, data_aktywacji, rejestracja_facebook from '.$prefiks_tabel.'uzytkownicy where '.$wyszukaj.' order by '.sortuj('data').' limit '.policz_strony($prefiks_tabel.'uzytkownicy',$ile_na_strone,$wyszukaj).','.$ile_na_strone.'');
	while($dane = mysql_fetch_assoc($q)){
		$dane['ile'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'ogloszenia where id_uzytkownika="'.$dane['id'].'"'));
		$dane['ile_aktywnych'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'ogloszenia where id_uzytkownika="'.$dane['id'].'" and aktywna=1'));
		$q2 = mysql_query('select data from '.$prefiks_tabel.'logi_uzytkownicy where id_uzytkownika="'.$dane['id'].'" order by data desc limit 1');
		while($dane2 = mysql_fetch_assoc($q2)){	$dane['ostatnie_logowanie'] = $dane2['data'];}
		$dane['ilosc_logowan'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'logi_uzytkownicy where id_uzytkownika="'.$dane['id'].'"'));
		$dane['dane_faktura'] = nl2br($dane['dane_faktura']);
		$dane['notatka'] = htmlspecialchars_decode($dane['notatka']);
		$uzytkownicy[] = $dane;
	}
	if(isset($uzytkownicy)){$smarty->assign("uzytkownicy", $uzytkownicy);}
	
}else{
	die('Brak dostepu!');
}
