<?php

header('Content-Type: text/html; charset=utf-8');

session_start();
ob_start(); 

error_reporting(E_ALL);
error_reporting(0);

include('config/db.php');

require_once('libs/Smarty.class.php');
$smarty = new Smarty();
$smarty->template_dir = 'views/'.$ustawienia['szablon'];
$smarty->compile_dir = 'tmp';
$smarty->cache_dir = 'cache';

include('php/funkcje.php');
include('php/maile.php');
include('php/logowanie.php');
include('php/globalne.php');
include('php/controller.php');

pobierz_panel_aktualnosci();
newsletter();
if(isset($infobox)){$smarty->assign("infobox", $infobox);}
if(isset($nawigacja)){$smarty->assign("nawigacja", $nawigacja);}

$smarty->assign("ustawienia", $ustawienia);
$smarty->assign('strona',$strona);

$smarty->display('main.tpl');
