<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

function pobierz_oceny_profilu($id_uzytkownika){
	global $prefiks_tabel;
	if($id_uzytkownika){
		$wynik = mysql_fetch_assoc(mysql_query('select ROUND(avg(ocena),2) as srednia, count(1) as ilosc from '.$prefiks_tabel.'uzytkownicy_oceny where id_profilu="'.filtruj($id_uzytkownika).'"'));
		if(!$wynik['srednia']){
			$wynik['srednia'] = 0;
		}
		return(array('srednia'=>$wynik['srednia'], 'ilosc'=>$wynik['ilosc']));
	}
}

function pobierz_panel_aktualnosci(){
	global $smarty, $prefiks_tabel;
	$q = mysql_query('select id, tytul, prosty_tytul, data from '.$prefiks_tabel.'aktualnosci order by data desc limit 5');
	while($dane = mysql_fetch_assoc($q)){$panel_aktualnosci[] = $dane;}
	if(isset($panel_aktualnosci)){$smarty->assign("panel_aktualnosci", $panel_aktualnosci);}
}

function newsletter(){
	global $prefiks_tabel, $infobox, $ustawienia;
	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_newsletter' and isset($_POST['email']) and $_POST['email']!='' and isset($_POST['regulamin'])){
		$email = filtruj($_POST['email']);
		if(!filter_var($email, FILTER_VALIDATE_EMAIL) or strlen($email)>32) {
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Adres e-mail '.$email.' jest nieprawidłowy');
		}elseif(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'newsletter where email="'.$email.'" limit 1'))){
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Adres e-mail '.$email.' jest już zarejestrowany w newsletterze');
		}elseif(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'czarna_lista where email="'.$email.'" limit 1'))){
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Adres e-mail '.$email.' nie może zostać zarejestrowany w newsletterze');
		}else{
			$kod = md5(randomPassword());

			wyslij_mail('newsletter_potwierdzanie',$email,array('kod'=>$kod));
			
			mysql_query('INSERT INTO `'.$prefiks_tabel.'newsletter`(`email`, `kod`, `ip`, `data`) VALUES ("'.$email.'", "'.$kod.'", "'.get_client_ip().'", "'.date("Y-m-d H:i:s").'")');
			$infobox[] = array('klasa'=>'zielona','tresc'=>'Potwierdź adres e-mail '.$email.' klikająć w wysłany link aktywacyjny!');

		}
	}elseif(isset($_GET['akcja']) and $_GET['akcja']=='anuluj_newsletter' and isset($_GET['kod']) and $_GET['kod']!=''){
		$kod = filtruj($_GET['kod']);
		$email = mysql_fetch_assoc(mysql_query('select email from '.$prefiks_tabel.'newsletter where kod="'.$kod.'" limit 1'))['email'];
		if($email!=''){
			mysql_query('delete from `'.$prefiks_tabel.'newsletter` where kod="'.$kod.'" limit 1');
			$infobox[] = array('klasa'=>'zielona','tresc'=>'Adres e-mail '.$email.' został poprawnie usunięty z newslettera');
		}else{
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nieprawidłowy kod lub newsletter został już anulowany');
		}
	}elseif(isset($_GET['akcja']) and $_GET['akcja']=='potwierdz_newsletter' and isset($_GET['kod']) and $_GET['kod']!=''){
		$kod = filtruj($_GET['kod']);
		$email = mysql_fetch_assoc(mysql_query('select email from '.$prefiks_tabel.'newsletter where kod="'.$kod.'" and aktywny=0 limit 1'))['email'];
		if($email!=''){
			mysql_query('update `'.$prefiks_tabel.'newsletter` set aktywny=1, ip="'.get_client_ip().'", data="'.date("Y-m-d H:i:s").'" where kod="'.$kod.'" limit 1');
			$infobox[] = array('klasa'=>'zielona','tresc'=>'Adres e-mail '.$email.' został poprawnie potwierdzony');
		}else{
			$infobox[] = array('klasa'=>'czerwona','tresc'=>'Nieprawidłowy kod lub adres email został już potwierdzony');
		}
	}
}

function pobierz_slider_dol(){
	global $smarty, $ustawienia, $prefiks_tabel;
	if($ustawienia['slider_dol']){
		$q = mysql_query('select '.$prefiks_tabel.'ogloszenia.id, tytul, prosty_tytul, promowana, cena, za_darmo, miniaturka from '.$prefiks_tabel.'ogloszenia, '.$prefiks_tabel.'zdjecia where aktywna=1 and slider=1 and id_ogloszenia='.$prefiks_tabel.'ogloszenia.id order by rand() limit 15');
		while($dane = mysql_fetch_assoc($q)){$slider_dol[] = $dane;}
		if(isset($slider_dol)){$smarty->assign("slider_dol", $slider_dol);}
	}
}

function policz_strony($tabela, $limit='10', $warunek='true'){
	global $smarty;
	if (isset($_GET['strona']) and is_numeric($_GET['strona']) and $_GET['strona']>0)  { 
		$limit_start = ($_GET['strona']-1)*$limit;
		$smarty->assign("ktora_strona", $_GET['strona']);
	}else{
		$limit_start = 0;
		$smarty->assign("ktora_strona", 1);
	}
	$smarty->assign("ile_stron", ceil(mysql_num_rows(mysql_query('select 1 from '.$tabela.' where '.$warunek.''))/$limit));
	return $limit_start;
}

function prosta_nazwa($text){
	$text = strtolower(str_replace(array(' ','%','$',':','–',',','/','=','?','Ę','Ó','Ą','Ś','Ł','Ż','Ź','Ć','Ń','ę','ó','ą','ś','ł','ż','ź','ć','ń'), array('-','-','','','','','','','','E','O','A','S','L','Z','Z','C','N','e','o','a','s','l','z','z','c','n'), $text));
	$text = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $text);
	$text = strtolower(str_replace(array(' ','$',':',',','/','=','?'), array('-','','','','','',''), $text));
	$text = preg_replace("/[^a-zA-Z0-9-_]+/", "", $text);
	return $text;
}

function filtruj($zmienna){
    if(get_magic_quotes_gpc()){
        $zmienna = stripslashes($zmienna);
	}
    return mysql_real_escape_string(htmlspecialchars(trim(strip_tags($zmienna)))); 
}

function purify($text=''){
	global $ustawienia, $purifier;
	require_once realpath(dirname(__FILE__)).'/../config/htmlpurifier.php';
	return $purifier->purify($text);
}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); 
    $alphaLength = strlen($alphabet) - 1; 
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); 
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
