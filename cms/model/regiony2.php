<?php

if(isset($cms_login)){

	if(isset($_GET['region_id'])){
		
		$region_id = filtruj($_GET['region_id']);
		$region = mysql_fetch_assoc(mysql_query('select nazwa from '.$prefiks_tabel.'regiony where id="'.$region_id.'" limit 1'));
		
		if($region){
			
			if(isset($_POST['akcja'])){
				if($_POST['akcja']=='dodaj' and isset($_POST['nazwa']) and $_POST['nazwa']!=''){
					mysql_query('INSERT INTO `'.$prefiks_tabel.'regiony2`(`nazwa`, `prosta_nazwa`, `region_id`) VALUES ("'.filtruj($_POST['nazwa']).'", "'.prosta_nazwa(filtruj($_POST['nazwa'])).'", "'.$region_id.'")');
				}elseif($_POST['akcja']=='edytuj' and isset($_POST['nazwa']) and $_POST['nazwa']!='' and isset($_POST['id'])){
					mysql_query('UPDATE `'.$prefiks_tabel.'regiony2` SET `nazwa`="'.filtruj($_POST['nazwa']).'", `prosta_nazwa`="'.prosta_nazwa(filtruj($_POST['nazwa'])).'" WHERE `id`="'.filtruj($_POST['id']).'" limit 1');
				}elseif($_POST['akcja']=='usun' and isset($_POST['id'])){
					mysql_query('DELETE FROM `'.$prefiks_tabel.'regiony2` WHERE `id`="'.filtruj($_POST['id']).'" limit 1');
				}
			}

			$smarty->assign("region", $region);
			
			$q = mysql_query('select id, nazwa from '.$prefiks_tabel.'regiony2 where region_id="'.$region_id.'" order by nazwa');
			while($dane = mysql_fetch_array($q)){$regiony2[] = $dane;}
			if(isset($regiony2)){$smarty->assign("regiony2", $regiony2);}
		}
	}
}else{
	die('Brak dostepu!');
}
