<?php
if(isset($cms_login)){

	$ile_na_strone = 100;
	
	$wyszukaj = '';
	if(isset($_GET['wyszukaj'])){
		if(isset($_GET['id']) and $_GET['id']!=''){
			$wyszukaj .= ' and '.$prefiks_tabel.'ogloszenia.id="'.filtruj($_GET['id']).'" ';
		}
		if(isset($_GET['tytul']) and $_GET['tytul']!=''){
			$wyszukaj .= ' and prosty_tytul like "%'.filtruj(prosta_nazwa($_GET['tytul'])).'%" ';
		}
	}
	
	$q = mysql_query('select '.$prefiks_tabel.'logi_wyswietlenia.id_uzytkownika, '.$prefiks_tabel.'logi_wyswietlenia.ip, '.$prefiks_tabel.'logi_wyswietlenia.data, '.$prefiks_tabel.'ogloszenia.id, tytul, prosty_tytul from '.$prefiks_tabel.'logi_wyswietlenia, '.$prefiks_tabel.'ogloszenia where id_ogloszenia = '.$prefiks_tabel.'ogloszenia.id '.$wyszukaj.' order by '.sortuj($prefiks_tabel.'logi_wyswietlenia.data desc').' limit '.policz_strony($prefiks_tabel.'logi_wyswietlenia, '.$prefiks_tabel.'ogloszenia',$ile_na_strone,'id_ogloszenia = '.$prefiks_tabel.'ogloszenia.id '.$wyszukaj).','.$ile_na_strone.'');
	while($dane = mysql_fetch_assoc($q)){
		if($dane['id_uzytkownika']!=0){
			$wynik = mysql_fetch_assoc(mysql_query('select login, email from '.$prefiks_tabel.'uzytkownicy where id="'.$dane['id_uzytkownika'].'" limit 1'));
			$dane['email'] = $wynik['email'];
			$dane['login'] = $wynik['login'];
		}
		$logi_ogloszenia[] = $dane;
	}
	if(isset($logi_ogloszenia)){$smarty->assign("logi_ogloszenia", $logi_ogloszenia);}
	
}else{
	die('Brak dostepu!');
}
