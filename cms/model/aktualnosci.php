<?php
if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='usun' and isset($_POST['id'])){
		mysql_query('delete from '.$prefiks_tabel.'aktualnosci where id="'.filtruj($_POST['id']).'" limit 1');
	}
	
	$q = mysql_query('select id, tytul, prosty_tytul, data from '.$prefiks_tabel.'aktualnosci order by data desc');
	while($dane = mysql_fetch_assoc($q)){$aktualnosci[] = $dane;}
	if(isset($aktualnosci)){$smarty->assign("aktualnosci", $aktualnosci);}

}else{
	die('Brak dostepu!');
}
