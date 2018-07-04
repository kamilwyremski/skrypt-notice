<?php

if(isset($cms_login)){

	$ile_na_strone = 50;
	
	$wyszukaj = ' true ';
	
	if(isset($_GET['wyszukaj'])){
		if(isset($_GET['id_ogloszenia']) and $_GET['id_ogloszenia']>0){
			$wyszukaj .= ' and id_ogloszenia = "'.filtruj($_GET['id_ogloszenia']).'" ';
		}
		if(isset($_GET['id_uzytkownika']) and $_GET['id_uzytkownika']>0){
			$wyszukaj .= ' and id_uzytkownika = "'.filtruj($_GET['id_uzytkownika']).'" ';
		}
		if(isset($_GET['id_polecajacego']) and $_GET['id_polecajacego']>0){
			$wyszukaj .= ' and id_polecajacego = "'.filtruj($_GET['id_polecajacego']).'" ';
		}
		if(isset($_GET['data_od']) and $_GET['data_od']!=''){
			$wyszukaj .= ' and data >= "'.filtruj($_GET['data_od']).'" ';
		}
		if(isset($_GET['data_do']) and $_GET['data_do']!=''){
			$wyszukaj .= ' and data <= "'.filtruj($_GET['data_do']).' 23:59" ';
		}
	}
	
	$q = mysql_query('select * from '.$prefiks_tabel.'pp_prowizje where '.$wyszukaj.' order by '.sortuj('data desc').' limit '.policz_strony($prefiks_tabel.'pp_prowizje',$ile_na_strone,$wyszukaj).','.$ile_na_strone.'');
	while($dane = mysql_fetch_assoc($q)){
		$ogl = mysql_fetch_array(mysql_query('select tytul, prosty_tytul from ogloszenia where id="'.$dane['id_ogloszenia'].'" limit 1'));
		$dane['tytul'] = $ogl['tytul'];
		$dane['prosty_tytul'] = $ogl['prosty_tytul'];
		$dane['uzytkownik'] = mysql_fetch_assoc(mysql_query('select login from uzytkownicy where id="'.$dane['id_uzytkownika'].'" limit 1'))['login'];
		$dane['polecajacy'] = mysql_fetch_assoc(mysql_query('select login from uzytkownicy where id="'.$dane['id_polecajacego'].'" limit 1'))['login'];
		$pp_prowizje[] = $dane;
	}
	if(isset($pp_prowizje)){$smarty->assign("pp_prowizje", $pp_prowizje);}
	
	$suma_prowizji = mysql_fetch_assoc(mysql_query('select sum(prowizja) as suma from '.$prefiks_tabel.'pp_prowizje where '.$wyszukaj.''))['suma'];
	$smarty->assign("suma_prowizji", $suma_prowizji);

	$q = mysql_query('select id, login from '.$prefiks_tabel.'uzytkownicy order by login');
	while($dane = mysql_fetch_assoc($q)){$uzytkownicy[] = $dane;}
	if(isset($uzytkownicy)){$smarty->assign("uzytkownicy", $uzytkownicy);}
	
}else{
	die('Brak dostepu!');
}
