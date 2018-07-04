<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

$id_ogloszenia = filtruj($_GET['id']);

if(isset($uzytkownik) and $uzytkownik['admin'] and isset($_GET['podglad']) and isset($_GET['tabela']) and $_GET['tabela'] == 'archiwum'){
	$tabela = 'archiwum_ogloszenia';
	$archiwum = true;
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Ogłoszenie znajduje się w archiwum. Będąć zalogowanym na konto admin możesz je podejrzeć');
}else{
	$tabela = 'ogloszenia';
	$archiwum = false;
}

$ogloszenie = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.$tabela.' where id="'.$id_ogloszenia.'" limit 1'));
if($ogloszenie!=''){
	
	if(isset($_POST['akcja']) and $_POST['akcja']=='dodaj_schowek'){
		if(isset($uzytkownik)){
			if(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'schowek where id_uzytkownika="'.$uzytkownik['id'].'" and id_ogloszenia="'.$id_ogloszenia.'" limit 1'))){
				$infobox[] = array('klasa'=>'zielona','tresc'=>'Już dodałeś to ogłoszenie do <a href="schowek" title="Schowek z ogłoszeniami">schowka</a>!');
			}else{
				mysql_query('INSERT INTO `'.$prefiks_tabel.'schowek`(`id_uzytkownika`, `id_ogloszenia`) VALUES ("'.$uzytkownik['id'].'", "'.$id_ogloszenia.'")');
				$infobox[] = array('klasa'=>'zielona','tresc'=>'Ogłoszenie zostało poprawnie dodane do <a href="schowek" title="Schowek z ogłoszeniami">schowka</a>');
			}
		}else{
			header("Location: logowanie?redirect=".$id_ogloszenia.','.$ogloszenie['prosty_tytul']);
		}
	}
	
	$podglad = $podglad_admin = false;
	
	if(isset($_GET['podglad']) and (isset($uzytkownik) or $ogloszenie['id_uzytkownika']==0)){
		if($ogloszenie['id_uzytkownika']==0 or $ogloszenie['id_uzytkownika'] == $uzytkownik['id'] or $uzytkownik['admin']){
			$podglad = true;
		}elseif($ogloszenie['aktywna']==0 and $uzytkownik['admin']){
			$podglad_admin = true;
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Ogłoszenie nie jest aktywne. Będąć zalogowanym na konto admin możesz je podejrzeć');
		}
		if(isset($_GET['zmieniono_email'])){
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Adres email podany w ogłoszeniu był nieprawidłowy - został zmieniony na '.$ogloszenie['email']);
		}
	}
	
	if($ogloszenie['aktywna'] or $podglad or $podglad_admin){
		
		if($podglad and !$archiwum){
			
			list($oplacona,$oplata,$do_oplacenia,$zostalo_do_zaplacenia) = policz_koszt_ogloszenia($id_ogloszenia, $tabela);
			
			if($oplacona and isset($_GET['zatwierdz'])){
				
				mysql_query('update '.$prefiks_tabel.'ogloszenia set oplacona=1 where id="'.$ogloszenie['id'].'" limit 1');
				$ogloszenie['oplacona'] = 1;
				
				if($ogloszenie['aktywna']==0){
					mysql_query('update '.$prefiks_tabel.'ogloszenia set aktywna=1 where id="'.$ogloszenie['id'].'" limit 1');
					$ogloszenie['aktywna'] = 1;
					$infobox[] = array('klasa'=>'zielona','tresc'=>'Ogłoszenie zostało poprawnie aktywowane w systemie');
					wyslij_mail('start','',array('id'=>$ogloszenie['id']));
					policz_podkategorie();
				}
			}elseif(isset($_GET['status'])){
				if($_GET['status']=='OK' || $_GET['status']=='ok' || $_GET['status']=='payu'){ 
					if(isset($_GET['error']) and $_GET['error']=='501'){
						$infobox[] = array('klasa'=>'czerwona','tresc'=>'Błąd płatności. Skontaktuj się z administratorem systemu: <a href="mailto:'.$ustawienia['email'].'">'.$ustawienia['email'].'</a>');
					}else{
						$infobox[] = array('klasa'=>'zielona','tresc'=>'Ogłoszenie zostanie aktywowane w ciągu kilku minut po zaksięgowaniu wpłaty');
						$infobox[] = array('klasa'=>'zielona','tresc'=>'Płatność została poprawnie przyjęta');	
					}			
				}elseif($_GET['status']=='FAIL' || $_GET['status']=='error'){
					$infobox[] = array('klasa'=>'czerwona','tresc'=>'Błąd płatności. Skontaktuj się z administratorem systemu: <a href="mailto:'.$ustawienia['email'].'">'.$ustawienia['email'].'</a>');
					if(!$ogloszenie['aktywna']){
						$infobox[] = array('klasa'=>'czerwona','tresc'=>'Ogłoszenie nie jest jeszcze aktywne w systemie');
					}
					if($ustawienia['platnosc_tpay']){
						$smarty->assign("tpay_md5sum",md5($ustawienia['tpay_id'] . $zostalo_do_zaplacenia . $ogloszenie['id'] . $ustawienia['tpay_kod']));
					}
					$smarty->assign("podglad", array('oplata'=>$oplata, 'do_oplacenia'=>$do_oplacenia, 'zostalo_do_zaplacenia'=>$zostalo_do_zaplacenia,'oplacona'=>$oplacona));
				}elseif($_GET['status']=='przelewy24'){
					if($ogloszenie['aktywna']){
						$infobox[] = array('klasa'=>'zielona','tresc'=>'Dziękujemy za dokonanie płatności. Ogłoszenie zostało aktywowane w systemie');
					}else{
						$infobox[] = array('klasa'=>'zielona','tresc'=>'Dziękujemy za dokonanie płatności. Po zatwierdzeniu ogłoszenie zostanie aktywowane.');
						$infobox[] = array('klasa'=>'czerwona','tresc'=>'Ogłoszenie nie jest jeszcze aktywne w systemie');
					}
				}
			}else{
				if($ogloszenie['oplacona'] and !$oplacona){
					mysql_query('update '.$prefiks_tabel.'ogloszenia set aktywna=0, oplacona=0 where id="'.$ogloszenie['id'].'" limit 1');
					$ogloszenie['aktywna'] = $ogloszenie['oplacona'] = 0;
				}
				if($ustawienia['platnosc_tpay']){
					$smarty->assign("tpay_md5sum",md5($ustawienia['tpay_id'] . $zostalo_do_zaplacenia . $ogloszenie['id'] . $ustawienia['tpay_kod']));
				}
				if(!$ogloszenie['aktywna']){
					$infobox[] = array('klasa'=>'czerwona','tresc'=>'Ogłoszenie nie jest jeszcze aktywne w systemie');
				}
				$smarty->assign("podglad", array('oplata'=>$oplata, 'do_oplacenia'=>$do_oplacenia, 'zostalo_do_zaplacenia'=>$zostalo_do_zaplacenia,'oplacona'=>$oplacona));
			}

		}else{
			if(isset($uzytkownik)){$id_uzytkownika=$uzytkownik['id'];}else{$id_uzytkownika=0;}
			if(!$archiwum ){
				mysql_query('INSERT INTO `'.$prefiks_tabel.'logi_wyswietlenia`(`id_ogloszenia`, `id_uzytkownika`, `ip`, `data`) VALUES ("'.$id_ogloszenia.'","'.$id_uzytkownika.'", "'.get_client_ip().'", "'.date("Y-m-d H:i:s").'")');
			}
		}
		
		$q = mysql_query('select id, nazwa from '.$prefiks_tabel.'opcje_dodatkowe order by pozycja');
		while($dane = mysql_fetch_assoc($q)){$opcje_dodatkowe[$dane['id']] = $dane['nazwa'];}

		if($archiwum){
			$ogloszenie['opcje_dodatkowe'] = json_decode($ogloszenie['opcje_dodatkowe'], true);
			if(is_array($ogloszenie['opcje_dodatkowe'])){
				foreach($ogloszenie['opcje_dodatkowe'] as $key=>$value){
					if(isset($opcje_dodatkowe[$key])){
						$ogloszenie['opcje_dodatkowe'][$opcje_dodatkowe[$key]] = $ogloszenie['opcje_dodatkowe'][$key];
					}
					unset($ogloszenie['opcje_dodatkowe'][$key]);
				}
			}
		}else{
			$q = mysql_query('select opcja_id, wartosc from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id="'.$ogloszenie['id'].'"');
			while($dane = mysql_fetch_assoc($q)){
				$ogloszenie['opcje_dodatkowe'][$opcje_dodatkowe[$dane['opcja_id']]] = $dane['wartosc'];
			}
		}
	
		if($ogloszenie['id_uzytkownika']){
			$dane_uzytkownika = mysql_fetch_assoc(mysql_query('select id, login, awatar from '.$prefiks_tabel.'uzytkownicy where id="'.$ogloszenie['id_uzytkownika'].'" limit 1'));
			if($dane_uzytkownika){
				$ogloszenie['uzytkownik'] = $dane_uzytkownika['login'];
				$ogloszenie['uzytkownik_oceny'] = pobierz_oceny_profilu($dane_uzytkownika['id']);
				$ogloszenie['awatar'] = $dane_uzytkownika['awatar'];
			}
		}
		if(!$archiwum and !$ustawienia['ukryj_liczbe_wyswietlen']){
			$ogloszenie['ile_wyswietlen'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'logi_wyswietlenia where id_ogloszenia="'.$id_ogloszenia.'"'));
		}
		$ogloszenie['typ_nazwa'] = mysql_fetch_assoc(mysql_query('select nazwa from '.$prefiks_tabel.'typy_ogloszen where id="'.$ogloszenie['typ'].'" limit 1'))['nazwa'];
		$kategoria = mysql_fetch_assoc(mysql_query('select nazwa, prosta_nazwa from '.$prefiks_tabel.'kategorie where id="'.$ogloszenie['kategoria'].'" limit 1'));
		$ogloszenie['kategoria_nazwa'] = $kategoria['nazwa'];
		$ogloszenie['kategoria_prosta_nazwa'] = $kategoria['prosta_nazwa'];
		$ogloszenie['region_nazwa'] = mysql_fetch_assoc(mysql_query('select nazwa from '.$prefiks_tabel.'regiony where id="'.$ogloszenie['region'].'" limit 1'))['nazwa'];
		$ogloszenie['region2_nazwa'] = mysql_fetch_assoc(mysql_query('select nazwa from '.$prefiks_tabel.'regiony2 where id="'.$ogloszenie['region2'].'" limit 1'))['nazwa'];
		$ogloszenie['opis'] = htmlspecialchars_decode($ogloszenie['opis']);
		$smarty->assign("ogloszenie", $ogloszenie);
		
		$q = mysql_query('select miniaturka, url from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$id_ogloszenia.'"');
		while($dane = mysql_fetch_assoc($q)){$zdjecia[] = $dane;}
		if(isset($zdjecia)){
			$ustawienia['logo_facebook'] = '/'.$ustawienia['upload'].'/'.$zdjecia[0]['url'];
			$smarty->assign("zdjecia", $zdjecia);
		}

		$nawigacja_nadkategoria = $ogloszenie['kategoria'];
		while($nawigacja_nadkategoria!=0){
			$q = mysql_query('select id, nazwa, prosta_nazwa, kategoria from '.$prefiks_tabel.'kategorie where id = "'.$nawigacja_nadkategoria.'"');
			if(mysql_num_rows($q)){
				while($dane = mysql_fetch_assoc($q)){
					$nawigacja_nadkategoria = $dane['kategoria'];
					$dane['url'] = 'kategoria,'.$dane['id'].','.$dane['prosta_nazwa'];
					$nawigacja[] = $dane;
				}
			}else{
				$nawigacja_nadkategoria = 0;
			}
		}
		if(isset($nawigacja)){
			$nawigacja = array_reverse($nawigacja);
		}
		$nawigacja[] = array('url'=>$id_ogloszenia.','.$ogloszenie['prosty_tytul'],'nazwa'=>$ogloszenie['tytul']);
		
		$ustawienia['title'] = $ogloszenie['tytul']." - ".$ustawienia['title'];
		$ustawienia['description'] = substr(trim(preg_replace('/\s\s+/', ' ', strip_tags($ogloszenie['opis']))),0,200);
		
	}else{
		
		$infobox[] = array('klasa'=>'czerwona','tresc'=>'Ogłoszenie nie jest aktywne');
		$strona = '404';
		include('model/404.php');
	}
}else{
	
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nieprawidłowy link. Ogłoszenie nie istnieje w systemie lub zostało przeniesione do archiwum');
	$strona = '404';
	include('model/404.php');
	
}

pobierz_slider_dol();

