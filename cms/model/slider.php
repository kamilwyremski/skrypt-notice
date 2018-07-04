<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_slider' and isset($_POST['tresc'])){
		foreach($_POST['tresc'] as $key => $value){
			if(isset($_POST['aktywne'][$key])){$aktywne=1;}else{$aktywne=0;}
			mysql_query('update '.$prefiks_tabel.'slider set aktywne="'.$aktywne.'", tresc="'.htmlspecialchars($value).'" where id="'.filtruj($key).'" limit 1');
		}
	}
	
	$q = mysql_query('select * from '.$prefiks_tabel.'slider');
	while($dane = mysql_fetch_assoc($q)){
		$dane['tresc'] = htmlspecialchars_decode($dane['tresc']);
		$slider[] = $dane;
	}
	if(isset($slider)){$smarty->assign("slider", $slider);}
	
}else{
	die('Brak dostepu!');
}
