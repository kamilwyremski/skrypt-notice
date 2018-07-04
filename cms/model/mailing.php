<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='wyslij_mailing' and isset($_POST['grupa']) and isset($_POST['tytul']) and $_POST['tytul']!='' and isset($_POST['tresc']) and $_POST['tresc']!=''){
	
		$wyslano_mailing['tytul'] = $_POST['tytul'];
		$wyslano_mailing['tresc'] = $_POST['tresc'];
		$wyslano_mailing['ilosc'] = 0;

		if($_POST['grupa']=='wszyscy'){
			$wyslano_mailing['grupa'] = 'wszyscy';
			$q = mysql_query('select email, kod from (select email, "" as kod from '.$prefiks_tabel.'uzytkownicy where aktywny=1 union select email, kod from '.$prefiks_tabel.'newsletter where aktywny = 1) as a group by email');
		}elseif($_POST['grupa']=='newsletter'){
			$wyslano_mailing['grupa'] = 'newsletter';
			$q = mysql_query('select email, kod from '.$prefiks_tabel.'newsletter where aktywny = 1');
		}elseif($_POST['grupa']=='uzytkownicy'){
			$wyslano_mailing['grupa'] = 'użytkownicy';
			$q = mysql_query('select email, "" as kod from '.$prefiks_tabel.'uzytkownicy where aktywny=1');
		}
		while($dane = mysql_fetch_array($q)){
			if($dane['kod']!=''){
				$message = $_POST['tresc'].'<br><p>Aby wypisać się z newslettera kliknij w następujący link: <a href="'.$ustawienia['base_url'].'?akcja=anuluj_newsletter&kod='.$dane['kod'].'">'.$ustawienia['base_url'].'?akcja=anuluj_newsletter&kod='.$dane['kod'].'</a></p>';
			}else{
				$message = $_POST['tresc'];
			}
			
			if(wyslij_mail('mailing',$dane['email'],array('message'=>$message, 'subject'=>$_POST['tytul']))){
				$wyslano_mailing['ilosc']++;
			}
		}
		$smarty->assign("wyslano_mailing", $wyslano_mailing);
		
	}else{
		
		$statystyki['wszyscy'] = mysql_num_rows(mysql_query('select email from (select email from '.$prefiks_tabel.'uzytkownicy where aktywny = 1 union select email from '.$prefiks_tabel.'newsletter where aktywny = 1) as a group by email'));
		$statystyki['newsletter'] = mysql_num_rows(mysql_query('select email from '.$prefiks_tabel.'newsletter where aktywny = 1'));
		$statystyki['uzytkownicy'] = mysql_num_rows(mysql_query('select email from '.$prefiks_tabel.'uzytkownicy where aktywny = 1'));
		$smarty->assign("statystyki", $statystyki);
	
	}
	
}else{
	die('Brak dostepu!');
}
