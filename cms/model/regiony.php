<?php

if(isset($cms_login)){

	if(isset($_POST['akcja'])){
		if($_POST['akcja']=='dodaj' and isset($_POST['nazwa']) and $_POST['nazwa']!=''){
			mysql_query('INSERT INTO `'.$prefiks_tabel.'regiony`(`nazwa`, `prosta_nazwa`) VALUES ("'.filtruj($_POST['nazwa']).'", "'.prosta_nazwa(filtruj($_POST['nazwa'])).'")');
		}elseif($_POST['akcja']=='edytuj' and isset($_POST['nazwa']) and $_POST['nazwa']!='' and isset($_POST['id'])){
			mysql_query('UPDATE `'.$prefiks_tabel.'regiony` SET `nazwa`="'.filtruj($_POST['nazwa']).'", `prosta_nazwa`="'.prosta_nazwa(filtruj($_POST['nazwa'])).'" WHERE `id`="'.filtruj($_POST['id']).'" limit 1');
		}elseif($_POST['akcja']=='usun' and isset($_POST['id'])){
			mysql_query('DELETE FROM `'.$prefiks_tabel.'regiony2` WHERE `region_id`="'.filtruj($_POST['id']).'"');
			mysql_query('DELETE FROM `'.$prefiks_tabel.'regiony` WHERE `id`="'.filtruj($_POST['id']).'" limit 1');
		}elseif($_POST['akcja']=='zmien_nazwe' and isset($_POST['regiony_nazwa']) and $_POST['regiony_nazwa']!=''){
			mysql_query('UPDATE `'.$prefiks_tabel.'ustawienia` SET `wartosc`="'.filtruj($_POST['regiony_nazwa']).'" WHERE `nazwa`="regiony_nazwa" limit 1');
			mysql_query('UPDATE `'.$prefiks_tabel.'ustawienia` SET `wartosc`="'.filtruj($_POST['regiony2_nazwa']).'" WHERE `nazwa`="regiony2_nazwa" limit 1');
			pobierz_ustawienia();
		}
	}

	$q = mysql_query('select id, nazwa from '.$prefiks_tabel.'regiony order by nazwa');
	while($dane = mysql_fetch_array($q)){$regiony[] = $dane;}
	if(isset($regiony)){$smarty->assign("regiony", $regiony);}
	
}else{
	die('Brak dostepu!');
}
