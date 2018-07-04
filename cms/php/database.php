<?php

session_start(); 

require_once('../../libs/Smarty.class.php');
$smarty = new Smarty();

include('../../config/db.php');
include('globalne.php');
include('logowanie.php');

if(isset($cms_login)){
	
	function EXPORT_TABLES($host,$user,$pass,$name,  $tables=false, $backup_name=false ){
		$mysqli = new mysqli($host,$user,$pass,$name); $mysqli->select_db($name); $mysqli->query("SET NAMES 'utf8'");
		$queryTables = $mysqli->query('SHOW TABLES'); while($row = $queryTables->fetch_row()) { $target_tables[] = $row[0]; }   if($tables !== false) { $target_tables = array_intersect( $target_tables, $tables); }
		foreach($target_tables as $table){
			$result = $mysqli->query('SELECT * FROM '.$table);  $fields_amount=$result->field_count;  $rows_num=$mysqli->affected_rows;     $res = $mysqli->query('SHOW CREATE TABLE '.$table); $TableMLine=$res->fetch_row();
			$content = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";
			for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) {
				while($row = $result->fetch_row())  { //when started (and every after 100 command cycle):
					if ($st_counter%100 == 0 || $st_counter == 0 )  {$content .= "\nINSERT INTO ".$table." VALUES";}
						$content .= "\n(";
						for($j=0; $j<$fields_amount; $j++)  { $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); if (isset($row[$j])){$content .= '"'.$row[$j].'"' ; }else {$content .= '""';}     if ($j<($fields_amount-1)){$content.= ',';}      }
						$content .=")";
					//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
					if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";} $st_counter=$st_counter+1;
				}
			} $content .="\n\n\n";
		}
		$backup_name = $backup_name ? $backup_name : $name."-".date('d-m-Y').".sql";
		header('Content-Type: application/octet-stream');   header("Content-Transfer-Encoding: Binary"); header("Content-disposition: attachment; filename=\"".$backup_name."\"");  echo $content; exit;
	}

	function IMPORT_TABLES($host,$user,$pass,$dbname,$sql_file){
		if (!file_exists($sql_file)) {die('Błąd! Wybierz właściwy plik sql! <button onclick="window.history.back();">Powrót</button>');} $allLines = file($sql_file);
		$mysqli = new mysqli($host, $user, $pass, $dbname); if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();} 
			$zzzzzz = $mysqli->query('SET foreign_key_checks = 0');	        preg_match_all("/\nCREATE TABLE(.*?)\`(.*?)\`/si", "\n".file_get_contents($sql_file), $target_tables); foreach ($target_tables[2] as $table){$mysqli->query('DROP TABLE IF EXISTS '.$table);}         $zzzzzz = $mysqli->query('SET foreign_key_checks = 1');
		$mysqli->query("SET NAMES 'utf8'");							$templine = '';	// Temporary variable, used to store current query
		foreach ($allLines as $line)	{											// Loop through each line
			if (substr($line, 0, 2) != '--' && $line != '') {$templine .= $line; 	// (if it is not a comment..) Add this line to the current segment
				if (substr(trim($line), -1, 1) == ';') {		// If it has a semicolon at the end, it's the end of the query
					$mysqli->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . $mysqli->error . '<br /><br />');  $templine = '';// Reset temp variable to empty
				}
			}
		}	echo 'Import zakończony sukcesem. <button onclick="window.history.back();">Powrót</button>';
	}

	if(isset($_GET['export'])){
		EXPORT_TABLES($mysql_server,$mysql_user,$mysql_pass,$mysql_db, false);
	}elseif(isset($_POST['import']) and is_uploaded_file($_FILES['plik']['tmp_name'])){
		IMPORT_TABLES($mysql_server,$mysql_user,$mysql_pass,$mysql_db,$_FILES['plik']['tmp_name']);
	}

}else{
	die('Brak dostepu!');
}
