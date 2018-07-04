<?php

session_start(); 

include('../config/db.php');

include('funkcje.php');
include('globalne.php');
include('logowanie.php');

if($ustawienia['program_partnerski'] and isset($uzytkownik) and isset($_POST['akcja'])){

	if($_POST['akcja']=='pp_statystyki' and isset($_POST['data_od']) and isset($_POST['data_do'])){
		$pp_statystyki['suma'] = 0;
		$q = mysql_query('select id_ogloszenia, id_uzytkownika, kwota, procent, prowizja, data from '.$prefiks_tabel.'pp_prowizje where id_polecajacego="'.$uzytkownik['id'].'" and data>="'.filtruj($_POST['data_od']).'" and data<="'.filtruj($_POST['data_do']).' 23:59" order by data desc');
		while($dane = mysql_fetch_assoc($q)){
			$dane['ogloszenie'] = mysql_fetch_assoc(mysql_query('select id, prosty_tytul, tytul from '.$prefiks_tabel.'ogloszenia where id="'.$dane['id_ogloszenia'].'" limit 1'));
			$dane['login'] = mysql_fetch_assoc(mysql_query('select login from '.$prefiks_tabel.'uzytkownicy where id="'.$dane['id_uzytkownika'].'" limit 1'))['login'];
			$data = date_create($dane['data']);
			$dane['data'] = date_format($data, 'd-m-Y');
			$pp_statystyki['suma'] += $dane['prowizja'];
			$dane['prowizja'] = number_format($dane['prowizja'], 2, ',', ' ').'&nbsp;'.$ustawienia['waluta'];
			$pp_statystyki['ogloszenia'][] = $dane;
		}
		
		$pp_statystyki['suma'] = number_format($pp_statystyki['suma'], 2, ',', ' ');
		echo (json_encode($pp_statystyki));
	}
}else{
	die('Brak dostępu!');
}
