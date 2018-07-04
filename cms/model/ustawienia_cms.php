<?php

if(isset($cms_login)){
	if(isset($_POST['akcja']) and $_POST['akcja'] == 'cms_zmien_ustawienia' and isset($_POST['nowy_login']) and isset($_POST['nowe_haslo']) and isset($_POST['powtorz_nowe_haslo'])){
		global $cms_login;
		if($_POST['nowe_haslo']!==$_POST['powtorz_nowe_haslo']){
			$smarty->assign("komunikat", 'Podane hasła są różne!');
		}else{
			$login = filtruj($_POST['nowy_login']);
			mysql_query('update '.$prefiks_tabel.'cms set login="'.$login.'", haslo=md5("'.filtruj($_POST['nowe_haslo']).'") where login="'.$cms_login.'" limit 1');
			logowanie();
			$smarty->assign("komunikat", 'Ustawienia zostały zmienione poprawnie.');
		}
	}
	if(isset($_POST['akcja']) and $_POST['akcja'] == 'cms_usun_logi'){
		mysql_query('delete from '.$prefiks_tabel.'cms_logi');
	}else{
		$q = mysql_query('select * from '.$prefiks_tabel.'cms_logi');
		while($dane = mysql_fetch_assoc($q)){$cms_logi[] = $dane;}
		if(isset($cms_logi)){$smarty->assign("cms_logi", $cms_logi);}
	}
}else{
	die('Brak dostepu!');
}

