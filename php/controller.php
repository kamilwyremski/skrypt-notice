<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($_GET['akcja'])){
	switch($_GET['akcja']){
		case 'logowanie':
		case 'aktywacja':
			$ustawienia['title'] = "Logowanie - ".$ustawienia['title'];
			$ustawienia['description'] = "Logowanie do serwisu. ".$ustawienia['description'];
			$strona = 'logowanie';
			include('model/logowanie.php');
			break;
		case 'rejestracja':
			$ustawienia['title'] = "Rejestracja - ".$ustawienia['title'];
			$ustawienia['description'] = "Rejestracja w serwisie. ".$ustawienia['description'];
			$strona = 'rejestracja';
			include('model/rejestracja.php');
			break;
		case 'reset_hasla':
			$ustawienia['title'] = "Reset hasła - ".$ustawienia['title'];
			$ustawienia['description'] = "Reset hasła. ".$ustawienia['description'];
			$strona = 'reset_hasla';
			include('model/reset_hasla.php');
			break;		
		case 'ogloszenie':
			$strona = 'ogloszenie';
			include('model/ogloszenie.php');
			break;	
		case 'dodaj':
			if(isset($uzytkownik) or $ustawienia['ogloszenia_bez_rejestracji']){
				$ustawienia['title'] = "Dodaj ogłoszenie - ".$ustawienia['title'];
				$ustawienia['description'] = "Dodaj ogłoszenie. ".$ustawienia['description'];
				$strona = 'dodaj';
				include('model/dodaj.php');
			}else{
				header('Location: logowanie?redirect=dodaj');
			}
			break;	
		case 'edytuj':
			if(isset($uzytkownik)){
				$ustawienia['title'] = "Edytuj ogłoszenie - ".$ustawienia['title'];
				$ustawienia['description'] = "Edytuj ogłoszenie. ".$ustawienia['description'];
				$strona = 'dodaj';
				include('model/dodaj.php');
			}else{
				header('Location: logowanie?redirect=edytuj,'.$_GET['id'].','.$_GET['prosty_tytul']);
			}
			break;	
		case 'moje_ogloszenia':
			if(isset($uzytkownik)){
				$ustawienia['title'] = "Moje ogłoszenia - ".$ustawienia['title'];
				$ustawienia['description'] = "Lista moich ogłoszeń. ".$ustawienia['description'];
				$strona = 'moje_ogloszenia';
				include('model/moje_ogloszenia.php');
			}else{
				header('Location: logowanie?redirect=moje_ogloszenia');
			}
			break;	
		case 'schowek':
			if(isset($uzytkownik)){
				$ustawienia['title'] = "Schowek - ".$ustawienia['title'];
				$ustawienia['description'] = "Schowek z ogłoszeniami. ".$ustawienia['description'];
				$strona = 'schowek';
				include('model/schowek.php');
			}else{
				header('Location: logowanie?redirect=schowek');
			}
			break;	
		case 'ustawienia':
			if(isset($uzytkownik)){
				$ustawienia['title'] = "Ustawienia konta - ".$ustawienia['title'];
				$ustawienia['description'] = "Ustawienia konta. ".$ustawienia['description'];
				$strona = 'ustawienia';
				include('model/ustawienia.php');
			}else{
				header('Location: logowanie?redirect=ustawienia');
			}
			break;
		case 'o_mnie':
			if(isset($uzytkownik)){
				$ustawienia['title'] = "O mnie - edytuj opis - ".$ustawienia['title'];
				$ustawienia['description'] = "Edytuj opis o mnie. ".$ustawienia['description'];
				$strona = 'o_mnie';
				include('model/o_mnie.php');
			}else{
				header('Location: logowanie?redirect=o_mnie');
			}
			break;
		case 'moj_program_partnerski':
			if(isset($uzytkownik)){
				$ustawienia['title'] = "Program partnerski - ustawienia - ".$ustawienia['title'];
				$ustawienia['description'] = "Program partnerski - ustawienia. ".$ustawienia['description'];
				$strona = 'moj_program_partnerski';
				include('model/moj_program_partnerski.php');
			}else{
				header('Location: logowanie?redirect=moj_program_partnerski');
			}
			break;
		case 'info':
			$strona = 'info';
			include('model/info.php');
			break;
		case 'kontakt':
			$ustawienia['title'] = "Kontakt - ".$ustawienia['title'];
			$ustawienia['description'] = "Kontakt z nami. ".$ustawienia['description'];
			$strona = 'kontakt';
			include('model/kontakt.php');
			break;	
		case 'profil':
			$strona = 'profil';
			include('model/profil.php');
			break;	
		case 'aktualnosci':
			$ustawienia['title'] = "Aktualności - ".$ustawienia['title'];
			$ustawienia['description'] = "Spis aktualności zamieszczonych na stronie. ".$ustawienia['description'];
			$strona = 'aktualnosci';
			include('model/aktualnosci.php');
			break;	
		case 'aktualnosc':
			$strona = 'aktualnosc';
			include('model/aktualnosc.php');
			break;	
		case '404':
			$ustawienia['title'] = "Błąd 404 - ".$ustawienia['title'];
			$ustawienia['description'] = "Błąd 404 - strona nie istnieje. ".$ustawienia['description'];
			$strona = '404';
			include('model/404.php');
			break;
		default:
			include('model/index.php');
			$strona = 'index';
	}
}else{
	include('model/index.php');
	$strona = 'index';
}
	
if(isset($_GET['zalogowano']) and isset($uzytkownik)){
	$infobox[] = array('klasa'=>'zielona','tresc'=>'Pomyślnie zalogowano użytkownika '.$uzytkownik['login']);
}elseif(isset($_GET['wylogowano'])){
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Pomyślnie wylogowano z serwisu');
}elseif(isset($_GET['ogloszenie_nie_dodano'])){
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Ogłoszenie nie mogło zostać dodane - zostało oznaczone jako spam');
}elseif(isset($_GET['ogloszenie_usunieto'])){
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Ogłoszenie zostało usunięte z systemu');
}
