<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='pp_procent' and isset($_POST['id']) and isset($_POST['pp_procent'])){
		mysql_query('update '.$prefiks_tabel.'uzytkownicy set pp_procent="'.filtruj($_POST['pp_procent']).'" where id="'.filtruj($_POST['id']).'" limit 1');
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
		if(isset($_GET['pp_polecajacy']) and $_GET['pp_polecajacy']>0){
			$wyszukaj .= ' and pp_polecajacy="'.filtruj($_GET['pp_polecajacy']).'" ';
		}
	}

	$q = mysql_query('select id, login, email, pp_polecajacy, rejestracja_facebook, pp_procent, pp_imie, pp_adres, pp_numer_konta from '.$prefiks_tabel.'uzytkownicy where '.$wyszukaj.' order by '.sortuj('data').' limit '.policz_strony($prefiks_tabel.'uzytkownicy',$ile_na_strone,$wyszukaj).','.$ile_na_strone.'');
	while($dane = mysql_fetch_array($q)){
		if($dane['pp_polecajacy']){
			$dane['polecajacy'] = mysql_fetch_assoc(mysql_query('select login from '.$prefiks_tabel.'uzytkownicy where id="'.$dane['pp_polecajacy'].'" limit 1'))['login'];
		}
		$q2 = mysql_query('select login from '.$prefiks_tabel.'uzytkownicy where pp_polecajacy="'.$dane['id'].'"');
		while($dane2 = mysql_fetch_array($q2)){
			$dane['poleceni'][] = $dane2['login'];
		}
		$pp_uzytkownicy[] = $dane;
	}
	if(isset($pp_uzytkownicy)){$smarty->assign("pp_uzytkownicy", $pp_uzytkownicy);}
	
	$q = mysql_query('select id, login from '.$prefiks_tabel.'uzytkownicy order by login');
	while($dane = mysql_fetch_assoc($q)){$uzytkownicy[] = $dane;}
	if(isset($uzytkownicy)){$smarty->assign("uzytkownicy", $uzytkownicy);}
	
}else{
	die('Brak dostepu!');
}
