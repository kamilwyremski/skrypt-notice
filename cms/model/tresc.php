<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_tresc' and isset($_POST['prosta_nazwa']) and isset($_POST['tresc']) and isset($_POST['keywords']) and isset($_POST['description'])){
		mysql_query('update '.$prefiks_tabel.'tresci set tresc="'.htmlspecialchars($_POST['tresc']).'", keywords="'.filtruj(strip_tags($_POST['keywords'])).'", description="'.filtruj(strip_tags($_POST['description'])).'" where prosta_nazwa="'.filtruj($_POST['prosta_nazwa']).'" limit 1');
	}
	
	if(isset($_GET['nazwa'])){
		$tresc = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'tresci where prosta_nazwa="'.filtruj($_GET['nazwa']).'" limit 1'));
		if($tresc!=''){
			$tresc['tresc'] = htmlspecialchars_decode($tresc['tresc']);
			$smarty->assign("tresc", $tresc);
		}
	}
	
}else{
	die('Brak dostepu!');
}
