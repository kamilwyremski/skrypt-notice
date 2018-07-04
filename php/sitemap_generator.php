<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

$memory = '64M'; 
$sitemapFile = "sitemap.xml"; 
$url = $ustawienia['base_url'].'/';

$sitemapFile = dirname(__FILE__)."/../".$sitemapFile;

chmod($sitemapFile, 0777);

$wpisy = array();

$wpisy[] = array('priority'=>'1','url'=>'');
$wpisy[] = array('priority'=>'0.8','url'=>'logowanie');
$wpisy[] = array('priority'=>'0.9','url'=>'rejestracja');
$wpisy[] = array('priority'=>'0.5','url'=>'reset_hasla');
$wpisy[] = array('priority'=>'0.1','url'=>'regulamin');
$wpisy[] = array('priority'=>'0.1','url'=>'polityka_prywatnosci');
$wpisy[] = array('priority'=>'0.7','url'=>'dodaj');
$wpisy[] = array('priority'=>'0.4','url'=>'moje_ogloszenia');
$wpisy[] = array('priority'=>'0.4','url'=>'ustawienia');
$wpisy[] = array('priority'=>'0.5','url'=>'pomoc');
$wpisy[] = array('priority'=>'0.5','url'=>'onas');
$wpisy[] = array('priority'=>'0.5','url'=>'platnosci');
$wpisy[] = array('priority'=>'0.3','url'=>'kontakt');
if($ustawienia['program_partnerski']){
	$wpisy[] = array('priority'=>'0.5','url'=>'program_partnerski');
	$wpisy[] = array('priority'=>'0.5','url'=>'moj_program_partnerski');
}

$q = mysql_query('select id, prosty_tytul from '.$prefiks_tabel.'ogloszenia where aktywna=1 order by id desc');
while($dane = mysql_fetch_assoc($q)){
	$wpis['priority'] = '0.9';
	$wpis['url'] = $dane['id'].','.$dane['prosty_tytul'];
	$wpisy[] = $wpis;
}
$q = mysql_query('select id, prosty_tytul from '.$prefiks_tabel.'aktualnosci order by id desc');
while($dane = mysql_fetch_assoc($q)){
	$wpis['priority'] = '0.7';
	$wpis['url'] = 'aktualnosc,'.$dane['id'].','.$dane['prosty_tytul'];
	$wpisy[] = $wpis;
}
$q = mysql_query('select id, prosta_nazwa from '.$prefiks_tabel.'kategorie');
while($dane = mysql_fetch_assoc($q)){
	$wpis['priority'] = '0.5';
	$wpis['url'] = 'kategoria,'.$dane['id'].','.$dane['prosta_nazwa'];
	$wpisy[] = $wpis;
}

ini_set('memory_limit', $memory);

$fh = fopen($sitemapFile, 'w');

$html = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">';
fwrite($fh, $html);

foreach($wpisy as $wpis){
	$entry = "\n";
	$entry .= '<url>';
	$entry .= "\n";
	$entry .= '  <loc>'.$url.$wpis['url'].'</loc>';
	$entry .= "\n";
	$entry .= '  <changefreq>daily</changefreq>';
	$entry .= "\n";
	$entry .= '  <priority>'.$wpis['priority'].'</priority>';
	$entry .= "\n";
	$entry .= '</url>';
	fwrite($fh, $entry);
}

$html = '
</urlset>';
fwrite($fh, $html);
fclose($fh);

chmod($sitemapFile, 0644);


