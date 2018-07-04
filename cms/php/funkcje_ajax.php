<?php

session_start(); 

require_once('../../libs/Smarty.class.php');
$smarty = new Smarty();

include('../../config/db.php');
include('globalne.php');
include('logowanie.php');

if(isset($cms_login) and isset($_POST['data'])){
	$post = $_POST['data'];
	if($post['akcja']=='aktywuj_uzytkownika' and isset($post['id'])){
		mysql_query('update '.$prefiks_tabel.'uzytkownicy set aktywny=1, data_aktywacji="'.date("Y-m-d H:i:s").'" where id="'.filtruj($post['id']).'" limit 1');
	}elseif($post['akcja']=='aktywuj_admina' and isset($post['id'])){
		mysql_query('update '.$prefiks_tabel.'uzytkownicy set admin=1 where id="'.filtruj($post['id']).'" limit 1');
	}elseif($post['akcja']=='dezaktywuj_admina' and isset($post['id'])){
		mysql_query('update '.$prefiks_tabel.'uzytkownicy set admin=0 where id="'.filtruj($post['id']).'" limit 1');
	}elseif($post['akcja']=='usun_czarna_lista' and isset($post['email'])){
		mysql_query('DELETE FROM `'.$prefiks_tabel.'czarna_lista` WHERE email="'.filtruj($post['email']).'" limit 1');
	}elseif($post['akcja']=='promuj_ogloszenie' and isset($post['id'])){		
		mysql_query('update '.$prefiks_tabel.'ogloszenia set promowana=1 where id="'.filtruj($post['id']).'" limit 1');	
	}elseif($post['akcja']=='nie_promuj_ogloszenie' and isset($post['id'])){		
		mysql_query('update '.$prefiks_tabel.'ogloszenia set promowana=0 where id="'.filtruj($post['id']).'" limit 1');	
	}elseif($post['akcja']=='slider_ogloszenie' and isset($post['id'])){		
		mysql_query('update '.$prefiks_tabel.'ogloszenia set slider=1 where id="'.filtruj($post['id']).'" limit 1');	
	}elseif($post['akcja']=='nie_slider_ogloszenie' and isset($post['id'])){		
		mysql_query('update '.$prefiks_tabel.'ogloszenia set slider=0 where id="'.filtruj($post['id']).'" limit 1');
	}elseif($post['akcja']=='opcje_dodatkowe_pozycja' and isset($post['id']) and isset($post['pozycja']) and isset($post['dzialanie'])){
		ustaw_pozycje($prefiks_tabel.'opcje_dodatkowe',filtruj($post['id']),filtruj($post['pozycja']),filtruj($post['dzialanie']),'id!="0"');
	}elseif($post['akcja']=='kategoria_pozycja' and isset($post['id']) and isset($post['pozycja']) and isset($post['dzialanie']) and isset($post['kategoria'])){
		ustaw_pozycje($prefiks_tabel.'kategorie',filtruj($post['id']),filtruj($post['pozycja']),filtruj($post['dzialanie']),'kategoria="'.filtruj($post['kategoria']).'"');
	}elseif($post['akcja']=='kategorie_uloz' and isset($post['kategoria']) and $post['kategoria']>=0){
		$pozycja = 1;
		$q = mysql_query('select id from '.$prefiks_tabel.'kategorie where kategoria="'.filtruj($post['kategoria']).'" order by nazwa');
		while($dane = mysql_fetch_assoc($q)){
			mysql_query('update '.$prefiks_tabel.'kategorie set pozycja="'.$pozycja.'" where id="'.$dane['id'].'" limit 1');
			$pozycja++;
		}
	}
}else{
	die('Brak dostepu!');
}
