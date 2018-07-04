<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

$prawidlowa_sesja = true;
if(isset($_POST['akcja'])){
	if(isset($_POST['kod_sesji_dodawania'])){
		$kod_sesji_dodawania = filtruj($_POST['kod_sesji_dodawania']);
		if(mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'sesje_dodawania where kod="'.$kod_sesji_dodawania.'" and ip="'.get_client_ip().'" and data < (NOW() - INTERVAL 4 SECOND) limit 1'))){
			mysql_query('delete from '.$prefiks_tabel.'sesje_dodawania where kod="'.$kod_sesji_dodawania.'" and ip="'.get_client_ip().'"');
			if(in_array(get_client_ip(),array_map('trim', array_filter(explode(PHP_EOL, $ustawienia['czarna_lista_ip']))))){
				$infobox[] = array('klasa'=>'czerwona','tresc'=>'Ogłoszenie nie mogło zostać dodane');
				$prawidlowa_sesja = false;
			}
		}else{
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nieprawidłowy kod sesji');
			$prawidlowa_sesja = false;
		}
	}else{
		$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nieprawidłowy kod sesji');
		$prawidlowa_sesja = false;
	}
}

$q = mysql_query('select id, nazwa, prosta_nazwa from '.$prefiks_tabel.'kategorie where kategoria="0" order by pozycja');
while($dane = mysql_fetch_assoc($q)){$kategorie[] = $dane;}
if(isset($kategorie)){$smarty->assign("kategorie", $kategorie);}

$q = mysql_query('select id, nazwa from '.$prefiks_tabel.'kategorie order by kategoria, pozycja');
while($dane = mysql_fetch_assoc($q)){
	$q2 = mysql_query('select id, nazwa from '.$prefiks_tabel.'kategorie where kategoria="'.$dane['id'].'" order by kategoria, pozycja');
	while($dane2 = mysql_fetch_assoc($q2)){$dane['podkategorie'][] = $dane2;}
	if(isset($dane['podkategorie'])){$podkategorie[] = $dane;}
}
if(isset($podkategorie)){$smarty->assign("podkategorie", $podkategorie);}

$q = mysql_query('select id, nazwa from '.$prefiks_tabel.'typy_ogloszen order by nazwa');
while($dane = mysql_fetch_assoc($q)){$typy_ogloszen[] = $dane;}
if(isset($typy_ogloszen)){$smarty->assign("typy_ogloszen", $typy_ogloszen);}

$q = mysql_query('select * from '.$prefiks_tabel.'regiony order by nazwa');
while($dane = mysql_fetch_assoc($q)){
	$q2 = mysql_query('select * from '.$prefiks_tabel.'regiony2 where region_id="'.$dane['id'].'" order by nazwa');
	while($dane2 = mysql_fetch_assoc($q2)){
		$regiony2[$dane['id']][] = $dane2;
	}
	$regiony[] = $dane;
}
if(isset($regiony)){$smarty->assign("regiony", $regiony);}
if(isset($regiony2)){$smarty->assign("regiony2", $regiony2);}

$q = mysql_query('select * from '.$prefiks_tabel.'czas_ogloszen order by dlugosc');
while($dane = mysql_fetch_assoc($q)){$czas_ogloszen[] = $dane;}
if(isset($czas_ogloszen)){$smarty->assign("czas_ogloszen", $czas_ogloszen);}

$q = mysql_query('select * from '.$prefiks_tabel.'opcje_dodatkowe order by pozycja');
while($dane = mysql_fetch_assoc($q)){
	if($dane['rodzaj']=='select'){
		$dane['opcje_wyboru_explode'] = array_filter(array_map('trim', explode(',',$dane['opcje_wyboru'])));
	}
	$dane['kategorie_explode'] = array_filter(explode(',',str_replace(array('--','-'),',',$dane['kategorie'])));
	if($dane['id']){
		$opcje_dodatkowe[] = $dane;
	}else{
		$opcje_cena = $dane;
	}
}
if(isset($opcje_dodatkowe)){$smarty->assign("opcje_dodatkowe", $opcje_dodatkowe);}
$smarty->assign("cena", $opcje_cena);

if(!isset($uzytkownik)){
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nie jesteś zalogowany w serwisie - nie będziesz miał możliwości edycji ogłoszenia');
}
	
if($prawidlowa_sesja and (isset($uzytkownik) or $ustawienia['ogloszenia_bez_rejestracji'])){
	
	if(isset($_POST['akcja']) and ($_POST['akcja']=='dodaj_ogloszenie' or ($_POST['akcja']=='edytuj_ogloszenie' and isset($_GET['id']))) and isset($_POST['tytul']) and $_POST['tytul']!='' and isset($_POST['opis']) and isset($_POST['lokalizacja']) and isset($_POST['email']) and $_POST['email']!='' and isset($_POST['telefon']) and isset($_POST['czas_ogloszenia']) and (!isset($kategorie) or isset($_POST['kategoria'])) and (!isset($typy_ogloszen) or isset($_POST['typ']))){

		$tytul = strip_tags(filtruj($_POST['tytul']));
		$prosty_tytul = prosta_nazwa($tytul);
		$opis = trim(htmlspecialchars(nofollow(purify($_POST['opis']))));
		if(isset($_POST['kategoria'])){$kategoria=filtruj($_POST['kategoria']);}else{$kategoria=0;}
		if(isset($_POST['slider'])){$slider=1;}else{$slider=0;}
		if(isset($_POST['promowana'])){$promowana=1;}else{$promowana=0;}
		if(isset($_POST['typ'])){$typ=filtruj($_POST['typ']);}else{$typ=0;}
		$cena = filtruj($_POST['cena']);
		if(isset($_POST['cena_do_negocjacji'])){$cena_do_negocjacji=1;}else{$cena_do_negocjacji=0;}
		if(isset($_POST['za_darmo'])){$za_darmo=1;$cena=0;}else{$za_darmo=0;}
		if(isset($_POST['stan'])){$stan=filtruj($_POST['stan']);}else{$stan=0;}
		if($ustawienia['pokaz_gmaps'] and isset($_POST['mapa_google'])){$mapa_google=1;}else{$mapa_google=0;}
		if(isset($_POST['region'])){$region=filtruj($_POST['region']);}else{$region=0;}
		if(isset($_POST['region2'])){$region2=filtruj($_POST['region2']);}else{$region2=0;}
		$email = strip_tags(filtruj($_POST['email']));
		if(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'czarna_lista where email="'.$email.'" limit 1'))){
			if(isset($uzytkownik)){
				$email = $uzytkownik['email'];
			}else{
				header("Location: index.php?ogloszenie_nie_dodano");
				die('Ogłoszenie nie mogło zostać dodane');
			}
			$zmieniono_email = true;
		}
		if(isset($_POST['formularz_kontaktowy'])){$formularz_kontaktowy=1;}else{$formularz_kontaktowy=0;}
		$czas_ogloszenia = filtruj($_POST['czas_ogloszenia']);
		$start = date("Y-m-d H:i:s");

		if($_POST['akcja']=='dodaj_ogloszenie' and isset($_POST['kod_sesji_dodawania'])){
			
			if($czas_ogloszenia==0){
				$czas_trwania = $ustawienia['domyslny_czas'];
				$koniec = date('Y-m-d', strtotime($start. ' + '.intval($ustawienia['domyslny_czas']).' days'));
			}else{
				$czas_trwania = mysql_fetch_assoc(mysql_query('select dlugosc from '.$prefiks_tabel.'czas_ogloszen where id="'.$czas_ogloszenia.'" limit 1'));
				$koniec = date('Y-m-d', strtotime($start. ' + '.intval($czas_trwania['dlugosc']).' days'));
			}
			
			if(isset($uzytkownik)){$id_uzytkownika=$uzytkownik['id'];}else{$id_uzytkownika=0;}
			
			mysql_query('INSERT INTO `'.$prefiks_tabel.'ogloszenia`(`tytul`, `prosty_tytul`, `id_uzytkownika`, `slider`, `kategoria`, `promowana`, `typ`, `start`, `koniec`, `czas_ogloszenia`, `cena`, `cena_do_negocjacji`, `za_darmo`, `opis`, `stan`, `lokalizacja`, `mapa_google`, `region`, `region2`, `email`, `telefon`, `formularz_kontaktowy`, `ip`, `data`) VALUES ("'.$tytul.'", "'.$prosty_tytul.'", "'.$id_uzytkownika.'", "'.$slider.'", "'.$kategoria.'", "'.$promowana.'", "'.$typ.'", "'.$start.'", "'.$koniec.'", "'.$czas_ogloszenia.'", "'.$cena.'", "'.$cena_do_negocjacji.'", "'.$za_darmo.'", "'.$opis.'", "'.$stan.'", "'.strip_tags(filtruj($_POST['lokalizacja'])).'", "'.$mapa_google.'", "'.$region.'", "'.$region2.'", "'.$email.'", "'.strip_tags(filtruj($_POST['telefon'])).'", "'.$formularz_kontaktowy.'", "'.get_client_ip().'", NOW())');
			
			$id_ogloszenia = mysql_insert_id();
			
			if(isset($_POST['opcje_dodatkowe'])){
				foreach($_POST['opcje_dodatkowe'] as $key=>$value){
					if($value!=''){
						mysql_query('INSERT INTO `'.$prefiks_tabel.'wartosci_opcji`(`ogloszenie_id`, `opcja_id`, `wartosc`) VALUES ("'.$id_ogloszenia.'", "'.filtruj($key).'", "'.filtruj($value).'")');
					}
				}
			}

		}else{
			
			$id_ogloszenia = filtruj($_GET['id']);
			
			$dane_ogloszenia = mysql_fetch_assoc(mysql_query('select start, aktywna, id_uzytkownika from '.$prefiks_tabel.'ogloszenia where id="'.$id_ogloszenia.'" limit 1'));
			if(!isset($uzytkownik) or ($dane_ogloszenia['id_uzytkownika']!=$uzytkownik['id']) and !$uzytkownik['admin']){
				die('dostep wzbroniony');
			}
			
			if($dane_ogloszenia['aktywna']){
				$start = $dane_ogloszenia['start'];
			}
			
			$koniec = date('Y-m-d', strtotime($start. ' + '.intval($ustawienia['domyslny_czas']).' days'));
			if($czas_ogloszenia>0){
				$czas_trwania = mysql_fetch_assoc(mysql_query('select dlugosc from '.$prefiks_tabel.'czas_ogloszen where id="'.$czas_ogloszenia.'" limit 1'));
				if($czas_trwania){
					$koniec = date('Y-m-d', strtotime($start. ' + '.intval($czas_trwania['dlugosc']).' days'));
				}
			}
		
			mysql_query('UPDATE `'.$prefiks_tabel.'ogloszenia` SET `tytul`="'.$tytul.'", `prosty_tytul`="'.$prosty_tytul.'", `kategoria`="'.$kategoria.'", `slider`="'.$slider.'", `promowana`="'.$promowana.'", `typ`="'.$typ.'", `start`="'.$start.'", `koniec`="'.$koniec.'", `czas_ogloszenia`="'.$czas_ogloszenia.'", `cena`="'.$cena.'", `cena_do_negocjacji`="'.$cena_do_negocjacji.'", `za_darmo`="'.$za_darmo.'", `opis`="'.$opis.'", `stan`="'.$stan.'",`lokalizacja`="'.strip_tags(filtruj($_POST['lokalizacja'])).'", `mapa_google`="'.$mapa_google.'", `region`="'.$region.'", `region2`="'.$region2.'", `email`="'.$email.'", `telefon`="'.strip_tags(filtruj($_POST['telefon'])).'", formularz_kontaktowy="'.$formularz_kontaktowy.'" WHERE `id`="'.$id_ogloszenia.'" limit 1');
			
			mysql_query('delete from `'.$prefiks_tabel.'wartosci_opcji` where `ogloszenie_id`="'.$id_ogloszenia.'"');
			if(isset($_POST['opcje_dodatkowe'])){
				foreach($_POST['opcje_dodatkowe'] as $key=>$value){
					if($value!=''){
						mysql_query('INSERT INTO `'.$prefiks_tabel.'wartosci_opcji`(`ogloszenie_id`, `opcja_id`, `wartosc`) VALUES ("'.$id_ogloszenia.'", "'.filtruj($key).'", "'.filtruj($value).'")');
					}
				}
			}
			
			if($ustawienia['zezwalaj_zdjecia'] and isset($_POST['zdjecia_edytuj']) and $_POST['zdjecia_edytuj']!=''){
				$zdjecia_edytuj = 'id!='.implode($_POST['zdjecia_edytuj'],' and id!=');
			}else{
				$zdjecia_edytuj = 'true';
			}
			$q = mysql_query('select miniaturka, url from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$id_ogloszenia.'" and ('.$zdjecia_edytuj.')');
			while($dane = mysql_fetch_assoc($q)){
				unlink($ustawienia['upload'].'/'.$dane['miniaturka']);
				unlink($ustawienia['upload'].'/'.$dane['url']);
			}
			mysql_query('delete from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$id_ogloszenia.'" and ('.$zdjecia_edytuj.')');
			
			if($ustawienia['zezwalaj_zdjecia'] and isset($_POST['zdjecia_edytuj']) and $_POST['zdjecia_edytuj']!=''){
				$q = mysql_query('select id, miniaturka, url from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$id_ogloszenia.'"');
				while($dane = mysql_fetch_assoc($q)){
					$zdjecia_zmiana_kolejnosci[$dane['id']] = $dane;
					$zdjecia_zmiana_kolejnosci_kolejne[] = $dane['id'];
				}
				foreach($_POST['zdjecia_edytuj'] as $key => $value){
					mysql_query('update '.$prefiks_tabel.'zdjecia set miniaturka="'.$zdjecia_zmiana_kolejnosci[$value]['miniaturka'].'", url="'.$zdjecia_zmiana_kolejnosci[$value]['url'].'" where id="'.$zdjecia_zmiana_kolejnosci_kolejne[$key].'" limit 1');
				}
			}
		}
		
		if($ustawienia['zezwalaj_zdjecia'] and isset($_POST['zdjecia']) and $_POST['zdjecia']!=''){
			foreach($_POST['zdjecia'] as $key => $value){
				if(isset($uzytkownik)){
					$zdjecie = mysql_fetch_assoc(mysql_query('select id, miniaturka, url from '.$prefiks_tabel.'zdjecia_temp where id="'.filtruj($value).'" and id_uzytkownika="'.$uzytkownik['id'].'" limit 1'));
				}else{
					$zdjecie = mysql_fetch_assoc(mysql_query('select id, miniaturka, url from '.$prefiks_tabel.'zdjecia_temp where id="'.filtruj($value).'" limit 1'));
				}
				if($zdjecie!=''){
					mysql_query('INSERT INTO `'.$prefiks_tabel.'zdjecia`(`id_ogloszenia`, `miniaturka`, `url`) VALUES ("'.$id_ogloszenia.'", "'.$zdjecie['miniaturka'].'", "'.$zdjecie['url'].'")');
					mysql_query('delete from '.$prefiks_tabel.'zdjecia_temp where id="'.$zdjecie['id'].'" limit 1');
				}
			}
		}

		if(isset($zmieniono_email)){
			header("Location: ".$id_ogloszenia.','.$prosty_tytul.'?podglad&zmieniono_email');
			die('Przekierowanie...');
		}else{
			header("Location: ".$id_ogloszenia.','.$prosty_tytul.'?podglad');
			die('Przekierowanie...');
		}
	}

	if(isset($_GET['akcja']) and isset($_GET['id'])){
		
		$id_ogloszenia = filtruj($_GET['id']);
		
		if($uzytkownik['admin']){
			$ogloszenie = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'ogloszenia where id="'.$id_ogloszenia.'" limit 1'));
		}else{
			$ogloszenie = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'ogloszenia where id="'.$id_ogloszenia.'" and id_uzytkownika="'.$uzytkownik['id'].'" limit 1'));
		}
		if($ogloszenie!=''){

			$nadkategoria = $ogloszenie['kategoria'];
			while($nadkategoria!=0){
				$q = mysql_query('select id, kategoria from '.$prefiks_tabel.'kategorie where id = "'.$nadkategoria.'"');
				if(mysql_num_rows($q)){
					while($dane = mysql_fetch_assoc($q)){
						$nadkategoria = $dane['kategoria'];
						$ogloszenie['nadkategorie'][] = $dane['id'];
					}
				}else{
					$nadkategoria = 0;
					$ogloszenie['nadkategorie'] = array();
				}
			}
		
			if($_GET['akcja']=='edytuj'){
				
				$q = mysql_query('select id, miniaturka, url from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$ogloszenie['id'].'"');
				while($dane = mysql_fetch_assoc($q)){$zdjecia[] = $dane;}
				if(isset($zdjecia)){$smarty->assign("zdjecia", $zdjecia);}
				
				$ogloszenie['akcja'] = 'edycja';
				$nawigacja[] = array('url'=>'moje_ogloszenia', 'nazwa'=>'Moje ogłoszenia');
				$nawigacja[] = array('url'=>'edytuj,'.$ogloszenie['id'].','.$ogloszenie['prosty_tytul'], 'nazwa'=>'Edytuj ogłoszenie');
				
			}else{
				$ogloszenie['akcja'] = 'dodaj';
				$nawigacja[] = array('url'=>'dodaj', 'nazwa'=>'Dodaj ogłoszenie');
			}
			
			$q = mysql_query('select opcja_id, wartosc from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id="'.$ogloszenie['id'].'"');
			while($dane = mysql_fetch_assoc($q)){
				$ogloszenie['opcje_dodatkowe'][$dane['opcja_id']] = $dane['wartosc'];
			}

			$smarty->assign("ogloszenie", $ogloszenie);
			
		}elseif($_GET['akcja']=='edytuj'){
			
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nieprawidłowy link do edycji ogłoszenia lub nie jesteś zalogowany na właściwe konto');
			$strona = '404';
			include('model/404.php');
		}
	}else{
		$nawigacja[] = array('url'=>'dodaj', 'nazwa'=>'Dodaj ogłoszenie');
	}
}

$kod_sesji_dodawania = md5(uniqid(rand(), true));
if(isset($uzytkownik)){$id_uzytkownika=$uzytkownik['id'];}else{$id_uzytkownika=0;}
mysql_query('INSERT INTO `'.$prefiks_tabel.'sesje_dodawania`(`id_uzytkownika`, `kod`, `ip`, `data`) VALUES ("'.$id_uzytkownika.'", "'.$kod_sesji_dodawania.'", "'.get_client_ip().'", NOW())');
$smarty->assign("kod_sesji_dodawania", $kod_sesji_dodawania);
