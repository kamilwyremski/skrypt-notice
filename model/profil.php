<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($_GET['login'])){
	
	$login = filtruj($_GET['login']);

	$profil = mysql_fetch_assoc(mysql_query('select id, login, awatar, o_mnie, data from '.$prefiks_tabel.'uzytkownicy where login="'.$login.'" limit 1'));
	if($profil!=''){
		
		$q = mysql_query('select id, tytul, prosty_tytul, kategoria, promowana, typ, start, cena, cena_do_negocjacji, za_darmo from '.$prefiks_tabel.'ogloszenia where id_uzytkownika = "'.$profil['id'].'" and aktywna=1 order by promowana desc, start desc, id desc limit '.policz_strony($prefiks_tabel.'ogloszenia',$ustawienia['ile_na_strone'],$prefiks_tabel.'ogloszenia.id_uzytkownika = "'.$profil['id'].'" and aktywna=1 ').','.$ustawienia['ile_na_strone'].'');
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

		$q = mysql_query('select * from '.$prefiks_tabel.'kategorie where kategoria = 0 order by nazwa');
		while($dane = mysql_fetch_assoc($q)){$kategorie[] = $dane;}
		if(isset($kategorie)){$smarty->assign("kategorie", $kategorie);}

		$profil['ile'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'ogloszenia where aktywna=1 and id_uzytkownika="'.$profil['id'].'"'));
		$profil['ostatnie_logowanie'] = mysql_fetch_array(mysql_query('select data from '.$prefiks_tabel.'logi_uzytkownicy where id_uzytkownika="'.$profil['id'].'" order by data desc limit 1'))['data'];
		$profil['o_mnie'] = htmlspecialchars_decode($profil['o_mnie']);
		
		$profil['oceny'] = pobierz_oceny_profilu($profil['id']);
		
		$smarty->assign("profil", $profil);
		$nawigacja[] = array('url'=>'profil,'.$login, 'nazwa'=>'Profil użytkownika '.$login);
		$ustawienia['title'] = 'Profil użytkownika '.$login.' - '.$ustawienia['title'];
		$ustawienia['description'] = 'Profil użytkownika '.$login.' - '.$ustawienia['description'];
		
	}else{
		$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nieprawidłowy link, w bazie danych nie istnieje użytkownik o podanym loginie');
		$strona = '404';
		include('model/404.php');
	}
}else{
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nieprawidłowy link, w bazie danych nie istnieje użytkownik o podanym loginie');
	$strona = '404';
	include('model/404.php');
}

pobierz_slider_dol();
