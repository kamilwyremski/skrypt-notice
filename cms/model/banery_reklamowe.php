<?php

if(isset($cms_login)){

	if(isset($_POST['akcja']) and $_POST['akcja']=='zapisz_banery_reklamowe' and isset($_POST['baner_top_1']) and isset($_POST['baner_top_2']) and isset($_POST['baner_panel_1']) and isset($_POST['baner_panel_2']) and isset($_POST['baner_panel_3']) and isset($_POST['baner_panel_4']) and isset($_POST['baner_bottom_1']) and isset($_POST['baner_bottom_2'])){
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.htmlspecialchars($_POST['baner_top_1']).'" where nazwa="baner_top_1" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.htmlspecialchars($_POST['baner_top_2']).'" where nazwa="baner_top_2" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.htmlspecialchars($_POST['baner_panel_1']).'" where nazwa="baner_panel_1" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.htmlspecialchars($_POST['baner_panel_2']).'" where nazwa="baner_panel_2" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.htmlspecialchars($_POST['baner_panel_3']).'" where nazwa="baner_panel_3" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.htmlspecialchars($_POST['baner_panel_4']).'" where nazwa="baner_panel_4" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.htmlspecialchars($_POST['baner_bottom_1']).'" where nazwa="baner_bottom_1" limit 1');
		mysql_query('update '.$prefiks_tabel.'ustawienia set wartosc="'.htmlspecialchars($_POST['baner_bottom_2']).'" where nazwa="baner_bottom_2" limit 1');
		pobierz_ustawienia();
	}
	
}else{
	die('Brak dostepu!');
}
