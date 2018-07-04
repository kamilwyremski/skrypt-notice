<?php

if(isset($cms_login)){

	if(isset($_POST['akcja'])){
		if($_POST['akcja']=='usun' and isset($_POST['id'])){
			mysql_query('delete from '.$prefiks_tabel.'opcje_dodatkowe where id="'.filtruj($_POST['id']).'" limit 1');
			mysql_query('delete from '.$prefiks_tabel.'wartosci_opcji where opcja_id="'.filtruj($_POST['id']).'"');
		}
	}

	$q = mysql_query('select * from '.$prefiks_tabel.'opcje_dodatkowe order by pozycja');
	while($dane = mysql_fetch_assoc($q)){$opcje_dodatkowe[] = $dane;}
	if(isset($opcje_dodatkowe)){$smarty->assign("opcje_dodatkowe", $opcje_dodatkowe);}

}else{
	die('Brak dostepu!');
}
