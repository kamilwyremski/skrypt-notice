<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($cms_login)){
	if(isset($_GET['akcja'])){
		switch($_GET['akcja']){
			case 'statystyki': 
				$title = "Statystyki";
				$strona = 'statystyki';
				include('model/statystyki.php');
				break;
			case 'ogloszenia': 
				$title = "Ogłoszenia";
				$strona = 'ogloszenia';
				include('model/ogloszenia.php');
				break;
			case 'uzytkownicy': 
				$title = "Użytkownicy";
				$strona = 'uzytkownicy';
				include('model/uzytkownicy.php');
				break;
			case 'program_partnerski': 
				$title = "Program partnerski";
				$strona = 'program_partnerski';
				include('model/program_partnerski.php');
				break;
			case 'pp_uzytkownicy': 
				$title = "Użytkownicy programu partnerskiego";
				$strona = 'pp_uzytkownicy';
				include('model/pp_uzytkownicy.php');
				break;
			case 'pp_prowizje': 
				$title = "Prowizje z programu partnerskiego";
				$strona = 'pp_prowizje';
				include('model/pp_prowizje.php');
				break;
			case 'pp_wyplaty': 
				$title = "Wypłaty z programu partnerskiego";
				$strona = 'pp_wyplaty';
				include('model/pp_wyplaty.php');
				break;
			case 'kategorie': 
				$title = "Kategorie";
				$strona = 'kategorie';
				include('model/kategorie.php');
				break;
			case 'kategoria': 
				$title = "Kategoria";
				$strona = 'kategoria';
				include('model/kategoria.php');
				break;
			case 'regiony': 
				$title = "Regiony";
				$strona = 'regiony';
				include('model/regiony.php');
				break;
			case 'regiony2': 
				$title = "Regiony 2";
				$strona = 'regiony2';
				include('model/regiony2.php');
				break;
			case 'typy_ogloszen': 
				$title = "Typy ogłoszeń";
				$strona = 'typy_ogloszen';
				include('model/typy_ogloszen.php');
				break;
			case 'opcje_dodatkowe': 
				$title = "Opcje dodatkowe";
				$strona = 'opcje_dodatkowe';
				include('model/opcje_dodatkowe.php');
				break;
			case 'opcja_dodatkowa': 
				$title = "Opcja dodatkowa";
				$strona = 'opcja_dodatkowa';
				include('model/opcja_dodatkowa.php');
				break;
			case 'tresci': 
				$title = "Treści";
				$strona = 'tresci';
				include('model/tresci.php');
				break;
			case 'tresc': 
				$title = "Edytuj treść";
				$strona = 'tresc';
				include('model/tresc.php');
				break;
			case 'aktualnosci': 
				$title = "Aktualności";
				$strona = 'aktualnosci';
				include('model/aktualnosci.php');
				break;
			case 'mailing': 
				$title = "Mailing";
				$strona = 'mailing';
				include('model/mailing.php');
				break;
			case 'aktualnosc': 
				$title = "Edytuj aktualność";
				$strona = 'aktualnosc';
				include('model/aktualnosc.php');
				break;
			case 'slider': 
				$title = "Slider - edycja";
				$strona = 'slider';
				include('model/slider.php');
				break;
			case 'banery_reklamowe': 
				$title = "Banery reklamowe";
				$strona = 'banery_reklamowe';
				include('model/banery_reklamowe.php');
				break;
			case 'czarna_lista': 
				$title = "Czarna lista użytkowników";
				$strona = 'czarna_lista';
				include('model/czarna_lista.php');
				break;
			case 'ustawienia_maile': 
				$title = "Ustawienia maili";
				$strona = 'ustawienia_maile';
				include('model/ustawienia_maile.php');
				break;
			case 'ustawienia_wyglad': 
				$title = "Ustawienia wyglądu serwisu";
				$strona = 'ustawienia_wyglad';
				include('model/ustawienia_wyglad.php');
				break;
			case 'ustawienia_portale': 
				$title = "Ustawienia portali społecznościowych";
				$strona = 'ustawienia_portale';
				include('model/ustawienia_portale.php');
				break;
			case 'ustawienia_czas': 
				$title = "Ustawienia czasu ogłoszeń";
				$strona = 'ustawienia_czas';
				include('model/ustawienia_czas.php');
				break;
			case 'ustawienia_platnosci': 
				$title = "Ustawienia płatności w serwisie";
				$strona = 'ustawienia_platnosci';
				include('model/ustawienia_platnosci.php');
				break;
			case 'ustawienia': 
				$title = "Ustawienia";
				$strona = 'ustawienia';
				include('model/ustawienia.php');
				break;
			case 'logi_platnosci': 
				$title = "Logi płatności";
				$strona = 'logi_platnosci';
				include('model/logi_platnosci.php');
				break;
			case 'logi_maile': 
				$title = "Logi maili";
				$strona = 'logi_maile';
				include('model/logi_maile.php');
				break;
			case 'logi_uzytkownicy': 
				$title = "Logi użytkowników";
				$strona = 'logi_uzytkownicy';
				include('model/logi_uzytkownicy.php');
				break;
			case 'logi_ogloszenia': 
				$title = "Logi ogłoszeń";
				$strona = 'logi_ogloszenia';
				include('model/logi_ogloszenia.php');
				break;
			case 'logi_reset_hasla': 
				$title = "Logi resetu hasła";
				$strona = 'logi_reset_hasla';
				include('model/logi_reset_hasla.php');
				break;
			case 'ustawienia_cms':
				$title = "Ustawienia CMS";
				$strona = 'ustawienia_cms';
				include('model/ustawienia_cms.php');
				break;
			default:
				$title = "CMS created by Kamil Wyremski";
				$strona = 'home';
		}
	}else{
		$title = "CMS created by Kamil Wyremski";
		$strona = 'home';
	}
	$smarty->assign("title", $title);
	$smarty->assign('strona',$strona);
	$smarty->assign("ustawienia", $ustawienia);
	$smarty->display('main.tpl');
}else{
	$smarty->display('logowanie.tpl');
}
