<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

$warunek = '';
$warunek_kategorii = $nadkategoria = 0;

$gets_array = $_GET;
unset($gets_array['akcja'],$gets_array['id'],$gets_array['strona'],$gets_array['zalogowano'],$gets_array['wylogowano']);
$gets = http_build_query($gets_array);
unset($gets_array['sortuj']);
$smarty->assign("gets_array", $gets_array);
$smarty->assign("gets", $gets);

if(isset($_GET['akcja']) and $_GET['akcja']=='kategoria' and isset($_GET['id'])){
	$id_kategorii = intval($_GET['id']);
	$wynik = mysql_fetch_assoc(mysql_query('select nazwa, prosta_nazwa, kategoria, podkategorie, keywords, description, glowna_opis from '.$prefiks_tabel.'kategorie where id="'.$id_kategorii.'" limit 1'));
	if($wynik!=''){
		$podkategorie = $wynik['podkategorie'];
		$nadkategoria = $wynik['kategoria'];
		if($podkategorie==''){
			$warunek = 'and kategoria='.$id_kategorii.'';
		}else{
			$warunek = 'and (kategoria='.$id_kategorii.' or kategoria='.str_replace(",",' or kategoria=',$podkategorie).')';
		}
		$warunek_kategorii = $id_kategorii;
		
		$nawigacja_nadkategoria = $id_kategorii;
		while($nawigacja_nadkategoria!=0){
			$q = mysql_query('select id, nazwa, prosta_nazwa, kategoria from '.$prefiks_tabel.'kategorie where id = "'.$nawigacja_nadkategoria.'"');
			while($dane = mysql_fetch_assoc($q)){
				$nawigacja_nadkategoria = $dane['kategoria'];
				$dane['url'] = 'kategoria,'.$dane['id'].','.$dane['prosta_nazwa'];
				$nawigacja[] = $dane;
			}
		}
		if(isset($nawigacja)){
			$smarty->assign("rss_kategoria", $nawigacja[0]);
			if(isset($nawigacja[1])){
				$smarty->assign("menu_kategoria", $nawigacja[1]);
			}else{
				$smarty->assign("menu_kategoria", array('url'=>$ustawienia['base_url'], 'nazwa'=>'Strona główna'));
			}
			$nawigacja = array_reverse($nawigacja);
		}
		$ustawienia['title'] = $wynik['nazwa'].' - '.$ustawienia['title'];
		if($wynik['keywords']!=''){$ustawienia['keywords'] = $wynik['keywords'];}
		if($wynik['description']!=''){
			$ustawienia['description'] = $wynik['description'];
		}else{
			$ustawienia['description'] = 'Ogłoszenia z kategorii: '.$wynik['nazwa'].'. '.$ustawienia['description'];
		}
		$smarty->assign("spis_stron_kategoria", "kategoria,".$id_kategorii.",".$wynik['prosta_nazwa']);
		$smarty->assign("glowna_opis", htmlspecialchars_decode($wynik['glowna_opis']));
	}else{
		$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nieprawidłowy link do kategorii');	
	}
}else{
	$smarty->assign("glowna_opis", $ustawienia['glowna_opis']);
}

if(isset($_GET['szukaj'])){
	if($_GET['szukaj']!=''){
		$szukaj_array = explode(' ', filtruj($_GET['szukaj']));
		if(count($szukaj_array)){
			$warunek .= ' and ( ';
			for($i=0; $i < count($szukaj_array); $i++){
				$warunek .= ' ('.$prefiks_tabel.'ogloszenia.prosty_tytul like "%'.prosta_nazwa($szukaj_array[$i]).'%" or '.$prefiks_tabel.'ogloszenia.opis like "%'.$szukaj_array[$i].'%") ';
				if($i+1 < count($szukaj_array)){$warunek .= ' or ';}
			}
			$warunek .= ' ) ';
		}
		$ustawienia['title'] = 'Szukaj: '.filtruj($_GET['szukaj']).' - '.$ustawienia['title'];
		$ustawienia['description'] = 'Szukaj: '.filtruj($_GET['szukaj']).'. '.$ustawienia['description'];
	}
	if(isset($_GET['cena_od']) and $_GET['cena_od']!=''){
		$warunek .= ' and '.$prefiks_tabel.'ogloszenia.cena>='.filtruj($_GET['cena_od']).' ';
	}
	if(isset($_GET['cena_do']) and $_GET['cena_do']!=''){
		$warunek .= ' and '.$prefiks_tabel.'ogloszenia.cena<='.filtruj($_GET['cena_do']).' ';
	}
	if(isset($_GET['typ_ogloszenia']) and $_GET['typ_ogloszenia']!=''){
		$typ_id = mysql_fetch_assoc(mysql_query('select id from '.$prefiks_tabel.'typy_ogloszen where prosta_nazwa="'.filtruj($_GET['typ_ogloszenia']).'" limit 1'))['id'];
		$warunek .= ' and '.$prefiks_tabel.'ogloszenia.typ="'.$typ_id.'" ';
	}
	if(isset($_GET['region']) and $_GET['region']!=''){
		$region_id = mysql_fetch_assoc(mysql_query('select id from '.$prefiks_tabel.'regiony where prosta_nazwa="'.filtruj($_GET['region']).'" limit 1'))['id'];
		$warunek .= ' and '.$prefiks_tabel.'ogloszenia.region="'.$region_id.'" ';
	}
	if(isset($_GET['region2']) and $_GET['region2']!=''){
		$region2_id = mysql_fetch_assoc(mysql_query('select id from '.$prefiks_tabel.'regiony2 where prosta_nazwa="'.filtruj($_GET['region2']).'" limit 1'))['id'];
		$warunek .= ' and '.$prefiks_tabel.'ogloszenia.region2="'.$region2_id.'" ';
	}
	if(isset($_GET['lokalizacja']) and $_GET['lokalizacja']!=''){
		$warunek .= ' and ('.$prefiks_tabel.'ogloszenia.lokalizacja like "%'.filtruj($_GET['lokalizacja']).'%" ';
		$region_id = mysql_fetch_assoc(mysql_query('select id from '.$prefiks_tabel.'regiony where prosta_nazwa like "%'.prosta_nazwa(filtruj($_GET['lokalizacja'])).'%" limit 1'))['id'];
		if($region_id){
			$warunek .= ' or '.$prefiks_tabel.'ogloszenia.region="'.$region_id.'" ';
		}
		$warunek .= ' ) '; 
	}
	if(isset($_GET['opcja']) and is_array($_GET['opcja'])){
		$opcje = array_filter($_GET['opcja']);
		foreach($opcje as $key => $value){
			if(is_array($value)){
				if(isset($value['od']) and $value['od']!=''){
					$warunek .= ' and (select wartosc from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id='.$prefiks_tabel.'ogloszenia.id and opcja_id='.filtruj($key).' limit 1) >= '.filtruj($value['od']).' ';
				}
				if(isset($value['do']) and $value['do']!=''){
					$warunek .= ' and (select wartosc from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id='.$prefiks_tabel.'ogloszenia.id and opcja_id='.filtruj($key).' limit 1) <= '.filtruj($value['do']).' ';
				}
				if(isset($value['od_data']) and $value['od_data']!=''){
					$warunek .= ' and (select wartosc from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id='.$prefiks_tabel.'ogloszenia.id and opcja_id='.filtruj($key).' limit 1) >= "'.filtruj($value['od_data']).'" ';
				}
				if(isset($value['do_data']) and $value['do_data']!=''){
					$warunek .= ' and (select wartosc from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id='.$prefiks_tabel.'ogloszenia.id and opcja_id='.filtruj($key).' limit 1) <= "'.filtruj($value['do_data']).'" ';
				}
				if(isset($value['select']) and $value['select']!=''){
					$warunek .= ' and (select wartosc from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id='.$prefiks_tabel.'ogloszenia.id and opcja_id='.filtruj($key).' limit 1) = "'.filtruj($value['select']).'" ';
				}
			}else{
				$warunek .= ' and (select wartosc from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id='.$prefiks_tabel.'ogloszenia.id and opcja_id='.filtruj($key).' limit 1) like "%'.filtruj($value).'%" ';
			}
		}
	}
	if(!isset($nawigacja)){$nawigacja = array();}
	array_unshift($nawigacja,array('nazwa'=>'Wyniki wyszukiwania', 'url'=>'?'.$gets));
}

$sortuj = 'start desc, id desc';
if(isset($_GET['sortuj'])){
	if($_GET['sortuj']=='najstarsze'){
		$sortuj = 'start, id';
	}elseif($_GET['sortuj']=='najtansze'){
		$sortuj = ' ISNULL(cena_sortowanie), cena, id DESC ';	
	}elseif($_GET['sortuj']=='najdrozsze'){
		$sortuj = 'cena desc, id desc';
	}
}

$q = mysql_query('select id, id_uzytkownika, tytul, prosty_tytul, kategoria, promowana, typ, start, cena, IF (cena = 0, NULL, cena) as cena_sortowanie, cena_do_negocjacji, za_darmo, telefon, email from '.$prefiks_tabel.'ogloszenia where '.$prefiks_tabel.'ogloszenia.aktywna=1 '.$warunek.' order by promowana desc, '.$sortuj.' limit '.policz_strony($prefiks_tabel.'ogloszenia',$ustawienia['ile_na_strone'],$prefiks_tabel.'ogloszenia.aktywna=1 '.$warunek).','.$ustawienia['ile_na_strone'].'');

while($dane = mysql_fetch_assoc($q)){
	$dane['login'] = mysql_fetch_assoc(mysql_query('select login from '.$prefiks_tabel.'uzytkownicy where id="'.$dane['id_uzytkownika'].'" limit 1'))['login'];
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

$q = mysql_query('select * from '.$prefiks_tabel.'kategorie where kategoria = "'.$warunek_kategorii.'" union select * from '.$prefiks_tabel.'kategorie where kategoria="'.$nadkategoria.'" AND NOT EXISTS (SELECT id FROM '.$prefiks_tabel.'kategorie where kategoria = "'.$warunek_kategorii.'") order by pozycja');
while($dane = mysql_fetch_assoc($q)){$kategorie[] = $dane;}
if(isset($kategorie)){$smarty->assign("kategorie", $kategorie);}

$q = mysql_query('select * from '.$prefiks_tabel.'typy_ogloszen order by nazwa');
while($dane = mysql_fetch_assoc($q)){$typy_ogloszen[] = $dane;}
if(isset($typy_ogloszen)){$smarty->assign("typy_ogloszen", $typy_ogloszen);}

$q = mysql_query('select * from '.$prefiks_tabel.'regiony order by nazwa');
while($dane = mysql_fetch_assoc($q)){
	$q2 = mysql_query('select * from '.$prefiks_tabel.'regiony2 where region_id="'.$dane['id'].'" order by nazwa');
	while($dane2 = mysql_fetch_assoc($q2)){
		$regiony2[$dane['prosta_nazwa']][] = $dane2;
	}
	$regiony[] = $dane;
}
if(isset($regiony)){$smarty->assign("regiony", $regiony);}
if(isset($regiony2)){$smarty->assign("regiony2", $regiony2);}

$q = mysql_query('select tresc from '.$prefiks_tabel.'slider where aktywne=1');
while($dane = mysql_fetch_assoc($q)){
	$dane['tresc'] = htmlspecialchars_decode($dane['tresc']);
	$slider[] = $dane;
}
if(isset($slider)){$smarty->assign("slider", $slider);}

$q = mysql_query('select id, nazwa, rodzaj, opcje_wyboru from '.$prefiks_tabel.'opcje_dodatkowe where wszystkie="1" or kategorie like "%-'.$warunek_kategorii.'-%" order by pozycja');
while($dane = mysql_fetch_assoc($q)){
	if($dane['id']){
		if($dane['rodzaj']=='select'){
			$dane['opcje_wyboru_explode'] = array_map('trim', explode(',',$dane['opcje_wyboru']));
		}
		$opcje_dodatkowe[] = $dane;
	}else{
		$smarty->assign("wyszukiwarka_pokaz_cene", true);
	}
}
if(isset($opcje_dodatkowe)){$smarty->assign("opcje_dodatkowe", $opcje_dodatkowe);}

pobierz_slider_dol();
