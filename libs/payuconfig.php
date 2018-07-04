<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

/**
 * OpenPayU Examples
 *
 * @copyright  Copyright (c) 2011-2015 PayU
 * @license    http://opensource.org/licenses/LGPL-3.0  Open Software License (LGPL 3.0)
 * http://www.payu.com
 * http://developers.payu.com
 */

OpenPayU_Configuration::setEnvironment('secure');
OpenPayU_Configuration::setMerchantPosId($ustawienia['payu_id']); // POS ID (Checkout)
OpenPayU_Configuration::setSignatureKey($ustawienia['payu_md5']); //Second MD5 key. You will find it in admin panel.


