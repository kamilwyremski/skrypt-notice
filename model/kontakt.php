<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

$kontakt = mysql_fetch_assoc(mysql_query('select tresc, keywords, description from '.$prefiks_tabel.'tresci where prosta_nazwa="kontakt" limit 1'));
$kontakt['tresc'] = htmlspecialchars_decode($kontakt['tresc']);
$smarty->assign("kontakt", $kontakt);
$nawigacja[] = array('url'=>'kontakt', 'nazwa'=>'Kontakt');
if($kontakt['keywords']!=''){$ustawienia['keywords'] = $kontakt['keywords'];}
if($kontakt['description']!=''){
	$ustawienia['description'] = $kontakt['description'];
}else{
	$ustawienia['description'] = str_replace(PHP_EOL, '',substr(strip_tags($kontakt['tresc']),0,200));
}

pobierz_slider_dol();
