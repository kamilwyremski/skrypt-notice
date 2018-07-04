<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($_POST['akcja']) and $_POST['akcja']=='rejestracja' and isset($_POST['login']) and isset($_POST['email']) and isset($_POST['haslo']) and isset($_POST['powtorz_haslo']) and isset($_POST['captcha'])){

	$login = filtruj($_POST['login']);
	$email = filtruj($_POST['email']);
	
	if($_POST['captcha']!=$_SESSION['captcha']){
		$blad['captcha'] = "Nieprawidłowy kod captcha.";
	}else{
		if(strlen($login)==0) {
			$blad['login'] = "Za krótki login.";
		}elseif(strlen($login)>32) {
			$blad['login'] = "Za długi login.";
		}elseif(mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'uzytkownicy where login="'.$login.'" limit 1'))){
			$blad['login'] = "Login istnieje już w bazie.";
		}elseif((strpos($login, '@') !== false)){
			$blad['login'] = "Login nie może zawierać znaku @.";
		}
		if(!filter_var($email, FILTER_VALIDATE_EMAIL) or strlen($email)>64) {
			$blad['email'] = "Nieprawidłowy adres email.";
		}elseif(mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'uzytkownicy where email="'.$email.'" and login!="" limit 1'))){ 
		// w przypadku gdy nie wpisał loginu przy rejestracji przez facebooka
			$blad['email'] = "Adres e-mail istnieje już w bazie.";
		}elseif(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'czarna_lista where email="'.$email.'" limit 1'))){
			$blad['email'] = "Adres e-mail nie może zostać zarejestrowany.";
		}
		if($_POST['haslo']!=$_POST['powtorz_haslo']){
			$blad['haslo'] = "Wpisane hasła są różne.";
		}elseif(strlen($_POST['haslo'])>32){
			$blad['haslo'] = "Podane hasło jest za długie.";
		}elseif(strlen($_POST['haslo'])==0){
			$blad['haslo'] = "Podane hasło jest za krótkie.";
		}
		if(!isset($_POST['regulamin'])){
			$blad['regulamin'] = "To pole jest obowiązkowe.";
		}
	}
	
	if(isset($blad)){
		$smarty->assign("blad", $blad);
	}else{
		
		// usuwanie kont nie aktywowanych przy rejestracji przez Facebook-a
		mysql_query('delete from `'.$prefiks_tabel.'uzytkownicy` where email="'.$email.'" limit 1');
		
		$kod_aktywacyjny = md5(uniqid(rand(), true));
		
		wyslij_mail('rejestracja',$email,array('kod_aktywacyjny'=>$kod_aktywacyjny, 'login'=>$login, 'email'=>$email, 'haslo'=>$_POST['haslo']));

		mysql_query('INSERT INTO `'.$prefiks_tabel.'uzytkownicy`(`login`, `email`, `haslo`, `kod_aktywacyjny`, `pp_polecajacy`, `pp_procent`, `data`) VALUES ("'.$login.'", "'.$email.'", "'.md5($_POST['haslo'].$password_salt).'", "'.$kod_aktywacyjny.'", "'.pp_sprawdz_czy_polecono().'", "-1", "'.date("Y-m-d H:i:s").'")');
		
		header("Location: logowanie?nowe_konto");
	}
}elseif(isset($_GET['facebook_login']) and isset($_GET['kod']) and $_GET['kod']!=''){
	$dane_uzytkownika = mysql_fetch_array(mysql_query('select id, email from '.$prefiks_tabel.'uzytkownicy where kod_aktywacyjny="'.filtruj($_GET['kod']).'" and login="" and rejestracja_facebook=1 limit 1'));
	if($dane_uzytkownika!=''){
		if(isset($_POST['akcja']) and $_POST['akcja']=='facebook_ustaw_login' and isset($_POST['login'])){
			$login = filtruj($_POST['login']);
			if(strlen($login)==0) {
				$blad['login'] = "Za krótki login.";
			}elseif(strlen($login)>32) {
				$blad['login'] = "Za długi login.";
			}elseif(mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'uzytkownicy where login="'.$login.'" limit 1'))){
				$blad['login'] = "Login istnieje już w bazie.";
			}elseif((strpos($login, '@') !== false)){
				$blad['login'] = "Login nie może zawierać znaku @.";
			}
			if(!isset($_POST['regulamin'])){
				$blad['regulamin'] = "To pole jest obowiązkowe.";
			}
			if(isset($blad)){
				$smarty->assign("blad", $blad);
			}else{
			
				$haslo = randomPassword();
				
				wyslij_mail('rejestracja_fb',$dane_uzytkownika['email'],array('login'=>$login, 'email'=>$dane_uzytkownika['email'], 'haslo'=>$haslo));
				
				mysql_query('update `'.$prefiks_tabel.'uzytkownicy` set login="'.$login.'", haslo="'.md5($haslo.$password_salt).'", aktywny="1", data_aktywacji="'.date("Y-m-d H:i:s").'", ip_aktywacji="'.get_client_ip().'" where id="'.$dane_uzytkownika['id'].'" limit 1');
				
				$kod_sesji = md5(uniqid(rand(), true));
				mysql_query('INSERT INTO `'.$prefiks_tabel.'sesje_logowania`(`id_uzytkownika`, `kod`, `ip`, `data`) VALUES ("'.$dane_uzytkownika['id'].'", "'.$kod_sesji.'", "'.get_client_ip().'", "'.date("Y-m-d H:i:s").'")');
				$_SESSION['uzytkownik']['id'] = $dane_uzytkownika['id'];
				$_SESSION['uzytkownik']['kod_sesji'] = $kod_sesji;
				
				header("Location: index.php?zalogowano");
			}
		}
		$smarty->assign("facebook_login", $dane_uzytkownika['email']);
	}else{
		$smarty->assign("blad", "Nieprawidłowy kod aktywacyjny.");
	}
}

if(!isset($_GET['facebook_login']) and $ustawienia['logowanie_facebook']==1 and $ustawienia['facebook_api']!='' and $ustawienia['facebook_secret']!=''){
	require_once('php/facebook.php');
}
