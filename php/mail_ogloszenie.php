<?php

session_start(); 

header('Content-Type: text/html; charset=utf-8');

include('../config/db.php');
include('funkcje.php');
include('maile.php');
include('logowanie.php');

if($ustawienia['pokaz_formularz_kontaktowy'] and isset($_POST['akcja']) and $_POST['akcja'] == 'formularz_kontaktowy' and isset($_POST['imie']) and $_POST['imie']!='' and isset($_POST['email']) and $_POST['email']!='' and isset($_POST['wiadomosc']) and $_POST['wiadomosc']!='' and isset($_POST['id_ogloszenia']) and $_POST['id_ogloszenia']>0){
	
	if(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'czarna_lista where email="'.filtruj($_POST['email']).'" limit 1'))){
		echo(false);
	}else{
		$id_ogloszenia = filtruj($_POST['id_ogloszenia']);
		$dane_ogloszenia = mysql_fetch_assoc(mysql_query('select id, tytul, prosty_tytul, email from '.$prefiks_tabel.'ogloszenia where id = "'.$id_ogloszenia.'" and aktywna=1 limit 1'));
		
		if($dane_ogloszenia['email']!=''){
			
			if(wyslij_mail('ogloszenie',$dane_ogloszenia['email'],array('id'=>$dane_ogloszenia['id'],'prosty_tytul'=>$dane_ogloszenia['prosty_tytul'],'tytul'=>$dane_ogloszenia['tytul'],'imie'=>$_POST['imie'],'email'=>$_POST['email'],'wiadomosc'=>$_POST['wiadomosc']))){
				echo(true);
			}else{
				echo(false);
			}
			
		}else{
			echo(false);
		}
	}
	
}elseif(isset($_POST['akcja']) and $_POST['akcja'] == 'zglos_naduzycie' and isset($_POST['email']) and $_POST['email']!='' and isset($_POST['wiadomosc']) and $_POST['wiadomosc']!='' and isset($_POST['id_ogloszenia']) and $_POST['id_ogloszenia']>0){
	
	if(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'czarna_lista where email="'.filtruj($_POST['email']).'" limit 1'))){
		echo(false);
	}else{
		$id_ogloszenia = filtruj($_POST['id_ogloszenia']);
		$dane_ogloszenia = mysql_fetch_assoc(mysql_query('select id, tytul, prosty_tytul from '.$prefiks_tabel.'ogloszenia where id = "'.$id_ogloszenia.'" limit 1'));
		
		if(!empty($dane_ogloszenia)){
			
			if(wyslij_mail('zglos_naduzycie',$ustawienia['email'],array('id'=>$dane_ogloszenia['id'],'prosty_tytul'=>$dane_ogloszenia['prosty_tytul'],'tytul'=>$dane_ogloszenia['tytul'],'email'=>$_POST['email'],'wiadomosc'=>$_POST['wiadomosc']))){
				echo(true);
			}else{
				echo(false);
			}
		}else{
			echo(false);
		}
	}
}elseif($ustawienia['powiadomienia_z_ogloszen'] and isset($_POST['akcja']) and $_POST['akcja'] == 'wyslij_powiadomienie' and isset($_POST['email']) and $_POST['email']!='' and isset($_POST['wiadomosc']) and $_POST['wiadomosc']!='' and isset($_POST['id_ogloszenia']) and $_POST['id_ogloszenia']>0){
	
	if(!isset($uzytkownik)){
		echo(false);
	}elseif(mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'czarna_lista where email="'.filtruj($_POST['email']).'" limit 1'))){
		echo(false);
	}else{
		$id_ogloszenia = filtruj($_POST['id_ogloszenia']);
		$dane_ogloszenia = mysql_fetch_assoc(mysql_query('select id, tytul, prosty_tytul from '.$prefiks_tabel.'ogloszenia where id = "'.$id_ogloszenia.'" limit 1'));
		
		if(!empty($dane_ogloszenia)){
			
			if(wyslij_mail('powiadomienia_z_ogloszen',$_POST['email'],array('id'=>$dane_ogloszenia['id'],'prosty_tytul'=>$dane_ogloszenia['prosty_tytul'],'tytul'=>$dane_ogloszenia['tytul'],'wiadomosc'=>$_POST['wiadomosc']))){
				echo(true);
			}else{
				echo(false);
			}

		}else{
			echo(false);
		}
	}
}
	

