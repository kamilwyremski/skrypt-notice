<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($_POST['akcja']) and $_POST['akcja'] == 'reset_hasla' and isset($_POST['login']) and isset($_POST['captcha'])){
	
	$login = filtruj($_POST['login']);
	
	if($_POST['captcha']!=$_SESSION['captcha']){
		$smarty->assign("login", $login);
		$smarty->assign("blad", "Nieprawidłowo przepisany kod");
	}else{
		$dane_uzytkownika =  mysql_fetch_array(mysql_query('select id, login, email from '.$prefiks_tabel.'uzytkownicy where login="'.$login.'" or email="'.$login.'" limit 1'));
		if($dane_uzytkownika!=''){
			if(mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'reset_hasla where id_uzytkownika = "'.$dane_uzytkownika['id'].'" and aktywny = 1 and data > (NOW() - INTERVAL 1 DAY) limit 1'))){
				$smarty->assign("blad", "Link do zmiany hasła został już wysłany.");
			}else{
				
				$kod = md5(randomPassword());
				mysql_query('INSERT INTO `'.$prefiks_tabel.'reset_hasla`(`id_uzytkownika`, `aktywny`, `kod`, `data`) VALUES ("'.$dane_uzytkownika['id'].'", "1", "'.$kod.'", "'.date("Y-m-d H:i:s").'")');
				
				wyslij_mail('reset_hasla',$dane_uzytkownika['email'],array('login'=>$dane_uzytkownika['login'], 'email'=>$dane_uzytkownika['email'], 'kod'=>$kod));
				
				header("Location: ".$ustawienia['base_url']."/logowanie?wyslano");
			}
		}else{
			$smarty->assign("login", $login);
			$smarty->assign("blad", "Nieprawidłowe dane użytkownika.");
		}
	}
}elseif(isset($_GET['nowe_haslo'])){
	$id_uzytkownika = mysql_fetch_array(mysql_query('select id_uzytkownika from '.$prefiks_tabel.'reset_hasla where aktywny=1 and kod="'.filtruj($_GET['nowe_haslo']).'" limit 1'));
	if($id_uzytkownika!=''){
		if(isset($_POST['akcja']) and $_POST['akcja'] == 'nowe_haslo' and isset($_POST['haslo']) and isset($_POST['powtorz_haslo'])){
			if($_POST['haslo']!=$_POST['powtorz_haslo']){
				$blad = "Wpisane hasła są różne.";
			}elseif(strlen($_POST['haslo'])>32){
				$blad = "Podane hasło jest za długie.";
			}elseif(strlen($_POST['haslo'])<4){
				$blad = "Podane hasło jest za krótkie.";
			}
			if(isset($blad)){
				$smarty->assign("blad", $blad);
			}else{
				mysql_query('update '.$prefiks_tabel.'reset_hasla set wykorzystany=1, aktywny=0 where kod="'.filtruj($_GET['nowe_haslo']).'" limit 1');
				mysql_query('update '.$prefiks_tabel.'uzytkownicy set haslo="'.md5($_POST['haslo'].$password_salt).'" where id="'.$id_uzytkownika['id_uzytkownika'].'" limit 1');
				header("Location: ".$ustawienia['base_url']."/logowanie?zmieniono_haslo");
			}
		}
		$email_uzytkownika = mysql_fetch_array(mysql_query('select email from '.$prefiks_tabel.'uzytkownicy where id="'.$id_uzytkownika['id_uzytkownika'].'" limit 1'))['email'];
		$smarty->assign("formularz_nowe_haslo", array('email'=>$email_uzytkownika));
	}else{
		$smarty->assign("blad", "Nieprawidłowy lub nieaktywny kod resetu hasła.");
	}
	
}
