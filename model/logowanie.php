<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($_POST['akcja']) and $_POST['akcja'] == 'logowanie' and isset($_POST['kod_sesji']) and $_POST['kod_sesji']!='' and isset($_POST['login']) and $_POST['login']!='' and isset($_POST['haslo']) and $_POST['haslo']!=''){
	
	$kod_sesji = filtruj($_POST['kod_sesji']);
	
	if(mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'sesje_logowania where kod="'.$kod_sesji.'" and ip="'.get_client_ip().'" limit 1'))){
		
		$wynik = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'uzytkownicy where (login="'.filtruj($_POST['login']).'" or email="'.filtruj($_POST['login']).'") and haslo="'.md5($_POST['haslo'].$password_salt).'" limit 1'));
		if($wynik!=''){
			if($wynik['aktywny']=='1'){
				
				$uzytkownik = $wynik;
				$_SESSION['uzytkownik']['id'] = $uzytkownik['id'];
				$_SESSION['uzytkownik']['kod_sesji'] = $kod_sesji;
				$smarty->assign("uzytkownik", $uzytkownik);	
				
				mysql_query('update '.$prefiks_tabel.'sesje_logowania set id_uzytkownika="'.$uzytkownik['id'].'" where kod="'.$kod_sesji.'" limit 1');

				mysql_query('INSERT INTO `'.$prefiks_tabel.'logi_uzytkownicy`(`id_uzytkownika`, `ip`, `data`) VALUES ("'.$uzytkownik['id'].'", "'.get_client_ip().'", "'.date("Y-m-d H:i:s").'")');
				
				if(isset($_GET['redirect']) and $_GET['redirect']!=''){
					header("Location: ".$_GET['redirect']."?zalogowano");
				}else{
					header("Location: ".$ustawienia['base_url']."?zalogowano");
				}
				
			}else{
				$smarty->assign("blad", "Konto nie zostało jeszcze aktywowane.");
				$smarty->assign("login", $_POST['login']);
			}
		}else{
			mysql_query('delete from '.$prefiks_tabel.'sesje_logowania where kod="'.$kod_sesji.'" limit 1');
			unset($_SESSION['uzytkownik']);
			$smarty->assign("blad", "Podane dane są nieprawidłowe.");
			$smarty->assign("login", $_POST['login']);
		}
	}else{
		$smarty->assign("blad", "Błąd sesji.");
		$smarty->assign("login", $_POST['login']);
	}
}elseif($_GET['akcja']=='aktywacja' and isset($_GET['kod']) and $_GET['kod']!=''){
	if(mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'uzytkownicy where kod_aktywacyjny="'.filtruj($_GET['kod']).'" and aktywny="0" limit 1'))){
		mysql_query('update '.$prefiks_tabel.'uzytkownicy set aktywny="1", data_aktywacji="'.date("Y-m-d H:i:s").'", ip_aktywacji="'.get_client_ip().'" where kod_aktywacyjny="'.filtruj($_GET['kod']).'" limit 1');
		$smarty->assign("info", "Konto zostało aktywowane,<br>możesz teraz się zalogować.");
	}else{
		$smarty->assign("blad", "Nieprawidłowy kod aktywacyjny lub konto zostało już aktywowane.");
	}
}elseif(isset($_GET['nowe_konto'])){
	$smarty->assign("info", "Konto zostało założone. Na Twój adres email została wysłana wiadomość z kodem aktywacyjnym");
}elseif(isset($_GET['wyslano'])){
	$smarty->assign("info", "Link do zmiany hasła został wysłany na Twój adres email.");
}elseif(isset($_GET['zmieniono_haslo'])){
	$smarty->assign("info", "Hasło zostało poprawnie zmienione. Możesz teraz zalogować się do serwisu.");
}

if($ustawienia['logowanie_facebook']==1 and $ustawienia['facebook_api']!='' and $ustawienia['facebook_secret']!=''){
	require_once('php/facebook.php');
}

$kod_sesji = md5(uniqid(rand(), true));
mysql_query('INSERT INTO `'.$prefiks_tabel.'sesje_logowania`(`kod`, `ip`, `data`) VALUES ("'.$kod_sesji.'", "'.get_client_ip().'", "'.date("Y-m-d H:i:s").'")');
$smarty->assign("kod_sesji", $kod_sesji);
