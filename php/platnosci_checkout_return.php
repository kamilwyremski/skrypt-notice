<?php

error_reporting(0);

include('../config/db.php');
include('globalne.php');
include('maile.php');

require '../libs/CheckoutFinland/Response.php';

use CheckoutFinland\Response;

$response = new Response($ustawienia['checkout_secret']);

$response->setRequestParams($_GET);

$status_string = 'Błąd płatności';

$wynik = false;

try {
    if($response->validate()) {
        // we have a valid response, now check the status
		
		$dane_ogloszenia = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'ogloszenia where id="'.$response->getReference().'" limit 1'));
		
		list($oplacona,$oplata,$do_oplacenia,$zostalo_do_zaplacenia) = policz_koszt_ogloszenia($dane_ogloszenia['id']);

        // the status codes are listed in the api documentation of Checkout Finland
        switch($response->getStatus())
        {
            case '2':
            case '5':
            case '6':
            case '8':
            case '9':
            case '10':
                // These are paid and we can ship the product
                $status_string = 'PAID';
				$wynik = true;
                break;
            case '7':
            case '3':
            case '4':
                // Payment delayed or it is not known yet if the payment was completed 
                 $status_string = 'DELAYED';
                break;
            case '-1':
                 $status_string = 'CANCELLED BY USER';
                 break;
            case '-2':
            case '-3':
            case '-4':
            case '-10':
                // Cancelled by banks, Checkout Finland, time out e.g. 
                 $status_string = 'CANCELLED';
                break;
        }
    } else {
        // something went wrong with the validation, perhaps the user changed the return parameters
    }
} catch(MacMismatchException $ex) {
    die('Mac mismatch');
} catch(UnsupportedAlgorithmException $ex) {
    die('Unsupported algorithm');
}

if($dane_ogloszenia){
	if($wynik){
		ogloszenie_zostalo_oplacone($dane_ogloszenia['id'],$zostalo_do_zaplacenia);
		header('Location: '.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'?podglad&status=ok');
	}else{
		header('Location: '.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'?podglad&status=error');
	}
}else{
	die($status_string);
}

?>

