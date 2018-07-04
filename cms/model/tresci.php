<?php

if(isset($cms_login)){

	$q = mysql_query('select nazwa, prosta_nazwa from '.$prefiks_tabel.'tresci');
	while($dane = mysql_fetch_assoc($q)){$tresci[] = $dane;}
	if(isset($tresci)){$smarty->assign("tresci", $tresci);}

}else{
	die('Brak dostepu!');
}
