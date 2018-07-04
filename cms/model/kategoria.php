<?php

if(isset($cms_login)){

	if(isset($_POST['akcja'])){
		
		if($_POST['akcja']=='zapisz_kategorie' and isset($_POST['nazwa']) and $_POST['nazwa']!='' and isset($_POST['oplata']) and isset($_POST['keywords']) and isset($_POST['description'])){
			
			$kategoria_nadrzedna = 0;

			if(isset($_GET['id']) and $_GET['id']>0){
				$id_kategorii = filtruj($_GET['id']);
				$kategoria_nadrzedna = mysql_fetch_assoc(mysql_query('select kategoria from '.$prefiks_tabel.'kategorie where id="'.$id_kategorii.'" limit 1'))['kategoria'];
			}else{
				if(isset($_GET['kategoria']) and $_GET['kategoria']>0){
					$kategoria_nadrzedna = filtruj($_GET['kategoria']);
				}
				mysql_query('INSERT INTO `'.$prefiks_tabel.'kategorie`(`pozycja`, `kategoria`) VALUES ("'.pozycja($prefiks_tabel.'kategorie', ' where kategoria="'.$kategoria_nadrzedna.'"').'", "'.$kategoria_nadrzedna.'")');
				$id_kategorii = mysql_insert_id();
			}
			
			mysql_query('UPDATE `'.$prefiks_tabel.'kategorie` set `nazwa`="'.filtruj($_POST['nazwa']).'",`prosta_nazwa`="'.prosta_nazwa(filtruj($_POST['nazwa'])).'", oplata="'.filtruj($_POST['oplata']).'", keywords="'.filtruj(strip_tags($_POST['keywords'])).'", description="'.filtruj(strip_tags($_POST['description'])).'", glowna_opis="'.filtruj($_POST['glowna_opis']).'", miniaturka="'.filtruj($_POST['miniaturka']).'" WHERE id="'.$id_kategorii.'" limit 1');
			
			policz_podkategorie();

			header('Location: ?akcja=kategorie&kategoria='.$kategoria_nadrzedna);
		}
	}
	
	if(isset($_GET['id']) and $_GET['id']>0){
		$id_kategorii = filtruj($_GET['id']);
		$kategoria = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'kategorie where id="'.$id_kategorii.'" limit 1'));
		if($kategoria){
			$kategoria['glowna_opis'] = htmlspecialchars_decode($kategoria['glowna_opis']);
			$smarty->assign("kategoria", $kategoria);
			
		}
	}
	
	if(isset($_GET['kategoria']) and $_GET['kategoria']>0){
		$kategoria_nadrzedna = filtruj($_GET['kategoria']);
		$kategoria_nadrzedna_dane = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'kategorie where id="'.$kategoria_nadrzedna.'" limit 1'));
		if($kategoria_nadrzedna_dane){
			$smarty->assign("kategoria_nadrzedna", $kategoria_nadrzedna_dane);
			
			$nawigacja[] = array('nazwa'=>$kategoria_nadrzedna_dane['nazwa'], 'url'=>'?akcja=kategorie&kategoria='.$kategoria_nadrzedna_dane['id']);
		
			$nawigacja_nadkategoria = $kategoria_nadrzedna_dane['kategoria'];
			while($nawigacja_nadkategoria!=0){
				$q = mysql_query('select id, nazwa, prosta_nazwa, kategoria from '.$prefiks_tabel.'kategorie where id = "'.$nawigacja_nadkategoria.'"');
				if(!mysql_num_rows($q)){$nawigacja_nadkategoria = 0;}
				while($dane = mysql_fetch_assoc($q)){
					$nawigacja_nadkategoria = $dane['kategoria'];
					$dane['url'] = '?akcja=kategorie&kategoria='.$dane['id'];
					$nawigacja[] = $dane;
				}
			}
			$nawigacja = array_reverse($nawigacja);
			$smarty->assign("nawigacja",$nawigacja);
		}
	}

}else{
	die('Brak dostepu!');
}
