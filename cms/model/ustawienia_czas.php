<?php

if(isset($cms_login)){

	if(isset($_POST['akcja'])){
		if($_POST['akcja']=='dodaj' and isset($_POST['dlugosc']) and $_POST['dlugosc']>0 and isset($_POST['cena'])){
			mysql_query('INSERT INTO `'.$prefiks_tabel.'czas_ogloszen`(`dlugosc`, `cena`) VALUES ("'.filtruj($_POST['dlugosc']).'", "'.filtruj($_POST['cena']).'")');
		}elseif($_POST['akcja']=='edytuj' and isset($_POST['id']) and isset($_POST['dlugosc']) and $_POST['dlugosc']>0 and isset($_POST['cena'])){
			mysql_query('UPDATE `'.$prefiks_tabel.'czas_ogloszen` set `dlugosc`="'.filtruj($_POST['dlugosc']).'",`cena`="'.filtruj($_POST['cena']).'" WHERE id="'.filtruj($_POST['id']).'" limit 1');
		}elseif($_POST['akcja']=='usun' and isset($_POST['id'])){
			mysql_query('delete from '.$prefiks_tabel.'czas_ogloszen where id="'.filtruj($_POST['id']).'" limit 1');
		}elseif($_POST['akcja']=='zapisz_czas' and isset($_POST['odswiezanie_dni']) and $_POST['odswiezanie_dni']>0 and isset($_POST['odswiezanie_dni_przed']) and $_POST['odswiezanie_dni_przed']>=0 and isset($_POST['domyslny_czas']) and $_POST['domyslny_czas']>0){
			mysql_query('UPDATE `'.$prefiks_tabel.'ustawienia` set `wartosc`="'.filtruj($_POST['odswiezanie_dni']).'" WHERE nazwa="odswiezanie_dni" limit 1');
			mysql_query('UPDATE `'.$prefiks_tabel.'ustawienia` set `wartosc`="'.filtruj($_POST['odswiezanie_dni_przed']).'" WHERE nazwa="odswiezanie_dni_przed" limit 1');
			mysql_query('UPDATE `'.$prefiks_tabel.'ustawienia` set `wartosc`="'.filtruj($_POST['domyslny_czas']).'" WHERE nazwa="domyslny_czas" limit 1');
			pobierz_ustawienia();
		}
	}
	
	$q = mysql_query('select * from '.$prefiks_tabel.'czas_ogloszen order by dlugosc');
	while($dane = mysql_fetch_assoc($q)){$czas_ogloszen[] = $dane;}
	if(isset($czas_ogloszen)){$smarty->assign("czas_ogloszen", $czas_ogloszen);}

}else{
	die('Brak dostepu!');
}
