<?php

if(!isset($_POST['kwota']) or !($_POST['kwota']>0) or !isset($_POST['id_ogloszenia']) or !($_POST['id_ogloszenia']>0) or !isset($_POST['opis']) or $_POST['opis']==''){
	die('Brak dostepu');
}

error_reporting(0);

include('../config/db.php');
include('globalne.php');

require '../libs/CheckoutFinland/Payment.php';
require '../libs/CheckoutFinland/Client.php';
require '../libs/CheckoutFinland/Response.php';
require '../libs/CheckoutFinland/Exceptions/AmountUnderMinimumException.php';

use CheckoutFinland\Payment;
use CheckoutFinland\Client;

$demo_merchant_id       = $ustawienia['checkout_id'];
$demo_merchant_secret   = $ustawienia['checkout_secret'];
$return_url             = 'http://' .$_SERVER['SERVER_NAME'] .str_replace('platnosci_checkout.php', 'platnosci_checkout_return.php', $_SERVER['REQUEST_URI']);

$payment = new  Payment($demo_merchant_id, $demo_merchant_secret);
$payment->setUrls($return_url);

$payment_data = [
    'stamp'         => time(),                      // stamp is the unique id for this transaction
    'amount'        => ($_POST['kwota'] * 100),    // amount is in cents
    'reference'     => $_POST['id_ogloszenia'],     // some reference id (perhaps order id)
    'message'       => $_POST['opis'],            // some short description about the order
	'deliveryDate'  => new \DateTime('2016-12-24'),
    'country'       => 'FIN',                       // country affects what payment options are shown FIN = all, others = credit cards
    'language'      => 'EN'
];

$payment->setData($payment_data);

$client = new Client();

$response = $client->sendPayment($payment);

if($response){
    $xml = @simplexml_load_string($response); // use @ to suppress warnings, checkout finland responds with an error string instead of xml if something went wrong

    if($xml and isset($xml->id)){
        // now we have a proper response xml and can show payment options to customer

        // here you can pass the xml to your view for rendering or something else
        // we just render the payment options a bit further down this file
             
    }else{ 
        die('Błąd płatności');
    }
}else{
    die('Serwer padł!');
} 
?>
<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-min.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout Finland API</title>
    <style>
        .C1 {
             width: 180px;
             height: 120px;
             border: 1pt solid #a0a0a0;
             display: block;
             float: left;
             margin: 7px;
             -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;
             clear: none;
             padding: 0;
            }
        .C1:hover {
             background-color: #f0f0f0;
             border-color: black;
            }
        .C1 form {
             width: 180px;height: 120px;
            }
        .C1 form span {
             display:table-cell; vertical-align:middle;
             height: 92px;
             width: 180px;
            }
        .C1 form span input {
             margin-left: auto;
             margin-right: auto;
             display: block;
             border: 1pt solid #f2f2f2;
             -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;
             padding: 5px;
             background-color: white;
            }
        .C1:hover form span input {
             border: 1pt solid black;
            }
        .C1 div {
             text-align: center;
             font-family: arial;
             font-size: 8pt;
            }
    </style>
</head>
<body>
    <div style="clear:both; display:block; max-width:800px; margin:auto; padding-bottom: 50px;">
        <h2>Payment options:</h2>
        <?php 
        if($xml and isset($xml->id))
        {
            $html = '<div class="block" style="padding: 10px; background-color: white;">';

            foreach($xml->payments->payment->banks as $bankX) 
            {
                foreach($bankX as $bank) 
                {
                    $html .= "<div class='C1' style='float: left; margin-right: 20px; min-height: 100px;' text-align: center;><form action='{$bank['url']}' method='post'><p>\n";
                    foreach($bank as $key => $value) 
                    {
                        $html .= "<input type='hidden' name='$key' value='$value' />\n";
                    }
                    $html .= "<span><input type='image' src='{$bank['icon']}' /></span><div><p>{$bank['name']}</p></div></form></div>\n";
                }
            }
        }
        echo "<div>$html<div style='clear:both;'></div></div>";
        ?>
    </div>
</body>
</html>