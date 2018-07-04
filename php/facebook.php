<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

require_once( 'facebook/HttpClients/FacebookHttpable.php' );
require_once( 'facebook/HttpClients/FacebookCurl.php' );
require_once( 'facebook/HttpClients/FacebookCurlHttpClient.php' );
require_once( 'facebook/HttpClients/FacebookStreamHttpClient.php' );
require_once( 'facebook/HttpClients/FacebookStream.php' );
require_once( 'facebook/FacebookSession.php' );
require_once( 'facebook/FacebookRedirectLoginHelper.php' );
require_once( 'facebook/FacebookRequest.php' );
require_once( 'facebook/FacebookResponse.php' );
require_once( 'facebook/FacebookSDKException.php' );
require_once( 'facebook/FacebookRequestException.php' );
require_once( 'facebook/FacebookServerException.php');
require_once( 'facebook/FacebookOtherException.php' );
require_once( 'facebook/FacebookAuthorizationException.php' );
require_once( 'facebook/GraphObject.php' );
require_once( 'facebook/GraphLocation.php' );
require_once( 'facebook/GraphUser.php' );
require_once( 'facebook/GraphSessionInfo.php' );
require_once( 'facebook/Entities/AccessToken.php');


// Called class with namespace
use Facebook\FacebookHttpable;
use Facebook\FacebookCurl;
use Facebook\FacebookCurlHttpClient;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookServerException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphLocation;
use Facebook\GraphUser;
use Facebook\GraphSessionInfo;

FacebookSession::setDefaultApplication($ustawienia['facebook_api'],$ustawienia['facebook_secret']);

$helper = new FacebookRedirectLoginHelper($ustawienia['base_url'].'/logowanie'); 

try {
  $session = $helper->getSessionFromRedirect();
}catch( FacebookRequestException $ex ) {
  // Exception
}catch( Exception $ex ) {
  // When validation fails or other local issues
}
 
if(isset($session)){
	$request = new FacebookRequest( $session, 'GET', '/me?fields=email' );
	$response = $request->execute();
	$object = $response->getGraphObject();
	$email = $object->getProperty('email');
	if($email!=''){
		$dane_uzytkownika = mysql_fetch_array(mysql_query('select id, login, aktywny, kod_aktywacyjny from '.$prefiks_tabel.'uzytkownicy where email="'.$email.'" limit 1'));
		if($dane_uzytkownika!=''){
			if($dane_uzytkownika['login']!=''){
				$kod_sesji = md5(uniqid(rand(), true));
				mysql_query('INSERT INTO `'.$prefiks_tabel.'sesje_logowania`(`id_uzytkownika`, `kod`, `ip`, `data`) VALUES ("'.$dane_uzytkownika['id'].'", "'.$kod_sesji.'", "'.get_client_ip().'", "'.date("Y-m-d H:i:s").'")');
				if($dane_uzytkownika['aktywny']==0){
					mysql_query('update `'.$prefiks_tabel.'uzytkownicy` set aktywny="1", data_aktywacji="'.date("Y-m-d H:i:s").'", ip_aktywacji="'.get_client_ip().'" where id="'.$dane_uzytkownika['id'].'" limit 1');
				}
				$_SESSION['uzytkownik']['id'] = $dane_uzytkownika['id'];
				$_SESSION['uzytkownik']['kod_sesji'] = $kod_sesji;
				header("Location: index.php?zalogowano");
			}else{
				header("Location: ".$ustawienia['base_url']."/rejestracja?facebook_login&kod=".$dane_uzytkownika['kod_aktywacyjny']);
			}
		}elseif(!mysql_num_rows(mysql_query('select 1 from '.$prefiks_tabel.'czarna_lista where email="'.$email.'" limit 1'))){
			$kod_aktywacyjny = md5(uniqid(rand(), true));
			mysql_query('INSERT INTO `'.$prefiks_tabel.'uzytkownicy`(`email`, `aktywny`, `kod_aktywacyjny`, `rejestracja_facebook`, `pp_polecajacy`, `pp_procent`, `data`) VALUES ("'.$email.'", "0", "'.$kod_aktywacyjny.'", "1", "'.pp_sprawdz_czy_polecono().'", "-1", "'.date("Y-m-d H:i:s").'")');
			header("Location: ".$ustawienia['base_url']."/rejestracja?facebook_login&kod=".$kod_aktywacyjny);
		}else{
			$smarty->assign("url_facebook", $helper->getLoginUrl(array('scope' => 'email')));
		}
	}else{
		$smarty->assign("url_facebook", $helper->getLoginUrl(array('scope' => 'email')));
	}
}else{
	$smarty->assign("url_facebook", $helper->getLoginUrl(array('scope' => 'email')));
}

