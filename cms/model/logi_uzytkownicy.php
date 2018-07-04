<?php

if(isset($cms_login)){

	$ile_na_strone = 100;
	
	$q = mysql_query('select '.$prefiks_tabel.'logi_uzytkownicy.ip, '.$prefiks_tabel.'logi_uzytkownicy.data, email, login from '.$prefiks_tabel.'logi_uzytkownicy, '.$prefiks_tabel.'uzytkownicy where id_uzytkownika = '.$prefiks_tabel.'uzytkownicy.id order by '.sortuj($prefiks_tabel.'logi_uzytkownicy.data desc').' limit '.policz_strony($prefiks_tabel.'logi_uzytkownicy, '.$prefiks_tabel.'uzytkownicy',$ile_na_strone,'id_uzytkownika = '.$prefiks_tabel.'uzytkownicy.id').','.$ile_na_strone.'');
	while($dane = mysql_fetch_assoc($q)){$logi_uzytkownicy[] = $dane;}
	if(isset($logi_uzytkownicy)){$smarty->assign("logi_uzytkownicy", $logi_uzytkownicy);}
	
	
}else{
	die('Brak dostepu!');
}
