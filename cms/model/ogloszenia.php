<?php

if(isset($cms_login)){
	
	if(isset($_POST['akcja']) and isset($_POST['id'])){
		$id = filtruj($_POST['id']);
		if($_POST['akcja']=='aktywuj_ogloszenie' and isset($_POST['koniec'])){
			mysql_query('update '.$prefiks_tabel.'ogloszenia set aktywna=1, koniec="'.filtruj($_POST['koniec']).'" where id="'.$id.'" limit 1');
			wyslij_mail('start','',array('id'=>$id));
		}elseif($_POST['akcja']=='dezaktywuj_ogloszenie'){
			mysql_query('update '.$prefiks_tabel.'ogloszenia set aktywna=0 where id="'.$id.'" limit 1');
			wyslij_mail('koniec','',array('id'=>$id));
		}elseif($_POST['akcja']=='usun_ogloszenie'){
			ogloszenie_do_archiwum($id);
		}
		policz_podkategorie();
	}elseif(isset($_POST['akcja']) and $_POST['akcja']=='usun_ogloszenia' and isset($_POST['ogloszenia']) and is_array($_POST['ogloszenia'])){
		foreach($_POST['ogloszenia'] as $key=>$value){
			ogloszenie_do_archiwum($value);
		}
		policz_podkategorie();
	}
	
	$smarty->assign("ogloszenia_nazwa", "wszystkie");
	$smarty->assign("ogloszenia_rodzaj", "wszystkie ogłoszenia");
	
	$warunek = 'true';
	$tabela = 'ogloszenia';
	$ogloszenia_nazwa = "wszystkie";
	$ogloszenia_rodzaj = "wszystkie ogłoszenia";
	
	if(isset($_GET['rodzaj'])){
		if($_GET['rodzaj'] == 'aktywne'){
			$warunek = ' aktywna=1 ';
			$ogloszenia_nazwa = "aktywne";
			$ogloszenia_rodzaj = "aktywne ogłoszenia";
		}elseif($_GET['rodzaj'] == 'nieaktywne'){
			$warunek = ' aktywna=0 ';
			$ogloszenia_nazwa = "nieaktywne";
			$ogloszenia_rodzaj = "nieaktywne ogłoszenia";
		}elseif($_GET['rodzaj'] == 'oplacone'){
			$warunek = ' oplacona=1 ';
			$ogloszenia_nazwa = "opłacone";
			$ogloszenia_rodzaj = "opłacone ogłoszenia";
		}elseif($_GET['rodzaj'] == 'nieoplacone'){
			$warunek = ' oplacona=0 ';
			$ogloszenia_nazwa = "nieopłacone";
			$ogloszenia_rodzaj = "nieopłacone ogłoszenia";
		}
	}elseif(isset($_GET['tabela']) and $_GET['tabela']=='archiwum'){
		$tabela = 'archiwum_ogloszenia';
		$ogloszenia_nazwa = "archiwum";
		$ogloszenia_rodzaj = "ogłoszenia będące w archiwum";
	}
	
	$ile_na_strone = 50;
	
	$wyszukaj = '';
	if(isset($_GET['wyszukaj'])){
		if(isset($_GET['id']) and $_GET['id']!=''){
			$wyszukaj .= ' and id="'.filtruj($_GET['id']).'" ';
		}
		if(isset($_GET['tytul']) and $_GET['tytul']!=''){
			$wyszukaj .= ' and prosty_tytul like "%'.filtruj(prosta_nazwa($_GET['tytul'])).'%" ';
		}
	}
	
	$q = mysql_query('select * from '.$prefiks_tabel.$tabela.' where '.$warunek.' '.$wyszukaj.' order by '.sortuj('aktywna, id desc').' limit '.policz_strony($prefiks_tabel.$tabela,$ile_na_strone, $warunek.' '.$wyszukaj).','.$ile_na_strone.'');
	while($dane = mysql_fetch_array($q)){
		$dane_uzytkownika = mysql_fetch_array(mysql_query('select login, email from '.$prefiks_tabel.'uzytkownicy where id="'.$dane['id_uzytkownika'].'" limit 1'));
		if($dane_uzytkownika){
			$dane['login'] = $dane_uzytkownika['login'];
			$dane['email'] = $dane_uzytkownika['email'];
		}
		$ogloszenia[] = $dane;
	}
	if(isset($ogloszenia)){$smarty->assign("ogloszenia", $ogloszenia);}
	
	$smarty->assign("ogloszenia_nazwa", $ogloszenia_nazwa);
	$smarty->assign("ogloszenia_rodzaj", $ogloszenia_rodzaj);
	
}else{
	die('Brak dostepu!');
}
