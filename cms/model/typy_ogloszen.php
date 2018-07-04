<?php

if(isset($cms_login)){

	if(isset($_POST['akcja'])){
		if($_POST['akcja']=='dodaj' and isset($_POST['nazwa']) and $_POST['nazwa']!=''){
			mysql_query('INSERT INTO `'.$prefiks_tabel.'typy_ogloszen`(`nazwa`, `prosta_nazwa`) VALUES ("'.filtruj($_POST['nazwa']).'", "'.prosta_nazwa(filtruj($_POST['nazwa'])).'")');
		}elseif($_POST['akcja']=='edytuj' and isset($_POST['nazwa']) and $_POST['nazwa']!='' and isset($_POST['id'])){
			mysql_query('UPDATE `'.$prefiks_tabel.'typy_ogloszen` SET `nazwa`="'.filtruj($_POST['nazwa']).'", `prosta_nazwa`="'.prosta_nazwa(filtruj($_POST['nazwa'])).'" WHERE `id`="'.filtruj($_POST['id']).'" limit 1');
		}elseif($_POST['akcja']=='usun' and isset($_POST['id'])){
			mysql_query('DELETE FROM `'.$prefiks_tabel.'typy_ogloszen` WHERE `id`="'.filtruj($_POST['id']).'" limit 1');
		}
	}

	$q = mysql_query('select id, nazwa from '.$prefiks_tabel.'typy_ogloszen order by nazwa');
	while($dane = mysql_fetch_array($q)){$typy_ogloszen[] = $dane;}
	if(isset($typy_ogloszen)){$smarty->assign("typy_ogloszen", $typy_ogloszen);}
	
}else{
	die('Brak dostepu!');
}
