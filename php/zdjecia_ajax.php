<?php

session_start(); 

include('../config/db.php');
include('funkcje.php');
include('logowanie.php');

if(!(isset($uzytkownik) or $ustawienia['ogloszenia_bez_rejestracji'])){
	die('brak dostepu');
}

if($ustawienia['zezwalaj_zdjecia'] and isset($_FILES["file"]["type"])){

	$sciezka = '../'.$ustawienia['upload'].'/';
	
	$path_parts = pathinfo($_FILES['file']['name']);
	$extension =  $path_parts['extension'];
	$nazwa = $stara_nazwa = prosta_nazwa(substr($path_parts['filename'],0,100)).'.'.$extension;
	$img = explode('.', $_FILES['file']['name']);
	
	$i = 0;
	while(file_exists($sciezka.$nazwa)) {  
		$nazwa = prosta_nazwa(substr($img[0],0,100)).'_'.$i.'.'.$extension;
		$i++;
	}
	
	if(!in_array($extension , array('jpg','jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'))){
		echo json_encode(array(false,'Nieprawidłowy typ pliku'));
	}elseif($_FILES["file"]["size"] > $ustawienia['rozmiar_zdjec']*1024){
		echo json_encode(array(false,'Za duży rozmiar pliku'));		
	}else{
		
		if($extension=="jpg" || $extension=="jpeg" || $extension=="JPG" || $extension=="JPEG" ){
			$src = imagecreatefromjpeg($_FILES['file']['tmp_name']);
		}else if($extension=="png" || $extension=="PNG" ){
			$src = imagecreatefrompng($_FILES['file']['tmp_name']);
		}else{
			$src = imagecreatefromgif($_FILES['file']['tmp_name']);
		}
		
		if($ustawienia['znak_wodny_wlacz'] and $ustawienia['znak_wodny_url']!=''){
			if(substr($ustawienia['znak_wodny_url'], 0, 1) == "/") {
				$ustawienia['znak_wodny_url'] = '..'.$ustawienia['znak_wodny_url'];
			}
			$stamp_extension = substr(strrchr($ustawienia['znak_wodny_url'], "."), 1); 
			if(in_array($stamp_extension , array('jpg','jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'))){
				if($stamp_extension=="jpg" || $stamp_extension=="jpeg" || $stamp_extension=="JPG" || $stamp_extension=="JPEG" ){
					$stamp = imagecreatefromjpeg($ustawienia['znak_wodny_url']);
				}else if($stamp_extension=="png" || $stamp_extension=="PNG" ){
					$stamp = imagecreatefrompng($ustawienia['znak_wodny_url']);
				}else{
					$stamp = imagecreatefromgif($ustawienia['znak_wodny_url']);
				}
				imagecopy($src,$stamp,imagesx($src)-imagesx($stamp) - 5, imagesy($src) - imagesy($stamp) - 5, 0, 0, imagesx($stamp), imagesy($stamp));
			}
			if($extension=="jpg" || $extension=="jpeg" || $extension=="JPG" || $extension=="JPEG" ){
				imagejpeg($src,$sciezka.$nazwa);
			}else if($extension=="png" || $extension=="PNG" ){
				imagepng($src,$sciezka.$nazwa);
			}else{
				imagegif($src,$sciezka.$nazwa);
			}
		}else{
			move_uploaded_file($_FILES['file']['tmp_name'], $sciezka.$nazwa);
		}

		list($width,$height)=getimagesize($sciezka.$nazwa);
		if($height>=200){
			$newheight=200;
		}else{
			$newheight=$height;
		}
		$newwidth=round($newheight/$height*$width);			
		$tmp=imagecreatetruecolor($newwidth,$newheight);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight, $width,$height);
		$thumb_temp = explode('.', $nazwa)[0];
		$miniaturka = $thumb_temp.'_thumb.jpg';
		$i = 0;
		while(file_exists($sciezka.$miniaturka)){  
			$miniaturka = $thumb_temp.'_'.$i.'_thumb.jpg';
			$i++;
		}
		imagejpeg($tmp,$sciezka.$miniaturka,100);
		imagedestroy($src);
		
		if(isset($uzytkownik)){$id_uzytkownika=$uzytkownik['id'];}else{$id_uzytkownika=0;}
		
		mysql_query('INSERT INTO `'.$prefiks_tabel.'zdjecia_temp`(`id_uzytkownika`, `miniaturka`, `url`, `data`) VALUES ("'.$id_uzytkownika.'", "'.$miniaturka.'","'.$nazwa.'", NOW())');
		
		echo json_encode(array(true,mysql_insert_id(),$nazwa,$ustawienia['upload'].'/'.$miniaturka));
	}
}else{
	echo json_encode(array(false,'Wybierz plik'));
}
