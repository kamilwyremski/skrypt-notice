<?php

error_reporting(0);

include('../config/db.php');
include('globalne.php');
include('funkcje.php');
include('maile.php');
include('logowanie.php');

require_once realpath(dirname(__FILE__)) . '/../libs/openpayu.php';
require_once realpath(dirname(__FILE__)) . '/../libs/payuconfig.php';

if(isset($_GET['akcja']) and $_GET['akcja']=='zlec_zaplate' and isset($_GET['id_ogloszenia']) and $_GET['id_ogloszenia']>0){
	
	$id_ogloszenia = filtruj($_GET['id_ogloszenia']);
	$ogloszenie = mysql_fetch_assoc(mysql_query('select * from '.$prefiks_tabel.'ogloszenia where id="'.$id_ogloszenia.'" limit 1'));

	if($ogloszenie){
		list($oplacona,$oplata,$do_oplacenia,$zostalo_do_zaplacenia) = policz_koszt_ogloszenia($id_ogloszenia);
		
		if(isset($uzytkownik)){
			$payu_email = $uzytkownik['email'];
		}else{
			$payu_email = $ogloszenie['email'];
		}
		
		$payu_id_transakcji = md5(randomPassword());
		
		mysql_query('INSERT INTO `'.$prefiks_tabel.'platnosci_payu`(`id_transakcji`, `payu_id`, `payu_md5`, `email`, `id_ogloszenia`, `opis`, `kwota`,  `data`) VALUES ("'.$payu_id_transakcji.'", "'.$ustawienia['payu_id'].'","'.$ustawienia['payu_md5'].'", "'.$payu_email.'", "'.$ogloszenie['id'].'", "Aktywacja ogloszenia: '.$ogloszenie['id'].'", "'.$zostalo_do_zaplacenia.'", NOW())');
		
		require_once realpath(dirname(__FILE__)) . '/../libs/openpayu.php';
		require_once realpath(dirname(__FILE__)) . '/../libs/payuconfig.php';

		$order = array();
		$order['notifyUrl'] = $ustawienia['base_url'].'/php/platnosci_payu.php';
		$order['continueUrl'] = $ustawienia['base_url'].'/'.$ogloszenie['id'].','.$ogloszenie['prosty_tytul'].'?podglad&status=payu';
		$order['customerIp'] = get_client_ip();
		$order['merchantPosId'] = OpenPayU_Configuration::getMerchantPosId();
		$order['description'] = 'Ogloszenie '.$ogloszenie['id'];
		$order['currencyCode'] = 'PLN';
		$order['totalAmount'] = $zostalo_do_zaplacenia*100;
		$order['extOrderId'] = $payu_id_transakcji;
		$order['products'][0]['name'] = 'Ogloszenie '.$ogloszenie['tytul'];
		$order['products'][0]['unitPrice'] = $zostalo_do_zaplacenia*100;
		$order['products'][0]['quantity'] = 1;
		$order['buyer']['email'] = $payu_email;
		$rsp = OpenPayU_Order::hostedOrderForm($order);
		
		echo('<style>form{display:none}</style>');
		echo($rsp);
		echo('<script type="text/javascript">document.getElementById("payu-payment-form").submit();</script>');
		echo('<style>form{display:block}</style>');
	}
	
}elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $body = file_get_contents('php://input');
    $data = trim($body);

    try {
        if (!empty($data)) {
            $result = OpenPayU_Order::consumeNotification($data);
        }

        if ($result->getResponse()->order->orderId) {

            $order = OpenPayU_Order::retrieve($result->getResponse()->order->orderId);
            if($order->getStatus() == 'SUCCESS'){

				$id_transakcji = $result->getResponse()->order->extOrderId;
				
				$dane_transakcji = mysql_fetch_assoc(mysql_query('SELECT * FROM `'.$prefiks_tabel.'platnosci_payu` WHERE id_transakcji = "'.$id_transakcji.'" limit 1'));
				
				if($dane_transakcji!='' and $dane_transakcji['status']==0){
					
					mysql_query('update `'.$prefiks_tabel.'platnosci_payu`set status="1", data_koniec = NOW() WHERE id_transakcji = "'.$id_transakcji.'" limit 1');
					
					ogloszenie_zostalo_oplacone($dane_transakcji['id_ogloszenia'],(float)$dane_transakcji['kwota']);

				}
				
                header("HTTP/1.1 200 OK");
		
            }
        }
    } catch (OpenPayU_Exception $e) {
        echo $e->getMessage();
    }
}


