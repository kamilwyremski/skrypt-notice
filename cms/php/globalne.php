<?php

function pozycja($tabela, $warunek=""){
	$wynik = mysql_fetch_array(mysql_query('select pozycja from '.$tabela.' '.$warunek.' order by pozycja desc limit 1'));
	if($wynik!=''){
		return $wynik['pozycja']+1;
	}else{
		return 1;
	}
}

function ustaw_pozycje($tabela, $id, $pozycja, $dzialanie, $dodatkowy_warunek="true"){
	if($dzialanie==0){$warunek = '<'; $sortowanie = 'desc';}else{$warunek = '>'; $sortowanie = 'asc';}
	$wynik = mysql_fetch_array(mysql_query('select id, pozycja from '.$tabela.' where pozycja '.$warunek.' '.$pozycja.' and '.$dodatkowy_warunek.' order by pozycja '.$sortowanie.' limit 1'));
	if($wynik!=''){
		mysql_query('update '.$tabela.' set pozycja="'.$wynik['pozycja'].'" where id="'.$id.'" limit 1');
		mysql_query('update '.$tabela.' set pozycja="'.$pozycja.'" where id="'.$wynik['id'].'" limit 1');
	}
}

function policz_strony($tabela, $limit='10', $warunek='true'){
	global $smarty;
	if (isset($_GET['strona']) and is_numeric($_GET['strona']) and $_GET['strona']>0)  { 
		$limit_start = ($_GET['strona']-1)*$limit;
		$smarty->assign("ktora_strona", $_GET['strona']);
	}else{
		$limit_start = 0;
		$smarty->assign("ktora_strona", 1);
	}
	$smarty->assign("ile_stron", ceil(mysql_num_rows(mysql_query('select 1 from '.$tabela.' where '.$warunek.''))/$limit));

	$gets = $gets_sortuj = $gets_strona = $_GET;
	$url_strony['cale'] = http_build_query($gets);
	unset($gets_sortuj['sortuj'],$gets_sortuj['desc']);
	$url_strony['sortuj'] = http_build_query($gets_sortuj);
	unset($gets_strona['strona']);
	$url_strony['strona'] = http_build_query($gets_strona);

	$smarty->assign("url_strony", $url_strony);
	$smarty->assign("iteration", $limit_start);
	return $limit_start;
}

function sortuj($domyslne='data'){
	if(isset($_GET['sortuj'])){
		$sortuj = $_GET['sortuj'];
		if(isset($_GET['desc'])){$sortuj .= ' desc';}
	}else{
		$sortuj = $domyslne;
	}
	return $sortuj;
}

function filtruj($zmienna){
    if(get_magic_quotes_gpc()){
        $zmienna = stripslashes($zmienna);
	}
    return mysql_real_escape_string(htmlspecialchars(trim($zmienna))); 
}

function prosta_nazwa($argument){
	$wynik = strtolower(str_replace(array(' ','%','$',':','–',',','/','=','?','Ę','Ó','Ą','Ś','Ł','Ż','Ź','Ć','Ń','ę','ó','ą','ś','ł','ż','ź','ć','ń'), array('-','-','','','','','','','','E','O','A','S','L','Z','Z','C','N','e','o','a','s','l','z','z','c','n'), $argument));
	return $wynik;
}

function adres_www($adres){
	if(substr($adres, 0, 7) != "http://" and substr($adres, 0, 8) != "https://" and $adres !='') {
		$adres = 'http://'.$adres;
	}
	if(substr($adres, -1)=='/'){
		$adres = substr($adres,0,-1);
	}
	return $adres;
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

