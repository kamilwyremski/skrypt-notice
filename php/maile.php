<?php

if(!isset($ustawienia['base_url'])){
	die('Brak dostepu!');
}

if($ustawienia['smtp']){
	include_once(realpath(dirname(__FILE__)).'/../config/smtp.php');
}

function wyslij_mail($rodzaj,$email,$dodatkowe=''){
	global $ustawienia, $uzytkownik, $prefiks_tabel, $mail;
	
	if($rodzaj!=''){
		
		$header = 'Reply-To: <'.$ustawienia['email']."> \r\n"; 
		
		if($rodzaj=='rejestracja'){
			
			$message = $ustawienia['rejestracja_tresc'];
			$subject = $ustawienia['rejestracja_tytul'];
			$message = str_replace("{link_aktywacyjny}",$ustawienia['base_url'].'?akcja=aktywacja&kod='.$dodatkowe['kod_aktywacyjny'],$message);
			$subject = str_replace("{link_aktywacyjny}",$ustawienia['base_url'].'?akcja=aktywacja&kod='.$dodatkowe['kod_aktywacyjny'],$subject);
			$message = str_replace("{haslo}",$dodatkowe['haslo'],$message);
			$subject = str_replace("{haslo}",$dodatkowe['haslo'],$subject);
			$ip = get_client_ip();
			
		}elseif($rodzaj=='rejestracja_fb'){
			
			$message = $ustawienia['rejestracja_fb_tresc'];
			$subject = $ustawienia['rejestracja_fb_tytul'];
			$message = str_replace("{haslo}",$dodatkowe['haslo'],$message);
			$subject = str_replace("{haslo}",$dodatkowe['haslo'],$subject);
			$rodzaj = "rejestracja facebook";
			$ip = get_client_ip();
			
		}elseif($rodzaj=='reset_hasla'){
			
			$message = $ustawienia['reset_hasla_tresc'];
			$subject = $ustawienia['reset_hasla_tytul'];
			$message = str_replace("{link_reset}",$ustawienia['base_url'].'/reset_hasla?nowe_haslo='.$dodatkowe['kod'],$message);
			$subject = str_replace("{link_reset}",$ustawienia['base_url'].'/reset_hasla?nowe_haslo='.$dodatkowe['kod'],$subject);
			$ip = get_client_ip();
			
		}elseif($rodzaj=='start'){
			
			$dane_ogloszenia = mysql_fetch_assoc(mysql_query('select id, tytul, prosty_tytul, id_uzytkownika from '.$prefiks_tabel.'ogloszenia where id="'.$dodatkowe['id'].'" limit 1'));
			if($dane_ogloszenia['id_uzytkownika']==0){return false;}
			
			$dane_uzytkownika = mysql_fetch_assoc(mysql_query('select login, email from '.$prefiks_tabel.'uzytkownicy where id="'.$dane_ogloszenia['id_uzytkownika'].'" limit 1'));
			
			$email = $dane_uzytkownika['email'];
			$message = $ustawienia['email_start_tresc'];
			$subject = $ustawienia['email_start_tytul'];
			$message = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'</a>',$message);
			$subject = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'</a>',$subject);
			$message = str_replace("{tytul_ogloszenie}",$dane_ogloszenia['tytul'],$message);
			$subject = str_replace("{tytul_ogloszenie}",$dane_ogloszenia['tytul'],$subject);
			$message = str_replace("{login}",$dane_uzytkownika['login'],$message);
			$subject = str_replace("{login}",$dane_uzytkownika['login'],$subject);
			$rodzaj = "start ogłoszenia - ".$dane_ogloszenia['id'];
			$ip = 'serwer';
			
		}elseif($rodzaj=='koniec'){
			
			$dane_ogloszenia = mysql_fetch_assoc(mysql_query('select id, tytul, prosty_tytul, id_uzytkownika from '.$prefiks_tabel.'ogloszenia where id="'.$dodatkowe['id'].'" limit 1'));
			if($dane_ogloszenia['id_uzytkownika']==0){return false;}
			
			$dane_uzytkownika = mysql_fetch_assoc(mysql_query('select login, email from '.$prefiks_tabel.'uzytkownicy where id="'.$dane_ogloszenia['id_uzytkownika'].'" limit 1'));
			
			$email = $dane_uzytkownika['email'];
			$message = $ustawienia['email_koniec_tresc'];
			$subject = $ustawienia['email_koniec_tytul'];
			$message = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'</a>',$message);
			$subject = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dane_ogloszenia['id'].','.$dane_ogloszenia['prosty_tytul'].'</a>',$subject);
			$message = str_replace("{tytul_ogloszenie}",$dane_ogloszenia['tytul'],$message);
			$subject = str_replace("{tytul_ogloszenie}",$dane_ogloszenia['tytul'],$subject);
			$message = str_replace("{login}",$dane_uzytkownika['login'],$message);
			$subject = str_replace("{login}",$dane_uzytkownika['login'],$subject);
			$rodzaj = "koniec ogłoszenia - ".$dane_ogloszenia['id'];
			$ip = 'serwer';
			
		}elseif($rodzaj=='formularz_kontaktowy'){
			
			$header = 'Reply-To: <'.$dodatkowe['email']."> \r\n"; 
			if($ustawienia['smtp']){$mail->AddReplyTo($dodatkowe['email']);}
			$message = $ustawienia['mail_kontakt_tresc'];
			$subject = $ustawienia['mail_kontakt_tytul'];
			$message = str_replace("{imie}",$dodatkowe['imie'],$message);
			$subject = str_replace("{imie}",$dodatkowe['imie'],$subject);
			$message = str_replace("{temat}",$dodatkowe['temat'],$message);
			$subject = str_replace("{temat}",$dodatkowe['temat'],$subject);
			$message = str_replace("{wiadomosc}",$dodatkowe['wiadomosc'],$message);
			$subject = str_replace("{wiadomosc}",$dodatkowe['wiadomosc'],$subject);
			$ip = get_client_ip();
			
		}elseif($rodzaj=='ogloszenie'){
			
			$header = 'Reply-To: <'.$dodatkowe['email']."> \r\n"; 
			if($ustawienia['smtp']){$mail->AddReplyTo($dodatkowe['email']);}
			$message = $ustawienia['mail_ogloszenie_tresc'];
			$subject = $ustawienia['mail_ogloszenie_tytul'];
			$message = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'</a>',$message);
			$subject = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'</a>',$subject);
			$message = str_replace("{tytul_ogloszenie}",$dodatkowe['tytul'],$message);
			$subject = str_replace("{tytul_ogloszenie}",$dodatkowe['tytul'],$subject);
			$message = str_replace("{imie}",$dodatkowe['imie'],$message);
			$subject = str_replace("{imie}",$dodatkowe['imie'],$subject);
			$message = str_replace("{wiadomosc}",$dodatkowe['wiadomosc'],$message);
			$subject = str_replace("{wiadomosc}",$dodatkowe['wiadomosc'],$subject);
			
			$rodzaj = "ogłoszenie ".$dodatkowe['id'];
			$ip = get_client_ip();
			
		}elseif($rodzaj=='zglos_naduzycie'){
			
			$message = $ustawienia['mail_naduzycie_tresc'];
			$subject = $ustawienia['mail_naduzycie_tytul'];
			$message = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'</a>',$message);
			$subject = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'</a>',$subject);
			$message = str_replace("{tytul_ogloszenie}",$dodatkowe['tytul'],$message);
			$subject = str_replace("{tytul_ogloszenie}",$dodatkowe['tytul'],$subject);
			$message = str_replace("{wiadomosc}",$dodatkowe['wiadomosc'],$message);
			$subject = str_replace("{wiadomosc}",$dodatkowe['wiadomosc'],$subject);

			$rodzaj = "nadużycie - ogłoszenie ".$dodatkowe['id'];
			$ip = get_client_ip();
			
		}elseif($rodzaj=='powiadomienia_z_ogloszen'){
			
			$message = $ustawienia['mail_powiadomienie_tresc'];
			$subject = $ustawienia['mail_powiadomienie_tytul'];
			$message = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'</a>',$message);
			$subject = str_replace("{link_ogloszenie}",'<a href="'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'">'.$ustawienia['base_url'].'/'.$dodatkowe['id'].','.$dodatkowe['prosty_tytul'].'</a>',$subject);
			$message = str_replace("{tytul_ogloszenie}",$dodatkowe['tytul'],$message);
			$subject = str_replace("{tytul_ogloszenie}",$dodatkowe['tytul'],$subject);
			$message = str_replace("{wiadomosc}",$dodatkowe['wiadomosc'],$message);
			$subject = str_replace("{wiadomosc}",$dodatkowe['wiadomosc'],$subject);
			
			$header = 'Reply-To: <'.$uzytkownik['email']."> \r\n"; 
			if($ustawienia['smtp']){$mail->AddReplyTo($uzytkownik['email']);}
			$rodzaj = "powiadomienie - ogłoszenie ".$dodatkowe['id'];
			$ip = get_client_ip();
			
		}elseif($rodzaj=='newsletter_potwierdzanie'){
			
			$message = $ustawienia['mail_newsletter_potw_tresc'];
			$subject = $ustawienia['mail_newsletter_potw_tytul'];
			
			$message = str_replace("{link_newsletter}",'<a href="'.$ustawienia['base_url'].'?akcja=potwierdz_newsletter&kod='.$dodatkowe['kod'].'" title="Potwierdź swój adres email">'.$ustawienia['base_url'].'?akcja=potwierdz_newsletter&kod='.$dodatkowe['kod'].'</a>',$message);
			$subject = str_replace("{link_newsletter}",'<a href="'.$ustawienia['base_url'].'?akcja=potwierdz_newsletter&kod='.$dodatkowe['kod'].'" title="Potwierdź swój adres email">'.$ustawienia['base_url'].'?akcja=potwierdz_newsletter&kod='.$dodatkowe['kod'].'</a>',$subject);
			
			$ip = get_client_ip();
			
		}elseif($rodzaj=='mailing'){
			
			$message = $dodatkowe['message'];
			$subject = $dodatkowe['subject'];
			$ip = get_client_ip();
			
		}elseif($rodzaj=='pp_wyplata_admin'){
			
			$message = $ustawienia['mail_pp_wyplata_admin_tresc'];
			$subject = $ustawienia['mail_pp_wyplata_admin_tytul'];
			$message = str_replace("{pp_imie}",$dodatkowe['pp_imie'],$message);
			$subject = str_replace("{pp_imie}",$dodatkowe['pp_imie'],$subject);
			$message = str_replace("{pp_adres}",$dodatkowe['pp_adres'],$message);
			$subject = str_replace("{pp_adres}",$dodatkowe['pp_adres'],$subject);
			$message = str_replace("{pp_numer_konta}",$dodatkowe['pp_numer_konta'],$message);
			$subject = str_replace("{pp_numer_konta}",$dodatkowe['pp_numer_konta'],$subject);
			$message = str_replace("{kwota}",$dodatkowe['kwota'].' '.$ustawienia['waluta'],$message);
			$subject = str_replace("{kwota}",$dodatkowe['kwota'].' '.$ustawienia['waluta'],$subject);
			
			$header = 'Reply-To: <'.$dodatkowe['email']."> \r\n"; 
			if($ustawienia['smtp']){$mail->AddReplyTo($dodatkowe['email']);}
			$rodzaj = "pp - zlecenie wypłaty - admin";
			$ip = get_client_ip();
			
		}elseif($rodzaj=='pp_wyplata_user'){
			
			$message = $ustawienia['mail_pp_wyplata_user_tresc'];
			$subject = $ustawienia['mail_pp_wyplata_user_tytul'];
			$message = str_replace("{pp_imie}",$dodatkowe['pp_imie'],$message);
			$subject = str_replace("{pp_imie}",$dodatkowe['pp_imie'],$subject);
			$message = str_replace("{pp_adres}",$dodatkowe['pp_adres'],$message);
			$subject = str_replace("{pp_adres}",$dodatkowe['pp_adres'],$subject);
			$message = str_replace("{pp_numer_konta}",$dodatkowe['pp_numer_konta'],$message);
			$subject = str_replace("{pp_numer_konta}",$dodatkowe['pp_numer_konta'],$subject);
			$message = str_replace("{kwota}",$dodatkowe['kwota'].' '.$ustawienia['waluta'],$message);
			$subject = str_replace("{kwota}",$dodatkowe['kwota'].' '.$ustawienia['waluta'],$subject);
			
			$rodzaj = "pp - zlecenie wypłaty";
			$ip = get_client_ip();
			
		}
		
		$message = str_replace("{tytul}",$ustawienia['tytul'],$message);
		$subject = str_replace("{tytul}",$ustawienia['tytul'],$subject);
		$message = str_replace("{base_url}",$ustawienia['base_url'],$message);
		$subject = str_replace("{base_url}",$ustawienia['base_url'],$subject);
		$message = str_replace("{link_logo}",'<img src="'.$ustawienia['base_url'].$ustawienia['logo'].'" style="max-width:300px; max-height: 200px">',$message);
		$subject = str_replace("{link_logo}",'<img src="'.$ustawienia['base_url'].$ustawienia['logo'].'" style="max-width:300px; max-height: 200px">',$subject);
		if(isset($dodatkowe['login'])){
			$message = str_replace("{login}",$dodatkowe['login'],$message);
			$subject = str_replace("{login}",$dodatkowe['login'],$subject);
		}
		if(isset($dodatkowe['email'])){
			$message = str_replace("{email}",$dodatkowe['email'],$message);
			$subject = str_replace("{email}",$dodatkowe['email'],$subject);
		}
		$message = str_replace("{data}",date("Y-m-d"),$message);
		$subject = str_replace("{data}",date("Y-m-d"),$subject);
		
		$header .= 'From: '.$ustawienia['email'].' <'.$ustawienia['email'].">\r\n"; 
		$header .= "MIME-Version: 1.0 \r\n"; 

		if($ustawienia['maile_zalaczniki'] and isset($_FILES['zalacznik']) and $_FILES['zalacznik']['name']!=''){

			$file_tmp_name    = $_FILES['zalacznik']['tmp_name'];
			$file_name        = $_FILES['zalacznik']['name'];
			$file_size        = $_FILES['zalacznik']['size'];
			$file_type        = $_FILES['zalacznik']['type'];
			$file_error       = $_FILES['zalacznik']['error'];
			
			if($file_error>0 or $file_size>5000000){
				die('zly zalacznik');
			}
			
			$handle = fopen($file_tmp_name, "r");
			$content = fread($handle, $file_size);
			fclose($handle);
			$encoded_content = chunk_split(base64_encode($content));

			$boundary = md5("sanwebe"); 

			$header .= "Content-Type: multipart/mixed; boundary = $boundary\r\n\r\n"; 
			
			$body = "--$boundary\r\n";
			$body .= "Content-Type: text/html; charset=utf-8\r\n";
			$body .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
			$body .= chunk_split(base64_encode($message)); 
			
			//attachment
			$body .= "--$boundary\r\n";
			$body .="Content-Type: $file_type; name=\"$file_name\"\r\n";
			$body .="Content-Disposition: attachment; filename=\"$file_name\"\r\n";
			$body .="Content-Transfer-Encoding: base64\r\n";
			$body .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n"; 
			$body .= $encoded_content; 

		}else{
			$header .= "Content-Type: text/html; charset=UTF-8"; 
			$body = $message;
		}
		
		$subject = '=?utf-8?B?'.base64_encode($subject).'?=';
		
		if($email!=''){
			
			if($ustawienia['smtp']){
				$mail->Subject = $subject;
				$mail->Body = $message;
				if(isset($boundary)){
					$mail->AddAttachment($_FILES['zalacznik']['tmp_name'],$_FILES['zalacznik']['name']);
				}
				$mail->ClearAllRecipients();
				$mail->AddAddress($email);
				if($mail->Send()){
					mysql_query('INSERT INTO `'.$prefiks_tabel.'logi_email`(`adresat`, `akcja`, `ip`, `data`) VALUES ("'.$email.'", "'.$rodzaj.'", "'.$ip.'", NOW())');
					return true;
				}
			}elseif(mail($email, $subject, $body, $header)){
				mysql_query('INSERT INTO `'.$prefiks_tabel.'logi_email`(`adresat`, `akcja`, `ip`, `data`) VALUES ("'.$email.'", "'.$rodzaj.'", "'.$ip.'", NOW())');
				return true;
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}
}

