<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

$ile_na_strone = 10;

$q = mysql_query('select id, tytul, prosty_tytul, miniaturka, krotki_opis, data from '.$prefiks_tabel.'aktualnosci order by data desc limit '.policz_strony($prefiks_tabel.'aktualnosci',$ile_na_strone).','.$ile_na_strone.'');
while($dane = mysql_fetch_assoc($q)){$aktualnosci[] = $dane;}
if(isset($aktualnosci)){$smarty->assign("aktualnosci", $aktualnosci);}

$nawigacja[] = array('url'=>'aktualnosci', 'nazwa'=>'Aktualności');

pobierz_slider_dol();
