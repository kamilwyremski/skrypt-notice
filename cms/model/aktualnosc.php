<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_aktualnosc' and isset($_POST['tytul']) and $_POST['tytul']!='' and isset($_POST['tresc']) and isset($_POST['miniaturka']) and isset($_POST['krotki_opis']) and isset($_POST['keywords']) and isset($_POST['description'])){
		if(isset($_POST['id'])){
			mysql_query('update '.$prefiks_tabel.'aktualnosci set tytul="'.filtruj($_POST['tytul']).'", prosty_tytul="'.prosta_nazwa(filtruj($_POST['tytul'])).'", miniaturka="'.filtruj($_POST['miniaturka']).'", krotki_opis="'.htmlspecialchars($_POST['krotki_opis']).'", tresc="'.htmlspecialchars($_POST['tresc']).'", keywords="'.filtruj(strip_tags($_POST['keywords'])).'", description="'.filtruj(strip_tags($_POST['description'])).'" where id="'.filtruj($_POST['id']).'" limit 1');
		}else{
			mysql_query('INSERT INTO `'.$prefiks_tabel.'aktualnosci`(`tytul`, `prosty_tytul`, `miniaturka`, `krotki_opis`, `tresc`, `keywords`, `description`, `data`) VALUES ("'.filtruj($_POST['tytul']).'", "'.prosta_nazwa(filtruj($_POST['tytul'])).'", "'.filtruj($_POST['miniaturka']).'", "'.htmlspecialchars($_POST['krotki_opis']).'", "'.htmlspecialchars($_POST['tresc']).'", "'.filtruj(strip_tags($_POST['keywords'])).'", "'.filtruj(strip_tags($_POST['description'])).'", "'.date("Y-m-d H:i:s").'")');
			header('Location: ?akcja=aktualnosc&id='.mysql_insert_id().'');
		}
	}
	
	if(isset($_GET['id'])){
		$aktualnosc = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'aktualnosci where id="'.filtruj($_GET['id']).'" limit 1'));
		if($aktualnosc!=''){
			$aktualnosc['tresc'] = htmlspecialchars_decode($aktualnosc['tresc']);
			$smarty->assign("aktualnosc", $aktualnosc);
		}
	}
	
}else{
	die('Brak dostepu!');
}
