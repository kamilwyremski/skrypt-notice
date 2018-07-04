<?php

if(isset($cms_login)){

	$ile_na_strone = 100;
	
	$q = mysql_query('select wykorzystany, '.$prefiks_tabel.'reset_hasla.aktywny, '.$prefiks_tabel.'reset_hasla.kod, '.$prefiks_tabel.'reset_hasla.data, login, email from '.$prefiks_tabel.'reset_hasla, '.$prefiks_tabel.'uzytkownicy where id_uzytkownika = '.$prefiks_tabel.'uzytkownicy.id order by '.sortuj($prefiks_tabel.'reset_hasla.data desc').' limit '.policz_strony($prefiks_tabel.'reset_hasla, '.$prefiks_tabel.'uzytkownicy',$ile_na_strone,'id_uzytkownika = '.$prefiks_tabel.'uzytkownicy.id').','.$ile_na_strone.'');
	while($dane = mysql_fetch_assoc($q)){$logi_reset_hasla[] = $dane;}
	if(isset($logi_reset_hasla)){$smarty->assign("logi_reset_hasla", $logi_reset_hasla);}
	
}else{
	die('Brak dostepu!');
}
