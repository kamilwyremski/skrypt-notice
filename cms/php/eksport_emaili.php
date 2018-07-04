<?php

session_start(); 

require_once('../../libs/Smarty.class.php');
$smarty = new Smarty();

include('../../config/db.php');
include('globalne.php');
include('logowanie.php');

if(isset($cms_login) and isset($_GET['grupa'])){
	
	$filename = "Adresy email.txt";
	header("Content-disposition: attachment; filename=\"$filename\"");
	header("Content-type: text/plain");

	if($_GET['grupa']=='wszystkie'){
		$q = mysql_query('select email from (select email from '.$prefiks_tabel.'uzytkownicy where aktywny=1 union select email from '.$prefiks_tabel.'newsletter) as a group by email');
	}elseif($_GET['grupa']=='newsletter'){
		$q = mysql_query('select email from '.$prefiks_tabel.'newsletter');
	}elseif($_GET['grupa']=='uzytkownicy'){
		$q = mysql_query('select email from '.$prefiks_tabel.'uzytkownicy where aktywny=1');
	}else{
		die('Brak dostêpu');
	}
	while($dane = mysql_fetch_array($q)){
		echo($dane['email'].','."\r\n");
	}
}else{
	die('Brak dostepu!');
}
