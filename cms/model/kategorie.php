<?php
if(isset($cms_login)){

	function skasuj_podkategorie($id){
		global $prefiks_tabel;
		$q = mysql_query('select id from '.$prefiks_tabel.'kategorie where kategoria="'.$id.'"');
		while($dane = mysql_fetch_assoc($q)){
			skasuj_podkategorie($dane['id']);
		}
		mysql_query('delete from '.$prefiks_tabel.'kategorie where kategoria="'.$id.'"');
	}

	if(isset($_POST['akcja'])){
		if($_POST['akcja']=='usun' and isset($_POST['id'])){
			$id = filtruj($_POST['id']);
			skasuj_podkategorie($id);
			mysql_query('delete from '.$prefiks_tabel.'kategorie where id="'.$id.'" limit 1');
		}
		policz_podkategorie();
	}
	
	if(isset($_GET['kategoria']) and $_GET['kategoria']>0){
		$kategoria = filtruj($_GET['kategoria']);
		$kategoria_dane = mysql_fetch_assoc(mysql_query('select id, nazwa, kategoria from '.$prefiks_tabel.'kategorie where id="'.$kategoria.'" limit 1'));
		$smarty->assign("kategoria",$kategoria_dane);
		
		$nawigacja[] = array('nazwa'=>$kategoria_dane['nazwa'], 'url'=>'?akcja=kategorie&kategoria='.$kategoria_dane['id']);
		
		$nawigacja_nadkategoria = $kategoria_dane['kategoria'];
		while($nawigacja_nadkategoria!=0){
			$q = mysql_query('select id, nazwa, prosta_nazwa, kategoria from '.$prefiks_tabel.'kategorie where id = "'.$nawigacja_nadkategoria.'"');
			while($dane = mysql_fetch_assoc($q)){
				$nawigacja_nadkategoria = $dane['kategoria'];
				$dane['url'] = '?akcja=kategorie&kategoria='.$dane['id'];
				$nawigacja[] = $dane;
			}
		}
		$nawigacja = array_reverse($nawigacja);
		$smarty->assign("nawigacja",$nawigacja);

	}else{
		$kategoria = 0;
	}
	$q = mysql_query('select * from '.$prefiks_tabel.'kategorie where kategoria="'.$kategoria.'" order by pozycja');
	while($dane = mysql_fetch_assoc($q)){$kategorie[] = $dane;}
	if(isset($kategorie)){$smarty->assign("kategorie", $kategorie);}

}else{
	die('Brak dostepu!');
}
