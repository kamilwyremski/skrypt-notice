<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if(isset($_GET['id'])){
	
	$id = filtruj($_GET['id']);

	if($id=='regulamin' or $id=='polityka_prywatnosci' or $id=='pomoc' or $id=='onas' or $id=='platnosci' or ($id=='program_partnerski' and $ustawienia['program_partnerski'])){
		$info = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'tresci where prosta_nazwa="'.$id.'" limit 1'));
		$info['tresc'] = htmlspecialchars_decode($info['tresc']);
		$smarty->assign("info", $info);
		$nawigacja[] = array('url'=>$id, 'nazwa'=>$info['nazwa']);
		$ustawienia['title'] = $info['nazwa']." - ".$ustawienia['title'];
		if($info['keywords']!=''){$ustawienia['keywords'] = $info['keywords'];}
		if($info['description']!=''){
			$ustawienia['description'] = $info['description'];
		}else{
			$ustawienia['description'] = str_replace(PHP_EOL, '',substr(strip_tags($info['tresc']),0,200));
		}
	}else{
		$strona = '404';
		include('model/404.php');
	}
}else{
	$strona = '404';
	include('model/404.php');
}

pobierz_slider_dol();
