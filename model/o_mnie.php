<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($uzytkownik)){
	
	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_o_mnie' and isset($_POST['o_mnie'])){
		
		mysql_query('update '.$prefiks_tabel.'uzytkownicy set o_mnie="'.trim(htmlspecialchars(nofollow(purify($_POST['o_mnie'])))).'" where id="'.$uzytkownik['id'].'" limit 1');
		logowanie();
		$infobox[] = array('klasa'=>'zielona','tresc'=>'Opis został prawidłowo zmieniony');
		
	}
	
	$uzytkownik['o_mnie'] = htmlspecialchars_decode($uzytkownik['o_mnie']);
	$smarty->assign("uzytkownik", $uzytkownik);	
	
	$nawigacja[] = array('url'=>'o_mnie', 'nazwa'=>'O mnie - edytuj');

}else{
	$infobox[] = array('klasa'=>'czerwona','tresc'=>'Musisz być zalogowany aby zobaczyć tą stronę');
	$strona = '404';
	include('model/404.php');
}

pobierz_slider_dol();
