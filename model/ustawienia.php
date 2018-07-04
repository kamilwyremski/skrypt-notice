<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($uzytkownik)){
	
	if(isset($_POST['akcja']) and $_POST['akcja']=='zmiana_kontaktu' and isset($_POST['lokalizacja']) and isset($_POST['telefon'])){
		
		if(isset($_POST['region']) and $_POST['region']!=''){$region = filtruj($_POST['region']);}else{$region=0;}
		if(isset($_POST['region2']) and $_POST['region2']!=''){$region2 = filtruj($_POST['region2']);}else{$region2=0;}
		mysql_query('update '.$prefiks_tabel.'uzytkownicy set lokalizacja="'.strip_tags(filtruj($_POST['lokalizacja'])).'", region="'.$region.'", region2="'.$region2.'", telefon="'.strip_tags(filtruj($_POST['telefon'])).'", dane_faktura="'.strip_tags(filtruj($_POST['dane_faktura'])).'" where id="'.$uzytkownik['id'].'" limit 1');
		
		$infobox[] = array('klasa'=>'zielona','tresc'=>'Dane do kontaktu zostały pomyślnie zmienione');
		
		if(isset($_FILES["awatar"]["type"]) and $_FILES["awatar"]["type"]!=''){
			
			$sciezka = $ustawienia['upload'].'/';
		
			$extension = substr(strrchr($_FILES['awatar']['name'], "."), 1); 
			$stara_nazwa = substr(prosta_nazwa($_FILES['awatar']['name']),0,100);
			if(file_exists($sciezka.$stara_nazwa)) {  
				$img = explode('.', $_FILES['awatar']['name']);
				$nazwa = substr(prosta_nazwa($img[0],0,100)).'_'.time().'.'.$extension;
			}else{
				$nazwa = $stara_nazwa;
			}
			
			if(!in_array($extension , array('jpg','jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'))){
				$infobox[] = array('klasa'=>'czerwona','tresc'=>'Awatar nie został zmieniony. Nieprawidłowy typ pliku');
			}elseif($_FILES["awatar"]["size"] > $ustawienia['rozmiar_zdjec']*1024){
				$infobox[] = array('klasa'=>'czerwona','tresc'=>'Awatar nie został zmieniony. Za duży rozmiar pliku');
			}else{
				
				move_uploaded_file($_FILES['awatar']['tmp_name'], $sciezka.$nazwa);
				
				if($uzytkownik['awatar']!=''){
					unlink($ustawienia['upload'].'/'.$uzytkownik['awatar']);
				}

				mysql_query('update '.$prefiks_tabel.'uzytkownicy set awatar="'.$nazwa.'" where id="'.$uzytkownik['id'].'" limit 1');
				
				$infobox[] = array('klasa'=>'zielona','tresc'=>'Awatar został zmieniony');
			}
		}
		
		logowanie();
		
	}elseif(isset($_POST['akcja']) and $_POST['akcja']=='zmiana_hasla' and isset($_POST['stare_haslo']) and isset($_POST['nowe_haslo']) and isset($_POST['powtorz_nowe_haslo'])){
		
		if(md5($_POST['stare_haslo'].$password_salt)!=$uzytkownik['haslo']){
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Hasło nie zostało zmienione. Podałeś nieprawidłowe stare hasło');
		}else{
			if($_POST['nowe_haslo']!=$_POST['powtorz_nowe_haslo']){
				$infobox[] = array('klasa'=>'czerwona','tresc'=>'Hasło nie zostało zmienione. Wpisane nowe hasła są różne');
			}elseif(strlen($_POST['nowe_haslo'])>32){
				$infobox[] = array('klasa'=>'czerwona','tresc'=>'Hasło nie zostało zmienione. Nowe hasło jest za długie');
			}elseif(strlen($_POST['nowe_haslo'])==0){
				$infobox[] = array('klasa'=>'czerwona','tresc'=>'Hasło nie zostało zmienione. Nowe hasło jest za krótkie');
			}else{
				mysql_query('update '.$prefiks_tabel.'uzytkownicy set haslo="'.md5($_POST['nowe_haslo'].$password_salt).'" where id="'.$uzytkownik['id'].'" limit 1');
				logowanie();
				$infobox[] = array('klasa'=>'zielona','tresc'=>'Hasło zostało prawidłowo zmienione');
			}
		}
		
	}
	
	$uzytkownik['ile_ogloszen'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'ogloszenia where aktywna=1 and id_uzytkownika="'.$uzytkownik['id'].'"'));
	$uzytkownik['ile_logowan'] = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'logi_uzytkownicy where id_uzytkownika="'.$uzytkownik['id'].'"'));
	$uzytkownik['ostatnie_logowanie'] = mysql_fetch_array(mysql_query('select data from '.$prefiks_tabel.'logi_uzytkownicy where id_uzytkownika="'.$uzytkownik['id'].'" order by data desc limit 1,1'))['data'];
	$smarty->assign("uzytkownik", $uzytkownik);	
	
	$nawigacja[] = array('url'=>'ustawienia', 'nazwa'=>'Ustawienia konta');
	
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
	
}else{
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Musisz być zalogowany aby zobaczyć tą stronę');
	$strona = '404';
	include('model/404.php');
}

pobierz_slider_dol();
