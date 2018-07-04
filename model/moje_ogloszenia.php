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
	
	$nawigacja[] = array('url'=>'moje_ogloszenia', 'nazwa'=>'Moje ogłoszenia');

	$warunek = 'id_uzytkownika = "'.$uzytkownik['id'].'"';
	
	if(isset($_GET['szukaj'])){
		$szukaj_array = explode(' ', filtruj($_GET['szukaj']));
		if(count($szukaj_array)){
			$warunek .= ' and ( ';
			for($i=0; $i < count($szukaj_array); $i++){
				$warunek .= ' ('.$prefiks_tabel.'ogloszenia.prosty_tytul like "%'.prosta_nazwa($szukaj_array[$i]).'%" or '.$prefiks_tabel.'ogloszenia.opis like "%'.$szukaj_array[$i].'%") ';
				if($i+1 < count($szukaj_array)){$warunek .= ' or ';}
			}
			$warunek .= ' ) ';
		}
		if(isset($_GET['cena_od']) and $_GET['cena_od']!=''){
			$warunek .= ' and '.$prefiks_tabel.'ogloszenia.cena>='.filtruj($_GET['cena_od']).' ';
		}
		if(isset($_GET['cena_do']) and $_GET['cena_do']!=''){
			$warunek .= ' and '.$prefiks_tabel.'ogloszenia.cena<='.filtruj($_GET['cena_do']).' ';
		}
		array_push($nawigacja,array('nazwa'=>'Wyniki wyszukiwania', 'url'=>'moje_ogloszenia?'.$gets));
	}

	$sortuj = 'id desc';
	if(isset($_GET['sortuj'])){
		if($_GET['sortuj']=='nieaktywne'){
			$sortuj = 'aktywna, id desc';
		}elseif($_GET['sortuj']=='aktywne'){
			$sortuj = 'aktywna desc, id desc';
		}elseif($_GET['sortuj']=='najstarsze'){
			$sortuj = 'id';
		}elseif($_GET['sortuj']=='najtansze'){
			$sortuj = 'cena, id desc';
		}elseif($_GET['sortuj']=='najdrozsze'){
			$sortuj = 'cena desc, id desc';
		}
	}

	$q = mysql_query('select id, aktywna, oplacona, tytul, prosty_tytul, kategoria, slider, promowana, typ, start, koniec, cena,cena_do_negocjacji, za_darmo, stan from '.$prefiks_tabel.'ogloszenia where '.$warunek.' order by '.$sortuj.' limit '.policz_strony($prefiks_tabel.'ogloszenia',$ustawienia['ile_na_strone'],$warunek).','.$ustawienia['ile_na_strone'].'');
	while($dane = mysql_fetch_assoc($q)){
		if(!$ustawienia['ukryj_liczbe_wyswietlen']){
			$dane['ile'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'logi_wyswietlenia where id_ogloszenia="'.$dane['id'].'"'));
		}
		$dane['miniaturka'] = mysql_fetch_assoc(mysql_query('select miniaturka from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$dane['id'].'" limit 1'))['miniaturka'];
		$dane['typ_nazwa'] = mysql_fetch_assoc(mysql_query('select nazwa from '.$prefiks_tabel.'typy_ogloszen where id="'.$dane['typ'].'" limit 1'))['nazwa'];
		$kategoria = mysql_fetch_assoc(mysql_query('select nazwa, prosta_nazwa from '.$prefiks_tabel.'kategorie where id="'.$dane['kategoria'].'" limit 1'));
		$dane['kategoria_nazwa'] = $kategoria['nazwa'];
		$dane['kategoria_prosta_nazwa'] = $kategoria['prosta_nazwa'];
		$dane['odswiez']['aktywne'] = false;
		$dane['odswiez']['dni'] = floor((strtotime($dane['koniec']) - time())/(60*60*24)-$ustawienia['odswiezanie_dni_przed']);
		if($dane['oplacona'] and ($dane['aktywna']==0 or $dane['odswiez']['dni']<=0)){
			$dane['odswiez']['aktywne'] = true;
		}
		$ogloszenia[] = $dane;
	}
	if(isset($ogloszenia)){
		$smarty->assign("ogloszenia", $ogloszenia);
	}elseif(isset($_GET['strona']) and is_numeric($_GET['strona']) and $_GET['strona']>1){
		header("Location: ".$ustawienia['base_url']."/moje_ogloszenia");
	}

}else{
	
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Musisz być zalogowany aby zobaczyć tą stronę');
	$strona = '404';
	include('model/404.php');
}
