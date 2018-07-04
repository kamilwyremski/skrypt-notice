<?php

function logowanie(){
	global $smarty, $cms_login, $prefiks_tabel;
	
	if (isset($_GET['wyloguj'])){
		if(isset($_SESSION['uzytkownik']['kod_sesji'])){
			mysql_query('delete from sesje_logowania where kod="'.filtruj($_SESSION['uzytkownik']['kod_sesji']).'" limit 1');
		}
		$_SESSION['cms_zalogowany'] = $_SESSION['cms_login'] = false;
		session_destroy();
		header('Location: index.php');
	}elseif(isset($_POST['login']) and isset($_POST['haslo']) and isset($_POST['logowanie'])){
		$login = filtruj($_POST['login']);
		if(mysql_num_rows(mysql_query('SELECT id FROM '.$prefiks_tabel.'cms_logi WHERE zalogowal=0 AND data > DATE_ADD(NOW(), INTERVAL -30 MINUTE) AND ip="'.get_client_ip().'";')) > 4){
			$_SESSION['cms_zalogowany'] = false;
			$smarty->assign("komunikat", 'Przekroczono limit prób logowania');
		}elseif(mysql_num_rows(mysql_query('SELECT id FROM '.$prefiks_tabel.'cms WHERE login = "'.$login.'" AND haslo = "'.md5(filtruj($_POST['haslo'])).'" limit 1'))){
			$_SESSION['cms_zalogowany'] = true;
			$_SESSION['cms_login'] = $cms_login = $login;
			$smarty->assign("cms_login", $cms_login);	
			mysql_query('insert into '.$prefiks_tabel.'cms_logi (`login`, `zalogowal`, `data`, `ip`) values("'.$login.'", "1", "'.date("Y-m-d H:i:s").'", "'.get_client_ip().'")');
		}else{
			$_SESSION['cms_zalogowany'] = false;
			$smarty->assign("komunikat", 'Niepoprawne dane');
			mysql_query('insert into '.$prefiks_tabel.'cms_logi (`login`, `zalogowal`, `data`, `ip`) values("'.$login.'", "0", "'.date("Y-m-d H:i:s").'", "'.get_client_ip().'")');
		}
	}elseif(isset($_SESSION['cms_login']) and isset($_SESSION['cms_zalogowany'])){
		if(mysql_num_rows(mysql_query('SELECT id FROM '.$prefiks_tabel.'cms WHERE login = "'.filtruj($_SESSION['cms_login']).'" limit 1'))){
			global $cms_login;
			$cms_login = $_SESSION['cms_login'];
			$smarty->assign("cms_login", $cms_login);	
		}else{
			$_SESSION['cms_zalogowany'] = $_SESSION['cms_login'] = false;
			session_destroy();
			header('Location: index.php');
		}
	}
}
logowanie();

