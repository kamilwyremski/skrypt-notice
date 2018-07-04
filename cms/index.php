<?php

session_start(); 

error_reporting(E_ALL);
error_reporting(0);

require_once('../libs/Smarty.class.php');
$smarty = new Smarty();
$smarty->template_dir = 'views';
$smarty->compile_dir = 'tmp';
$smarty->cache_dir = 'cache';

include('../config/db.php');
include('php/globalne.php');
include('../php/globalne.php');
include('../php/maile.php');
include('php/logowanie.php');
include('php/controller.php');

