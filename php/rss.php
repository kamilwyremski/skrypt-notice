<?php

include('../config/db.php');
include('../php/funkcje.php');

if(!$ustawienia['rss']){
	die('Kanał RSS został wyłączony');
}

function xmlEscape($string) {
	return $string;
	return str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $string);
}

header("Content-Type: application/xml; charset=utf-8");

$warunek = '';
if(isset($_GET['kategoria'])){
	$id_kategorii = intval($_GET['kategoria']);
	$wynik = mysql_fetch_assoc(mysql_query('select nazwa, podkategorie, keywords, description from '.$prefiks_tabel.'kategorie where id="'.$id_kategorii.'" limit 1'));
	if($wynik!=''){
		$podkategorie = $wynik['podkategorie'];
		if($podkategorie==''){
			$warunek = ' and kategoria='.$id_kategorii.' ';
		}else{
			$warunek = ' and (kategoria='.$id_kategorii.' or kategoria='.str_replace(",",' or kategoria=',$podkategorie).') ';
		}
		$ustawienia['title'] = $wynik['nazwa'].' - '.$ustawienia['title'];
		if($wynik['description']!=''){
			$ustawienia['description'] = $wynik['description'];
		}else{
			$ustawienia['description'] = 'Ogłoszenia z kategorii: '.$wynik['nazwa'].'. '.$ustawienia['description'];
		}
	}
}elseif(isset($_GET['profil'])){
	$profil = filtruj($_GET['profil']);
	$wynik = mysql_fetch_assoc(mysql_query('select id, login from '.$prefiks_tabel.'uzytkownicy where login="'.$profil.'" limit 1'));
	if($wynik!=''){
		$warunek = ' and id_uzytkownika="'.$wynik['id'].'" ';
		$ustawienia['title'] = $wynik['login'].' - '.$ustawienia['title'];
		$ustawienia['description'] = 'Ogłoszenia użytkownika: '.$wynik['login'].'. '.$ustawienia['description'];
	}
}

$rssfeed = '<?xml version="1.0" encoding="utf-8"?>';
$rssfeed .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
$rssfeed .= '<channel>';
$rssfeed .= '<title>'.$ustawienia['title'].'</title>';
$rssfeed .= '<link>'.$ustawienia['base_url'].'</link>';
if($ustawienia['logo']!=''){
	$rssfeed .= ' <image>
		<title>'.$ustawienia['title'].'</title>
		<url>'.$ustawienia['base_url'].$ustawienia['logo'].'</url>
		<link>'.$ustawienia['base_url'].'</link>
	</image>';
}
$rssfeed .= '<description>'.$ustawienia['description'].'</description>';
$rssfeed .= '<language>pl-pl</language>';
$rssfeed .= '<lastBuildDate>'.date("D, d M Y H:i:s O").'</lastBuildDate>';
$rssfeed .= '<atom:link href="'.$ustawienia['base_url'].'/php/rss.php" rel="self" type="application/rss+xml" />';

$q = mysql_query('select id, tytul, prosty_tytul, opis, start from '.$prefiks_tabel.'ogloszenia where aktywna=1 '.$warunek.' order by start desc limit 20');
while($dane = mysql_fetch_assoc($q)){
	$rssfeed .= '<item>';
	$rssfeed .= '<title>'.$dane['tytul'].'</title>';
	$rssfeed .= '<link>'.$ustawienia['base_url'].'/'.$dane['id'].','.$dane['prosty_tytul'].'</link>';
	$rssfeed .= '<guid>'.$ustawienia['base_url'].'/'.$dane['id'].','.$dane['prosty_tytul'].'</guid>';
	$rssfeed .= '<pubDate>'.date("D, d M Y H:i:s O", strtotime($dane['start'])).'</pubDate>';
	$dane['miniaturka'] = mysql_fetch_assoc(mysql_query('select miniaturka from '.$prefiks_tabel.'zdjecia where id_ogloszenia="'.$dane['id'].'" limit 1'))['miniaturka'];
	$rssfeed .= '<description>';
	if($dane['miniaturka']){
		$rssfeed .= '&lt;br&gt;&lt;br&gt;&lt;a href="'.$ustawienia['base_url'].'/'.$dane['id'].','.$dane['prosty_tytul'].'"&gt;&lt;img src="'.$ustawienia['base_url'].'/'.$ustawienia['upload'].'/'.$dane['miniaturka'].'" height="80"/&gt;&lt;/&gt;';
	}
	$rssfeed .= '</description>';
	$rssfeed .= '</item>';
}
$rssfeed .= '</channel>';
$rssfeed .= '</rss>';

echo $rssfeed;
