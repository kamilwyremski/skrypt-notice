<?php

header('Content-Type: text/html; charset=utf-8');

include('../config/db.php');
include('funkcje.php');
include('maile.php');

if(isset($_POST['akcja']) and $_POST['akcja'] == 'formularz_kontaktowy' and isset($_POST['imie']) and $_POST['imie']!='' and isset($_POST['email']) and $_POST['email']!='' and isset($_POST['wiadomosc']) and $_POST['wiadomosc']!='' and isset($_POST['temat'])){
	
	if(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'czarna_lista where email="'.filtruj($_POST['email']).'" limit 1'))){
		echo(false);
	}else{
	
		if(wyslij_mail('formularz_kontaktowy',$ustawienia['email'],array('imie'=>$_POST['imie'],'email'=>$_POST['email'],'temat'=>$_POST['temat'],'wiadomosc'=>$_POST['wiadomosc']))){
			echo(true);
		}else{
			echo(false);
		}
		
	}	
}
	
