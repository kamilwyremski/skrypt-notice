<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($uzytkownik)){
	
	if($ustawienia['program_partnerski']){
		
		if(isset($_POST['akcja']) and $_POST['akcja']=='pp_wyplata_srodkow' and isset($_POST['pp_imie']) and $_POST['pp_imie']!='' and isset($_POST['pp_adres']) and isset($_POST['pp_numer_konta']) and $_POST['pp_numer_konta']!='' and isset($_POST['kwota']) and $_POST['kwota']>0){
			
			$kwota = floatval($_POST['kwota']);
			if(!mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'pp_wyplaty where id_uzytkownika="'.$uzytkownik['id'].'" and status="oczekujacy" limit 1'))){
				
				$suma_prowizji = mysql_fetch_assoc(mysql_query('select sum(prowizja) as suma from '.$prefiks_tabel.'pp_prowizje where id_polecajacego="'.$uzytkownik['id'].'"'))['suma'];
				$wyplacone = mysql_fetch_assoc(mysql_query('select sum(kwota) as suma from '.$prefiks_tabel.'pp_wyplaty where id_uzytkownika="'.$uzytkownik['id'].'" and status="zrealizowany"'))['suma'];
				
				if($suma_prowizji - $wyplacone >= $kwota and $kwota >= $ustawienia['pp_minimalna_kwota']){
					
					mysql_query('update '.$prefiks_tabel.'uzytkownicy set pp_imie="'.filtruj($_POST['pp_imie']).'", pp_adres="'.filtruj($_POST['pp_adres']).'", pp_numer_konta="'.filtruj($_POST['pp_numer_konta']).'" where id="'.$uzytkownik['id'].'" limit 1');
					logowanie();
					
					mysql_query('INSERT INTO `'.$prefiks_tabel.'pp_wyplaty`(`id_uzytkownika`, `kwota`, `data`) VALUES ("'.$uzytkownik['id'].'","'.$kwota.'", "'.date("Y-m-d H:i:s").'")');
					
					$infobox[] = array('klasa'=>'zielona','tresc'=>'Zlecone wypłacenie środków w wysokości '.$kwota.'&nbsp;'.$ustawienia['waluta']);
					
					wyslij_mail('pp_wyplata_admin',$ustawienia['pp_email'],array('email'=>$uzytkownik['email'], 'login'=>$uzytkownik['login'], 'pp_imie'=>$uzytkownik['pp_imie'], 'pp_adres'=>$uzytkownik['pp_adres'], 'pp_numer_konta'=>$uzytkownik['pp_numer_konta'], 'kwota'=>$kwota));
					
					wyslij_mail('pp_wyplata_user',$uzytkownik['email'],array('login'=>$uzytkownik['login'], 'kwota'=>$kwota));

				}else{
					$infobox[] = array('klasa'=>'czerwona','tresc'=>'Zlecenie wypłacenia środków nie może zostać zrealizowane');
				}
			}else{
				$infobox[] = array('klasa'=>'czerwona','tresc'=>'Oczekujesz na wypłatę środków!');
			}
		}
		
		if($uzytkownik['pp_procent']=='-1'){
			$program_partnerski['prowizja'] = $ustawienia['pp_prowizja'];
		}else{
			$program_partnerski['prowizja'] = $uzytkownik['pp_procent'];
		}

		if($uzytkownik['pp_polecajacy']){
			$program_partnerski['polecajacy'] = mysql_fetch_assoc(mysql_query('select login from '.$prefiks_tabel.'uzytkownicy where id="'.$uzytkownik['pp_polecajacy'].'" limit 1'))['login'];
		}
		
		$q = mysql_query('select login, data from '.$prefiks_tabel.'uzytkownicy where pp_polecajacy="'.$uzytkownik['id'].'"');
		while($dane = mysql_fetch_assoc($q)){$program_partnerski['poleceni'][] = $dane;}
		
		$program_partnerski['suma_prowizji']['rok'] = date("Y");
		$program_partnerski['suma_prowizji']['od_poczatku'] = mysql_fetch_assoc(mysql_query('select sum(prowizja) as suma from '.$prefiks_tabel.'pp_prowizje where id_polecajacego="'.$uzytkownik['id'].'"'))['suma'];
		$program_partnerski['suma_prowizji']['zeszly_rok'] = mysql_fetch_assoc(mysql_query('select sum(prowizja) as suma from '.$prefiks_tabel.'pp_prowizje where id_polecajacego="'.$uzytkownik['id'].'" and (YEAR(data) = YEAR(CURDATE())-1)'))['suma'];
		$program_partnerski['suma_prowizji']['ten_rok'] = mysql_fetch_assoc(mysql_query('select sum(prowizja) as suma from '.$prefiks_tabel.'pp_prowizje where id_polecajacego="'.$uzytkownik['id'].'" and YEAR(data) = YEAR(CURDATE())'))['suma'];
		$program_partnerski['suma_prowizji']['wyplacone'] = mysql_fetch_assoc(mysql_query('select sum(kwota) as suma from '.$prefiks_tabel.'pp_wyplaty where id_uzytkownika="'.$uzytkownik['id'].'" and status="zrealizowany"'))['suma'];
		$program_partnerski['suma_prowizji']['do_wyplaty'] = $program_partnerski['suma_prowizji']['od_poczatku'] - $program_partnerski['suma_prowizji']['wyplacone'];
	
		$program_partnerski['oczekujace'] = 0;
		$q = mysql_query('select status, kwota, data, data_realizacji from '.$prefiks_tabel.'pp_wyplaty where id_uzytkownika="'.$uzytkownik['id'].'"');
		while($dane = mysql_fetch_assoc($q)){
			if($dane['status']=='oczekujacy'){$program_partnerski['oczekujace'] += $dane['kwota'];}
			$program_partnerski['zlecone_wyplaty'][] = $dane;
		}
	
		if(!$program_partnerski['oczekujace'] and ($program_partnerski['suma_prowizji']['do_wyplaty'] - $program_partnerski['oczekujace'] >= $ustawienia['pp_minimalna_kwota'])){
			$program_partnerski['formularz_wyplaty'] = true;
		}else{
			$program_partnerski['formularz_wyplaty'] = false;
		}
	
		$smarty->assign("program_partnerski", $program_partnerski);
		
	}else{
		$infobox[] = array('klasa'=>'czerwona','tresc'=>'Program partnerski jest wyłączony');
		$strona = '404';
	}

	$nawigacja[] = array('url'=>'moj_program_partnerski', 'nazwa'=>'Program partnerski - ustawienia');
	
}else{
	
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Musisz być zalogowany aby zobaczyć tą stronę');
	$strona = '404';
}
