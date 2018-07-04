<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($uzytkownik)){
	
	$gets_array = $_GET;
	unset($gets_array['akcja'],$gets_array['id'],$gets_array['strona'],$gets_array['zalogowano'],$gets_array['wylogowano']);
	$gets = http_build_query($gets_array);
	unset($gets_array['sortuj']);
	$smarty->assign("gets_array", $gets_array);
	$smarty->assign("gets", $gets);
	
	if(isset($_POST['akcja']) and $_POST['akcja']=='usun_schowek' and isset($_POST['id'])){
		mysql_query('DELETE from '.$prefiks_tabel.'schowek where id_uzytkownika="'.$uzytkownik['id'].'" and id_ogloszenia="'.filtruj($_POST['id']).'" limit 1');
		$infobox[] = array('klasa'=>'zielona','tresc'=>'Ogłoszenie zostało poprawnie usunięte ze schowka!');
	}

	$sortuj = 'id desc';
	if(isset($_GET['sortuj'])){
		if($_GET['sortuj']=='najstarsze'){
			$sortuj = 'id';
		}elseif($_GET['sortuj']=='najtansze'){
			$sortuj = 'cena, id desc';
		}elseif($_GET['sortuj']=='najdrozsze'){
			$sortuj = 'cena desc, id desc';
		}
	}

	$q = mysql_query('select '.$prefiks_tabel.'ogloszenia.id, tytul, prosty_tytul, kategoria, slider, promowana, typ, start, koniec, cena,cena_do_negocjacji, za_darmo, stan, email, telefon from '.$prefiks_tabel.'ogloszenia, '.$prefiks_tabel.'schowek where '.$prefiks_tabel.'schowek.id_uzytkownika = "'.$uzytkownik['id'].'" and '.$prefiks_tabel.'schowek.id_ogloszenia = '.$prefiks_tabel.'ogloszenia.id order by '.$sortuj.' limit '.policz_strony($prefiks_tabel.'ogloszenia, '.$prefiks_tabel.'schowek',$ustawienia['ile_na_strone'],$prefiks_tabel.'schowek.id_uzytkownika = "'.$uzytkownik['id'].'" and '.$prefiks_tabel.'schowek.id_ogloszenia = '.$prefiks_tabel.'ogloszenia.id').','.$ustawienia['ile_na_strone'].'');
	while($dane = mysql_fetch_assoc($q)){
		if(!$ustawienia['ukryj_liczbe_wyswietlen']){
			$dane['ile'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'logi_wyswietlenia where id_ogloszenia="'.$dane['id'].'"'));
		}
		$dane['miniaturka'] = mysql_fetch_assoc(mysql_query('select miniaturka from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$dane['id'].'" limit 1'))['miniaturka'];
		$dane['typ_nazwa'] = mysql_fetch_assoc(mysql_query('select nazwa from '.$prefiks_tabel.'typy_ogloszen where id="'.$dane['typ'].'" limit 1'))['nazwa'];
		$kategoria = mysql_fetch_assoc(mysql_query('select nazwa, prosta_nazwa from '.$prefiks_tabel.'kategorie where id="'.$dane['kategoria'].'" limit 1'));
		$dane['kategoria_nazwa'] = $kategoria['nazwa'];
		$dane['kategoria_prosta_nazwa'] = $kategoria['prosta_nazwa'];
		$ogloszenia[] = $dane;
	}
	if(isset($ogloszenia)){$smarty->assign("ogloszenia", $ogloszenia);}

	$nawigacja[] = array('url'=>'schowek', 'nazwa'=>'Schowek z ogłoszeniami');
	
}else{
	
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Musisz być zalogowany aby zobaczyć tą stronę');
	$strona = '404';
	include('model/404.php');
}
