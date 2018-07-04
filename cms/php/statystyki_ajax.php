<?php

session_start(); 

require_once('../../libs/Smarty.class.php');
$smarty = new Smarty();

include('../../config/db.php');
include('globalne.php');
include('logowanie.php');

if(isset($cms_login) and isset($_POST['select_1']) and isset($_POST['select_2']) and isset($_POST['data_od']) and isset($_POST['data_do'])){

	$data_od = filtruj($_POST['data_od']);
	$data_do = filtruj($_POST['data_do']);

	function wykres_select($select){
		global $prefiks_tabel, $data_od, $data_do;
		$wynik = array();
		
		switch($select){
			case 'logowania': 
				$q = mysql_query('select data, count(id) from '.$prefiks_tabel.'logi_uzytkownicy where data>="'.$data_od.'" and data<="'.$data_do.' 23:59" group by date(data)');
				break;
			case 'unikalne_logowania': 
				$q = mysql_query('select data, count(id) from (select id, data from '.$prefiks_tabel.'logi_uzytkownicy where data>="'.$data_od.'" and data<="'.$data_do.' 23:59" group by date(data), id_uzytkownika) t1 group by date(data)');
				break;
			case 'rejestracje': 
				$q = mysql_query('select data, count(id) from '.$prefiks_tabel.'uzytkownicy where data>="'.$data_od.'" and data<="'.$data_do.' 23:59" group by date(data)');
				break;
			case 'aktywacje': 
				$q = mysql_query('select data, count(id) from '.$prefiks_tabel.'uzytkownicy where data_aktywacji>="'.$data_od.'" and data_aktywacji<="'.$data_do.' 23:59" group by date(data_aktywacji)');
				break;
			case 'dodanie': 
				$q = mysql_query('select data, count(id) from '.$prefiks_tabel.'ogloszenia where data>="'.$data_od.'" and data<="'.$data_do.' 23:59" group by date(data)');
				break;
			case 'start': 
				$q = mysql_query('select start, count(id) from '.$prefiks_tabel.'ogloszenia where start>="'.$data_od.'" and start<="'.$data_do.' 23:59" group by date(start)');
				break;
			case 'wyswietlenia': 
				$q = mysql_query('select data, count(id) from '.$prefiks_tabel.'logi_wyswietlenia where data>="'.$data_od.'" and data<="'.$data_do.' 23:59" group by date(data)');
				break;
			default:
				$q = mysql_query('select data, id from '.$prefiks_tabel.'logi_wyswietlenia where 1=2');
		}
		while($dane = mysql_fetch_row($q)){
			$dane[1] = (int) $dane[1];
			$wynik[] = $dane;
		}
		if(empty($wynik)){
			$wynik[] = array();
		}
		return $wynik;
	}

	$statystyki = array();
	$statystyki[] = wykres_select($_POST['select_1']);
	$statystyki[] = wykres_select($_POST['select_2']);
	echo (json_encode($statystyki));
}
