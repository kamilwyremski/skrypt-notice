<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

header('HTTP/1.0 404 Not Found');

$nawigacja[] = array('url'=>'404', 'nazwa'=>'Strona błędu 404');

$ustawienia['title'] = "Błąd 404 - ".$ustawienia['title'];
$ustawienia['description'] = "Błąd 404 - strona nie istnieje. ".$ustawienia['description'];
	

