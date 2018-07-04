<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($_GET['id'])){
	$aktualnosc = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'aktualnosci where id="'.filtruj($_GET['id']).'" limit 1'));
	if($aktualnosc!=''){
		$aktualnosc['tresc'] = htmlspecialchars_decode($aktualnosc['tresc']);
		$smarty->assign("aktualnosc", $aktualnosc);
		$nawigacja[] = array('url'=>'aktualnosci', 'nazwa'=>'Aktualności');
		$nawigacja[] = array('url'=>'aktualnosc,'.$aktualnosc['id'].','.$aktualnosc['prosty_tytul'], 'nazwa'=>$aktualnosc['tytul']);
		$ustawienia['title'] = $aktualnosc['tytul']." - ".$ustawienia['title'];
		if($aktualnosc['keywords']!=''){$ustawienia['keywords'] = $aktualnosc['keywords'];}
		if($aktualnosc['description']!=''){
			$ustawienia['description'] = $aktualnosc['description'];
		}else{
			$ustawienia['description'] = str_replace(PHP_EOL, '',substr(strip_tags($aktualnosc['tresc']),0,200));
		}
		if($aktualnosc['miniaturka']!=''){$ustawienia['logo_facebook'] = $aktualnosc['miniaturka'];}
	}else{
		$strona = '404';
		include('model/404.php');
	}
}else{
	$strona = '404';
	include('model/404.php');
}

pobierz_slider_dol();
