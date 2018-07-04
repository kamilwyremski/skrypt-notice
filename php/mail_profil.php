<?php

header('Content-Type: text/html; charset=utf-8');

include('../config/db.php');
include('funkcje.php');
include('maile.php');

if($ustawienia['pokaz_formularz_kontaktowy_profil'] and isset($_POST['akcja']) and $_POST['akcja'] == 'formularz_kontaktowy' and isset($_POST['imie']) and $_POST['imie']!='' and isset($_POST['email']) and $_POST['email']!='' and isset($_POST['wiadomosc']) and $_POST['wiadomosc']!='' and isset($_POST['id_profilu']) and $_POST['id_profilu']>0){
	
	if(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'czarna_lista where email="'.filtruj($_POST['email']).'" limit 1'))){
		echo(false);
	}else{
		$id_profilu = filtruj($_POST['id_profilu']);
		$dane_profilu = mysql_fetch_assoc(mysql_query('select email, login from '.$prefiks_tabel.'uzytkownicy where id = "'.$id_profilu.'" and aktywny=1 limit 1'));
		
		if(wyslij_mail('formularz_kontaktowy',$dane_profilu['email'],array('imie'=>$_POST['imie'],'email'=>$_POST['email'],'temat'=>'Profil użytkownika '.$dane_profilu['login'],'wiadomosc'=>$_POST['wiadomosc']))){
			echo(true);
		}else{
			echo(false);
		}

	}
	
}
	
