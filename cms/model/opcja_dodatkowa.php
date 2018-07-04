<?php

if(isset($cms_login)){

	if(isset($_GET['id']) and $_GET['id']!=''){
		$id = filtruj($_GET['id']);
	}
	
	if(isset($_POST['akcja'])){
		if($_POST['akcja']=='dodaj' and isset($_POST['nazwa']) and $_POST['nazwa']!='' and isset($_POST['rodzaj']) and $_POST['rodzaj']!=''){
			if(isset($_POST['wymagane'])){$wymagane=1;}else{$wymagane=0;}
			if(isset($_POST['wszystkie'])){
				$wszystkie = 1;
				$kategorie = '';
			}else{
				$wszystkie = 0;
				if(isset($_POST['kategorie'])){
					$kategorie = '-'.implode($_POST['kategorie'],'--').'-';
				}else{
					$kategorie = '';
				}
			}
			if($_POST['rodzaj']=='select' and isset($_POST['opcje_wyboru'])){
				$opcje_wyboru = filtruj($_POST['opcje_wyboru']);
			}else{
				$opcje_wyboru = '';
			}
			mysql_query('INSERT INTO `'.$prefiks_tabel.'opcje_dodatkowe`(`nazwa`, `pozycja`, `rodzaj`, `wymagane`, `kategorie`, `wszystkie`, `opcje_wyboru`) VALUES ("'.filtruj($_POST['nazwa']).'", "'.pozycja($prefiks_tabel.'opcje_dodatkowe').'", "'.filtruj($_POST['rodzaj']).'", "'.$wymagane.'", "'.$kategorie.'", "'.$wszystkie.'", "'.$opcje_wyboru .'")');
			header('Location: '.$ustawienia['base_url'].'/cms/?akcja=opcja_dodatkowa&id='.mysql_insert_id());
		}elseif($_POST['akcja']=='edytuj' and isset($id)){
			if(isset($_POST['wszystkie'])){
				$wszystkie = 1;
				$kategorie = '';
			}else{
				$wszystkie = 0;
				if(isset($_POST['kategorie'])){
					$kategorie = '-'.implode($_POST['kategorie'],'--').'-';
				}else{
					$kategorie = '';
				}
			}
			if(isset($_POST['specjalne'])){
				mysql_query('update `'.$prefiks_tabel.'opcje_dodatkowe` set kategorie="'.$kategorie.'", wszystkie="'.$wszystkie.'" where id="'.$id.'" limit 1');
			}else{
				if(isset($_POST['wymagane'])){$wymagane=1;}else{$wymagane=0;}
				if($_POST['rodzaj']=='select' and isset($_POST['opcje_wyboru'])){
					$opcje_wyboru = filtruj($_POST['opcje_wyboru']);
				}else{
					$opcje_wyboru = '';
				}
				mysql_query('update `'.$prefiks_tabel.'opcje_dodatkowe` set nazwa="'.filtruj($_POST['nazwa']).'", rodzaj="'.filtruj($_POST['rodzaj']).'", wymagane="'.$wymagane.'", kategorie="'.$kategorie.'", wszystkie="'.$wszystkie.'", opcje_wyboru="'.$opcje_wyboru .'" where id="'.$id.'" limit 1');
			}
		}
	}
	
	$kategorie = array();
	function pobierz_podkategorie($id="0", $glebokosc="0"){
		global $kategorie, $prefiks_tabel;
		$q = mysql_query('select id, nazwa, prosta_nazwa, ile, kategoria, podkategorie, oplata from '.$prefiks_tabel.'kategorie where kategoria="'.$id.'" order by nazwa');
		while($dane = mysql_fetch_assoc($q)){
			$glebokosc ++;
			$dane['glebokosc'] = $glebokosc;
			if($dane['kategoria']!=$id){
				$dane['nadkategorie'][] = $dane['id'];
			}
			$nadkategoria = $dane['kategoria'];
			while($nadkategoria!=0){
				$q2 = mysql_query('select id, kategoria from '.$prefiks_tabel.'kategorie where id = "'.$nadkategoria.'"');
				while($dane2 = mysql_fetch_assoc($q2)){
					$nadkategoria = $dane2['kategoria'];
					$dane['nadkategorie'][] = $dane2['id'];
				}
			}
			$dane['id_myslniki'] = '-'.$dane['id'].'-';
			$kategorie[] = $dane;
			pobierz_podkategorie($dane['id'], $glebokosc);
			$glebokosc --;
		}
	}
	pobierz_podkategorie();
	if(!empty($kategorie)){$smarty->assign("kategorie", $kategorie);}
	
	if(isset($id)){
		$opcja_dodatkowa = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'opcje_dodatkowe where id="'.$id.'" limit 1'));
		if($opcja_dodatkowa!=''){$smarty->assign("opcja_dodatkowa", $opcja_dodatkowa);}
	}
	

}else{
	die('Brak dostepu!');
}
