<?php

function logowanie(){
	global $smarty, $uzytkownik, $prefiks_tabel;
	
	if(mysql_num_rows(mysql_query('select id from '.$prefiks_tabel.'sesje_logowania where id_uzytkownika="'.filtruj($_SESSION['uzytkownik']['id']).'" and kod="'.filtruj($_SESSION['uzytkownik']['kod_sesji']).'" limit 1'))){

		$wynik = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'uzytkownicy where id="'.filtruj($_SESSION['uzytkownik']['id']).'" limit 1'));
		if($wynik!=''){
			$uzytkownik = $wynik;
			if(isset($smarty)){$smarty->assign("uzytkownik", $wynik);}
		}else{
			unset($_SESSION['uzytkownik']);
			session_destroy();
		}
	}
}
	
if(isset($_GET['wyloguj'])){
	if(isset($_SESSION['uzytkownik']['kod_sesji'])){
		mysql_query('delete from sesje_logowania where kod="'.filtruj($_SESSION['uzytkownik']['kod_sesji']).'" limit 1');
	}
	unset($_SESSION['uzytkownik']);
	session_destroy();
	header("Location: ".$ustawienia['base_url']."?wylogowano");
}elseif(isset($_SESSION['uzytkownik']['id']) and isset($_SESSION['uzytkownik']['kod_sesji'])){
	logowanie();
}
