<?php

if(isset($cms_login)){

	$ile_na_strone = 100;
	
	$q = mysql_query('select adresat, akcja, ip, data from '.$prefiks_tabel.'logi_email order by '.sortuj('data desc').' limit '.policz_strony($prefiks_tabel.'logi_email',$ile_na_strone).','.$ile_na_strone.'');
	while($dane = mysql_fetch_assoc($q)){$logi_maile[] = $dane;}
	if(isset($logi_maile)){$smarty->assign("logi_maile", $logi_maile);}
	
}else{
	die('Brak dostepu!');
}
