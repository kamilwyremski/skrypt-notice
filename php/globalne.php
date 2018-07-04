<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

function policz_koszt_ogloszenia($id_ogloszenia, $tabela='ogloszenia'){
	global $ustawienia, $prefiks_tabel;
	
	$ogloszenie = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.$tabela.' where id="'.$id_ogloszenia.'" limit 1'));
	if($ogloszenie!=''){
		
		$do_oplacenia = 0;
		
		if($ustawienia['koszt_ogloszenia']!='0.00'){
			$oplata[] = array('opis'=>'Opłata za wystawienie ogłoszenia', 'ile'=>$ustawienia['koszt_ogloszenia']);
			$do_oplacenia += $ustawienia['koszt_ogloszenia'];
		}

		$koszt_kategoria = mysql_fetch_assoc(mysql_query('select nazwa, oplata from '.$prefiks_tabel.'kategorie where id="'.$ogloszenie['kategoria'].'" limit 1'));
		if($koszt_kategoria != ''){
			$oplata[] = array('opis'=>'Opłata za wystawienie ogłoszenia w kategorii: '.$koszt_kategoria['nazwa'], 'ile'=>$koszt_kategoria['oplata']);
			$do_oplacenia += $koszt_kategoria['oplata'];
		}
		
		$czas_ogloszenia = mysql_fetch_assoc(mysql_query('select dlugosc, cena from '.$prefiks_tabel.'czas_ogloszen where id="'.$ogloszenie['czas_ogloszenia'].'" limit 1'));
		if($czas_ogloszenia == ''){
			$czas_ogloszenia = array('dlugosc'=>$ustawienia['domyslny_czas'], 'cena' => '0');
		}
		$oplata[] = array('opis'=>'Opłata za długość trwania ogłoszenia ('.$czas_ogloszenia['dlugosc'].' dni)', 'ile'=>$czas_ogloszenia['cena']);
		$do_oplacenia += $czas_ogloszenia['cena'];

		if($ogloszenie['slider']){
			$oplata[] = array('opis'=>'Opłata za wyświetlanie ogłoszenia w sliderze', 'ile'=>$ustawienia['koszt_slider']);
			$do_oplacenia += $ustawienia['koszt_slider'];
		}
		if($ogloszenie['promowana']){
			$oplata[] = array('opis'=>'Opłata za promowanie ogłoszenia', 'ile'=>$ustawienia['koszt_promowana']);
			$do_oplacenia += $ustawienia['koszt_promowana'];
		}
		
		$ile_zdjec = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'zdjecia where id_ogloszenia = "'.$ogloszenie['id'].'"'));
		
		if($ile_zdjec>$ustawienia['zdjec_bezplatnie']){
			$ustawienia['koszt_zdjecia'] = $ustawienia['koszt_zdjecia'];
			$oilezdjec = $ile_zdjec - $ustawienia['zdjec_bezplatnie'];
			$oplata[] = array('opis'=>'Opłata za dodatkowe zdjęcia ('.$oilezdjec.')', 'ile'=>($oilezdjec*$ustawienia['koszt_zdjecia']));
			$do_oplacenia += $oilezdjec*$ustawienia['koszt_zdjecia'];
		}
		
		$zostalo_do_zaplacenia = $do_oplacenia - $ogloszenie['oplacona_koszt'];
		if($zostalo_do_zaplacenia<=0){
			$oplacona = true;
			$zostalo_do_zaplacenia = '0.00';
		}else{
			$oplacona = false;
		}
		
		return array($oplacona,$oplata,$do_oplacenia,$zostalo_do_zaplacenia);
	}
	
}

function ogloszenie_do_archiwum($id_ogloszenia){
	global $ustawienia, $prefiks_tabel;
	
	$ile_wyswietlen = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'logi_wyswietlenia where id_ogloszenia="'.$id_ogloszenia.'"'));
	$opcje_dodatkowe = array();
	$q = mysql_query('select opcja_id, wartosc from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id="'.$id_ogloszenia.'"');
	while($dane = mysql_fetch_assoc($q)){
		$opcje_dodatkowe[$dane['opcja_id']] = $dane['wartosc'];
	}	
	mysql_query('CREATE TEMPORARY TABLE temp_table SELECT * FROM '.$prefiks_tabel.'ogloszenia WHERE id='.$id_ogloszenia.'');
	mysql_query('ALTER TABLE `temp_table` ADD `ile_wyswietlen` int(11)');
	mysql_query('ALTER TABLE `temp_table` ADD `opcje_dodatkowe` TEXT');
	mysql_query('UPDATE temp_table SET ile_wyswietlen="'.$ile_wyswietlen.'", opcje_dodatkowe="'.mysql_real_escape_string(json_encode($opcje_dodatkowe, JSON_UNESCAPED_UNICODE)).'"');
	mysql_query('INSERT INTO '.$prefiks_tabel.'archiwum_ogloszenia SELECT * from temp_table;');
	mysql_query('DROP TABLE temp_table;');
	$q = mysql_query('select miniaturka, url from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$id_ogloszenia.'"');
	while($dane = mysql_fetch_assoc($q)){
		unlink(realpath(dirname(__FILE__)).'/../'.$ustawienia['upload'].'/'.$dane['miniaturka']);
		unlink(realpath(dirname(__FILE__)).'/../'.$ustawienia['upload'].'/'.$dane['url']);
	}
	mysql_query('delete from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$id_ogloszenia.'"');
	mysql_query('delete from '.$prefiks_tabel.'wartosci_opcji where ogloszenie_id="'.$id_ogloszenia.'"');
	mysql_query('delete from '.$prefiks_tabel.'logi_wyswietlenia where id_ogloszenia="'.$id_ogloszenia.'"');
	mysql_query('delete from '.$prefiks_tabel.'schowek where id_ogloszenia="'.$id_ogloszenia.'"');
	mysql_query('delete from '.$prefiks_tabel.'ogloszenia where id="'.$id_ogloszenia.'" limit 1');
}

function policz_podkategorie($id_kategorii="0"){
	global $prefiks_tabel;
	$q = mysql_query('select id from '.$prefiks_tabel.'kategorie where kategoria="'.$id_kategorii.'"');
	$ile = mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'ogloszenia where kategoria="'.$id_kategorii.'" and aktywna=1'));
	$podkategorie = array();
	while($dane = mysql_fetch_assoc($q)){
		$podkategorie[] = $dane['id'];
		$return = policz_podkategorie($dane['id']);
		$ile += $return[0];
		foreach($return[1] as $key => $value){
			$podkategorie[] = $value;
		}
	}
	$spis_podkategorii = implode(',',$podkategorie);
	mysql_query('update '.$prefiks_tabel.'kategorie set ile="'.$ile.'", podkategorie="'.$spis_podkategorii.'" where id="'.$id_kategorii.'" limit 1');
	return array($ile, $podkategorie);
}

function ogloszenie_zostalo_oplacone($id_ogloszenia, $kwota){
	global $prefiks_tabel, $ustawienia;
	
	if($id_ogloszenia>0 and $kwota>0){
		
		mysql_query('UPDATE `'.$prefiks_tabel.'ogloszenia` SET `oplacona_koszt`= '.$kwota.' + oplacona_koszt where id="'.$id_ogloszenia.'" limit 1');

		list($oplacona,$oplata,$do_oplacenia,$zostalo_do_zaplacenia) = policz_koszt_ogloszenia($id_ogloszenia);

		if($oplacona){
			
			$dane_ogloszenia = mysql_fetch_assoc(mysql_query('select id_uzytkownika, start, aktywna, czas_ogloszenia from '.$prefiks_tabel.'ogloszenia where id="'.$id_ogloszenia.'" limit 1'));
			
			$koniec = date('Y-m-d', strtotime($dane_ogloszenia['start']. ' + '.intval($ustawienia['domyslny_czas']).' days'));
			if($dane_ogloszenia['czas_ogloszenia']>0){
				$czas_trwania = mysql_fetch_assoc(mysql_query('select dlugosc from '.$prefiks_tabel.'czas_ogloszen where id="'.$dane_ogloszenia['czas_ogloszenia'].'" limit 1'));
				if($czas_trwania){
					$koniec = date('Y-m-d', strtotime($dane_ogloszenia['start']. ' + '.intval($czas_trwania['dlugosc']).' days'));
				}
			}
			
			mysql_query('UPDATE `'.$prefiks_tabel.'ogloszenia` SET aktywna=1, oplacona=1, koniec="'.$koniec.'" where id="'.$id_ogloszenia.'" limit 1');
			
			if(!$dane_ogloszenia['aktywna']){
				wyslij_mail('start','',array('id'=>$id_ogloszenia));
				policz_podkategorie();
			}

			if($ustawienia['program_partnerski'] and $dane_ogloszenia['id_uzytkownika']){
				
				$polecajacy = mysql_fetch_assoc(mysql_query('select id, pp_procent from '.$prefiks_tabel.'uzytkownicy where id=(select pp_polecajacy from '.$prefiks_tabel.	'uzytkownicy where id="'.$dane_ogloszenia['id_uzytkownika'].'" limit 1) limit 1'));
				
				if($polecajacy){
					if($polecajacy['pp_procent']=='-1'){
						$procent = $ustawienia['pp_prowizja'];
					}else{
						$procent = $polecajacy['pp_procent'];
					}
					$prowizja = $kwota*$procent/100;
					
					if(mysql_num_rows(mysql_query('select procent, kwota, prowizja from '.$prefiks_tabel.'pp_prowizje where id_ogloszenia="'.$id_ogloszenia.'" and id_uzytkownika="'.$dane_ogloszenia['id_uzytkownika'].'" and id_polecajacego="'.$polecajacy['id'].'" limit 1'))){
						mysql_query('UPDATE `'.$prefiks_tabel.'pp_prowizje` SET kwota = kwota + "'.$kwota.'",  prowizja = prowizja + "'.$prowizja.'", procent = (prowizja/kwota*100), data = "'.date("Y-m-d H:i:s").'" where id_ogloszenia="'.$id_ogloszenia.'" limit 1');
					}else{
						mysql_query('INSERT INTO `'.$prefiks_tabel.'pp_prowizje`(`id_ogloszenia`, `id_uzytkownika`, `id_polecajacego`, `kwota`,  `procent`, `prowizja`, `data`) VALUES ("'.$id_ogloszenia.'", "'.$dane_ogloszenia['id_uzytkownika'].'", "'.$polecajacy['id'].'", "'.$kwota.'", "'.$procent.'", "'.$prowizja.'", "'.date("Y-m-d H:i:s").'")');
					}
				}			
			}
		}
	}
}

function pp_sprawdz_czy_polecono(){
	global $prefiks_tabel, $ustawienia;
	if($ustawienia['program_partnerski']){
		$pp_id_polecajacego = mysql_fetch_assoc(mysql_query('select id_uzytkownika from '.$prefiks_tabel.'pp_polecenia where ip="'.get_client_ip().'" and data>(CURDATE() - INTERVAL '.$ustawienia['pp_godzin_dezaktywacja'].' HOUR) limit 1'))['id_uzytkownika'];
		if($pp_id_polecajacego!=''){
			if($ustawienia['pp_deaktywacja_po_rejestracji']){
				mysql_query('delete from '.$prefiks_tabel.'pp_polecenia where ip="'.get_client_ip().'"');
			}
			return $pp_id_polecajacego;
		}else{
			return "0";
		}
	}else{
		return "0";
	}
}

if($ustawienia['program_partnerski'] and isset($_GET['ref']) and $_GET['ref']!=''){
	mysql_query('DELETE FROM `'.$prefiks_tabel.'pp_polecenia` WHERE ip="'.get_client_ip().'" limit 1');
	$pp_id_polecajacego = mysql_fetch_assoc(mysql_query('select id from '.$prefiks_tabel.'uzytkownicy where login="'.filtruj($_GET['ref']).'" limit 1'))['id'];
	if($pp_id_polecajacego!=''){
		mysql_query('INSERT INTO `'.$prefiks_tabel.'pp_polecenia`(`ip`, `id_uzytkownika`, `data`) VALUES ("'.get_client_ip().'","'.$pp_id_polecajacego.'","'.date("Y-m-d H:i:s").'")');
	}
	header('Location: index.php');
}

function nofollow($html) {
	global $ustawienia;
	$skip = $ustawienia['base_url'];
    return preg_replace_callback(
        "#(<a[^>]+?)>#is", function ($mach) use ($skip) {
            return (
                !($skip && strpos($mach[1], $skip) !== false) &&
                strpos($mach[1], 'rel=') === false
            ) ? $mach[1] . ' rel="nofollow">' : $mach[0];
        },
        $html
    );
}

