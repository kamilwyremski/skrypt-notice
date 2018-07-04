<?php

if(isset($cms_login)){
	
	if(isset($_POST['akcja']) and $_POST['akcja']=='pp_realizacja' and isset($_POST['id']) and isset($_POST['status'])){
		if($_POST['status']=='zrealizowany' and isset($_POST['kwota'])){
			mysql_query('update '.$prefiks_tabel.'pp_wyplaty set status="zrealizowany", kwota="'.filtruj($_POST['kwota']).'", data_realizacji="'.date("Y-m-d H:i:s").'" where id="'.filtruj($_POST['id']).'" limit 1');
		}elseif($_POST['status']=='odmowa'){
			mysql_query('update '.$prefiks_tabel.'pp_wyplaty set status="odmowa", data_realizacji="'.date("Y-m-d H:i:s").'" where id="'.filtruj($_POST['id']).'" limit 1');
		}
	}
	
	$ile_na_strone = 100;

	$q = mysql_query('select * from '.$prefiks_tabel.'pp_wyplaty order by '.sortuj('id desc').' limit '.policz_strony($prefiks_tabel.'pp_wyplaty',$ile_na_strone).','.$ile_na_strone.'');
	while($dane = mysql_fetch_array($q)){
		$dane['login'] = mysql_fetch_array(mysql_query('select login from '.$prefiks_tabel.'uzytkownicy where id="'.$dane['id_uzytkownika'].'" limit 1'))['login'];
		$pp_wyplaty[] = $dane;
	}
	if(isset($pp_wyplaty)){$smarty->assign("pp_wyplaty", $pp_wyplaty);}
	
	
}else{
	die('Brak dostepu!');
}
