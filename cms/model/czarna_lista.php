<?php

if(isset($cms_login)){

	if(isset($_POST['akcja'])){
		if($_POST['akcja']=='zapisz_czarna_lista_ip' and isset($_POST['czarna_lista_ip'])){
			mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.filtruj($_POST['czarna_lista_ip']).'" where nazwa="czarna_lista_ip" limit 1');
			pobierz_ustawienia();
		}elseif($_POST['akcja']=='dodaj_czarna_lista' and isset($_POST['email']) and $_POST['email']!=''){
			mysql_query('INSERT INTO `'.$prefiks_tabel.'czarna_lista`(`email`, `data`) VALUES ("'.filtruj($_POST['email']).'","'.date("Y-m-d H:i:s").'")');
		}
	}
	
	$ile_na_strone = 100;
	
	$q = mysql_query('select email, data from '.$prefiks_tabel.'czarna_lista order by '.sortuj('email').' limit '.policz_strony($prefiks_tabel.'czarna_lista',$ile_na_strone).','.$ile_na_strone.'');
	while($dane = mysql_fetch_array($q)){$czarna_lista[] = $dane;}
	if(isset($czarna_lista)){$smarty->assign("czarna_lista", $czarna_lista);}
	
}else{
	die('Brak dostepu!');
}
