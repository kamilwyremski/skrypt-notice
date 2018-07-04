<?php

if(isset($cms_login)){
	if(isset($_GET['rodzaj'])){
		if($_GET['rodzaj']=='payu'){
			
			$ile_na_strone = 100;
			$q = mysql_query('select * from '.$prefiks_tabel.'platnosci_payu  where status = "1" order by '.sortuj('id desc').' limit '.policz_strony($prefiks_tabel.'platnosci_payu',$ile_na_strone,'status = 1').','.$ile_na_strone.'');
			while($dane = mysql_fetch_assoc($q)){
				$wynik = mysql_fetch_assoc(mysql_query('select tytul, prosty_tytul from '.$prefiks_tabel.'ogloszenia where id="'.$dane['id_ogloszenia'].'" limit 1'));
				if(is_array($wynik)){
					$dane['tytul'] = $wynik['tytul'];
					$dane['prosty_tytul'] = $wynik['prosty_tytul'];
				}
				$platnosci_payu[] = $dane;
			}
			if(isset($platnosci_payu)){$smarty->assign("platnosci_payu", $platnosci_payu);}
	
		}elseif($_GET['rodzaj']=='dotpay'){
			
			$ile_na_strone = 100;
			$q = mysql_query('select * from '.$prefiks_tabel.'platnosci_dotpay order by '.sortuj('id desc').' limit '.policz_strony($prefiks_tabel.'platnosci_dotpay',$ile_na_strone).','.$ile_na_strone.'');
			while($dane = mysql_fetch_assoc($q)){
				$wynik = mysql_fetch_assoc(mysql_query('select tytul, prosty_tytul from '.$prefiks_tabel.'ogloszenia where id="'.$dane['id_ogloszenia'].'" limit 1'));
				$dane['tytul'] = $wynik['tytul'];
				$dane['prosty_tytul'] = $wynik['prosty_tytul'];
				$platnosci_dotpay[] = $dane;
			}
			if(isset($platnosci_dotpay)){$smarty->assign("platnosci_dotpay", $platnosci_dotpay);}
	
		}elseif($_GET['rodzaj']=='tpay'){
			
			$ile_na_strone = 100;
			$q = mysql_query('select * from '.$prefiks_tabel.'platnosci_tpay order by '.sortuj('id desc').' limit '.policz_strony($prefiks_tabel.'platnosci_tpay',$ile_na_strone).','.$ile_na_strone.'');
			while($dane = mysql_fetch_assoc($q)){
				$wynik = mysql_fetch_assoc(mysql_query('select tytul, prosty_tytul from '.$prefiks_tabel.'ogloszenia where id="'.$dane['id_ogloszenia'].'" limit 1'));
				$dane['tytul'] = $wynik['tytul'];
				$dane['prosty_tytul'] = $wynik['prosty_tytul'];
				$platnosci_tpay[] = $dane;
			}
			if(isset($platnosci_tpay)){$smarty->assign("platnosci_tpay", $platnosci_tpay);}
			
		}elseif($_GET['rodzaj']=='przelewy24'){
			
			$ile_na_strone = 100;
			$q = mysql_query('select * from '.$prefiks_tabel.'platnosci_przelewy24 order by '.sortuj('id desc').' limit '.policz_strony($prefiks_tabel.'platnosci_przelewy24',$ile_na_strone).','.$ile_na_strone.'');
			while($dane = mysql_fetch_assoc($q)){
				$wynik = mysql_fetch_assoc(mysql_query('select tytul, prosty_tytul from '.$prefiks_tabel.'ogloszenia where id="'.$dane['id_ogloszenia'].'" limit 1'));
				$dane['tytul'] = $wynik['tytul'];
				$dane['prosty_tytul'] = $wynik['prosty_tytul'];
				$platnosci_przelewy24[] = $dane;
			}
			if(isset($platnosci_przelewy24)){$smarty->assign("platnosci_przelewy24", $platnosci_przelewy24);}
			
		}elseif($_GET['rodzaj']=='paypal'){
			
			$ile_na_strone = 100;
			$q = mysql_query('select * from '.$prefiks_tabel.'platnosci_paypal order by '.sortuj('id desc').' limit '.policz_strony($prefiks_tabel.'platnosci_paypal',$ile_na_strone).','.$ile_na_strone.'');
			while($dane = mysql_fetch_assoc($q)){
				$wynik = mysql_fetch_assoc(mysql_query('select tytul, prosty_tytul from '.$prefiks_tabel.'ogloszenia where id="'.$dane['id_ogloszenia'].'" limit 1'));
				$dane['tytul'] = $wynik['tytul'];
				$dane['prosty_tytul'] = $wynik['prosty_tytul'];
				$platnosci_paypal[] = $dane;
			}
			if(isset($platnosci_paypal)){$smarty->assign("platnosci_paypal", $platnosci_paypal);}
		
		}
	}
	
	
}else{
	die('Brak dostepu!');
}
