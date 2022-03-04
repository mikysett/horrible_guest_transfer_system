<?php
require_once('config.php');

// formatta il testo per la corretta visualizzazione al momento della stampa su schermo
function str_show(&$str) {
	return stripslashes(htmlspecialchars_decode($str));
}

// Formatta testo per visualizzazione + non breaking spaces
function set_visual(&$str) {
	return str_replace(' ', '&nbsp;', str_show($str));
}

function str_save(&$str) {
	return strtoupper(str_save_sensitive($str));
}

function str_save_sensitive(&$str) {
	return htmlentities(addslashes($str), ENT_COMPAT, "UTF-8");
}

// Effettua tutte le operazioni al salvataggio sicuro e alla verifica dei dati su un transfer modificato/creato
function escapeToSave(&$trans) {
	
	$trans['nome'] 					 = str_save($trans['nome']);
	$trans['camera'] 				 = str_save($trans['camera']);
	$trans['num_tel'] 				 = str_save($trans['num_tel']);
	$trans['num_tel_sec'] 			 = str_save($trans['num_tel_sec']);
	$trans['email'] 					 = str_save($trans['email']);
	$trans['email_sec'] 			 = str_save($trans['email_sec']);
	
	$trans['luogo_arr'] 			 = str_save($trans['luogo_arr']);
	$trans['volo_arr']				 = str_save($trans['volo_arr']);
	$trans['operatore_arr']		 = str_save($trans['operatore_arr']);
	
	$trans['luogo_par'] 			 = str_save($trans['luogo_par']);
	$trans['volo_par']				 = str_save($trans['volo_par']);
	$trans['operatore_par']		 = str_save($trans['operatore_par']);
	
	
	if($trans['ora_hh_arr'] != '' && $trans['ora_mm_arr'] != '')
		$trans['ora_arr'] = controllo_data_ora($trans['data_arr'], $trans['ora_hh_arr'].':'.$trans['ora_mm_arr']);
	else 										 $trans['ora_arr'] = NULL;
	
	if($trans['ora_taxi_hh_arr'] != '' && $trans['ora_taxi_mm_arr'] != '')
		$trans['ora_taxi_arr'] = controllo_data_ora($trans['data_arr'], $trans['ora_taxi_hh_arr'].':'.$trans['ora_taxi_mm_arr']);
	else 										 $trans['ora_taxi_arr'] = NULL;
	
	$trans['ora_barca_arr']			 = controllo_data_ora($trans['data_arr'], $trans['ora_barca_hh_arr'].':'.$trans['ora_barca_mm_arr']);
	if($trans['ora_barca_hh_arr'] != '' && $trans['ora_barca_mm_arr'] != '')
		$trans['ora_barca_arr'] = controllo_data_ora($trans['data_arr'], $trans['ora_barca_hh_arr'].':'.$trans['ora_barca_mm_arr']);
	else 										 $trans['ora_barca_arr'] = NULL;
	// Calcoliamo l'orario di arrivo della barca
	if($trans['ora_barca_arr'] != NULL) {
		$trans['ora_barca_arr_cal'] = orario_arrivo($trans['porto_partenza_arr'], $trans['porto_arrivo_arr'], $trans['ora_barca_arr'], $trans['barca_arr']);
	}
	else $trans['ora_barca_arr_cal'] = NULL;
	
	

	if($trans['ora_hh_par'] != '' && $trans['ora_mm_par'] != '') $trans['ora_par'] = controllo_data_ora($trans['data_par'], $trans['ora_hh_par'].':'.$trans['ora_mm_par']);
	else 										 $trans['ora_par'] = NULL;

	if($trans['ora_taxi_hh_par'] != '' && $trans['ora_taxi_mm_par'] != '')	 $trans['ora_taxi_par'] = controllo_data_ora($trans['data_par'], $trans['ora_taxi_hh_par'].':'.$trans['ora_taxi_mm_par']);
	else 										 $trans['ora_taxi_par'] = NULL;
	
	$trans['porto_arrivo_par']		 = $trans['porto_arrivo_par'];
	if($trans['ora_barca_hh_par'] != '' && $trans['ora_barca_mm_par'] != '') $trans['ora_barca_par'] = controllo_data_ora($trans['data_par'], $trans['ora_barca_hh_par'].':'.$trans['ora_barca_mm_par']);
	else 										 $trans['ora_barca_par'] = NULL;
	
	// Calcoliamo l'orario di arrivo della barca
	if($trans['ora_barca_par'] != NULL) {
		$trans['ora_barca_par_cal'] = orario_arrivo($trans['porto_partenza_par'], $trans['porto_arrivo_par'], $trans['ora_barca_par'], $trans['barca_par']);
	}
	else $trans['ora_barca_par_cal'] = NULL;
	
	
	// Le due date vanno formattate per ultime perché necessarie nel formato GG/MM/YYYY per il settaggio delle altre date
	if($trans['data_arr'] != '') $trans['data_arr'] = controllo_data($trans['data_arr']);
	else 								 	$trans['data_arr'] = NULL;
	
	if($trans['data_par'] != '') $trans['data_par'] = controllo_data($trans['data_par']);
	else 								 	 $trans['data_par'] = NULL;
}

// Restituisce una tring per update il trans
function sqlTransUpdate(&$trans) {
	$sql_update =  'UPDATE transfer SET'
	.' tipo_transfer='.$trans['tipo_transfer'].','
	.' titolo='.$trans['titolo'].','
	.' nome=\''.$trans['nome'].'\',';
	
	if($trans['pax_ad'] != NULL)  $sql_update .= ' pax_ad='.$trans['pax_ad'].',';
	else 										$sql_update .= ' pax_ad=0,';
	if($trans['pax_bam'] != NULL) $sql_update .= ' pax_bam='.$trans['pax_bam'].',';
	else 										$sql_update .= ' pax_bam=0,';
 $sql_update .= ' camera=\''.$trans['camera'].'\','
 					//.' lingua='.$trans['lingua'].','
 					//.' auto_remind='.$trans['auto_remind'].','
 					//.' modificabile='.$trans['modificabile'].','
 					.' num_tel=\''.$trans['num_tel'].'\','
 					.' num_tel_sec=\''.$trans['num_tel_sec'].'\','
			.' email=\''.$trans['email'].'\','
			.' email_sec=\''.$trans['email_sec'].'\',';
	
	if($trans['data_arr'] != NULL) $sql_update .= ' data_arr='.$trans['data_arr'].',';
	else 										 $sql_update .= ' data_arr=NULL,';
	if($trans['ora_arr'] != NULL) $sql_update .= ' ora_arr='.$trans['ora_arr'].',';
	else 										$sql_update .= ' ora_arr=NULL,';
 $sql_update .= ' luogo_arr=\''.$trans['luogo_arr'].'\','
	.' volo_arr=\''.$trans['volo_arr'].'\','
	.' taxi_arr='.$trans['taxi_arr'].',';
	if($trans['ora_taxi_arr'] != NULL) $sql_update .= ' ora_taxi_arr='.$trans['ora_taxi_arr'].',';
	else 											  $sql_update .= ' ora_taxi_arr=NULL,';
 $sql_update .= ' porto_partenza_arr='.$trans['porto_partenza_arr'].','
	.' barca_arr='.$trans['barca_arr'].','
	.' porto_arrivo_arr='.$trans['porto_arrivo_arr'].',';
	if($trans['ora_barca_arr'] != NULL) $sql_update .= ' ora_barca_arr='.$trans['ora_barca_arr'].',';
	else 												$sql_update .= ' ora_barca_arr=NULL,';
	if($trans['ora_barca_arr_cal'] != NULL) $sql_update .= ' ora_barca_arr_cal='.$trans['ora_barca_arr_cal'].',';
	else 													 $sql_update .= ' ora_barca_arr_cal=NULL,';
 $sql_update .= ' stato_arr='.$trans['stato_arr'].','
	.' operatore_arr=\''.$trans['operatore_arr'].'\',';
	
	if($trans['ultima_mod_arr'] != NULL) $sql_update .= ' ultima_mod_arr='.$trans['ultima_mod_arr'].',';
	
	if($trans['data_par'] != NULL) $sql_update .= ' data_par='.$trans['data_par'].',';
	else 										 $sql_update .= ' data_par=NULL,';
	if($trans['ora_par'] != NULL) $sql_update .= ' ora_par='.$trans['ora_par'].',';
	else 										$sql_update .= ' ora_par=NULL,';
 $sql_update .= ' luogo_par=\''.$trans['luogo_par'].'\','
	.' volo_par=\''.$trans['volo_par'].'\','
	.' taxi_par='.$trans['taxi_par'].',';
	if($trans['ora_taxi_par'] != NULL) $sql_update .= ' ora_taxi_par='.$trans['ora_taxi_par'].',';
	else 											  $sql_update .= ' ora_taxi_par=NULL,';
 $sql_update .= ' porto_partenza_par='.$trans['porto_partenza_par'].','
	.' barca_par='.$trans['barca_par'].','
	.' porto_arrivo_par='.$trans['porto_arrivo_par'].',';
	if($trans['ora_barca_par'] != NULL) $sql_update .= ' ora_barca_par='.$trans['ora_barca_par'].',';
	else 												$sql_update .= ' ora_barca_par=NULL,';
	if($trans['ora_barca_par_cal'] != NULL) $sql_update .= ' ora_barca_par_cal='.$trans['ora_barca_par_cal'].',';
	else 													 $sql_update .= ' ora_barca_par_cal=NULL,';
 $sql_update .= ' stato_par='.$trans['stato_par'].','
	.' operatore_par=\''.$trans['operatore_par'].'\'';
	
	if($trans['ultima_mod_par'] != NULL) $sql_update .= ', ultima_mod_par='.$trans['ultima_mod_par'];
	
	return $sql_update;
}

// Calcola il tempo in funzione della tratta scelta
function orario_arrivo(&$partenza, &$arrivo, &$data_partenza, &$barca) {
	// Se non sono barche oppure se non sono porti conosciuti
	if(($partenza != 1 && $partenza != 2 && $partenza != 3) ||
		($arrivo != 1 && $arrivo != 2 && $arrivo != 3) ||
		($barca != 1 && $barca != 2 && $barca != 3))
		return $data_partenza;
	
	// Se si parte da cavallo per piantarella o da piantarella per cavallo ri aggiungono 15 minuti
	if(($partenza == 1 && $arrivo == 2) || ($partenza == 2 && $arrivo == 1)) {
		
		if($barca == 2 || $barca == 1) return strtotime('+1200 seconds', $data_partenza); // Fabirs 20 minuti
		elseif($barca == 3)				 return strtotime('+900 seconds', $data_partenza); // Rotation 15 minuti
	}
	
	// Se si parte da cavallo per st. teresa o da st. teresa per cavallo ri aggiungono 40 minuti
	if(($partenza == 1 && $arrivo == 3) || ($partenza == 3 && $arrivo == 1))
		return strtotime('+2700 seconds', $data_partenza);
	
	return $data_partenza;
}

function controllo_data(&$data) {
	$data_smistata = explode('/', str_replace(array('.', ',', ';', '-', '_'), '/', $data));
	$num_data_smistata = count($data_smistata);

	// Se non esiste nemmeno la prima entrata o non è un int ritorniamo errore
	if($num_data_smistata > 0 && is_numeric($data_smistata[0]) == true) {
		
		// Se il mese non è stato inserito o non è un int si inserisce il mese corrente
		if($num_data_smistata == 1 || is_numeric($data_smistata[1]) == false)
			$data_smistata[1] = $_SESSION['oggi_mese'];
		
		// Se l'anno non è stato inserito o non è un int si inserisce l'anno corrente
		if($num_data_smistata <= 2 || ($num_data_smistata == 3 && is_numeric($data_smistata[2]) == false)) {
			// Se il mese scelto è più piccolo del mese corrente si setta come anno di default l'anno prossimo
			if($data_smistata[1] < $_SESSION['oggi_mese']) $data_smistata[2] = $_SESSION['oggi_anno'] + 1;
			// Altrimenti l'anno di default è quello attuale
			else 												 		  $data_smistata[2] = $_SESSION['oggi_anno'];
		}

		$data_corretta = mktime(0, 0, 0, $data_smistata[1], $data_smistata[0], $data_smistata[2]);
	}
	else return NULL;
	
	return $data_corretta;
}

function controllo_data_ora(&$data, $ora) {
	$data_smistata = explode('/', str_replace(array('.', ',', ';', '-', '_'), '/', $data));
	$num_data_smistata = count($data_smistata);

	// Se non esiste nemmeno la prima entrata o non è un int ritorniamo errore
	if($num_data_smistata > 0 && is_numeric($data_smistata[0]) == true) {
		
		// Se il mese non è stato inserito o non è un int si inserisce il mese corrente
		if($num_data_smistata == 1 || is_numeric($data_smistata[1]) == false)
			$data_smistata[1] = $_SESSION['oggi_mese'];
		
		// Se l'anno non è stato inserito o non è un int si inserisce l'anno corrente
		if($num_data_smistata <= 2 || ($num_data_smistata == 3 && is_numeric($data_smistata[2]) == false)) {
			// Se il mese scelto è più piccolo del mese corrente si setta come anno di default l'anno prossimo
			if($data_smistata[1] < $_SESSION['oggi_mese']) $data_smistata[2] = $_SESSION['oggi_anno'] + 1;
			// Altrimenti l'anno di default è quello attuale
			else 												 		  $data_smistata[2] = $_SESSION['oggi_anno'];
		}

		$data_corretta = mktime(0, 0, 0, $data_smistata[1], $data_smistata[0], $data_smistata[2]);
	}
	else return NULL;
	
	$ora_smistata = explode(":", $ora);
	if($ora_smistata[0] == NULL) $ora_smistata[0] = 0;
	if($ora_smistata[1] == NULL) $ora_smistata[1] = 0;
	
	$data_corretta = mktime($ora_smistata[0], $ora_smistata[1], 0, $data_smistata[1], $data_smistata[0], $data_smistata[2]);
	
	return $data_corretta;
}

/* backup the db OR just a table */
function backup_tables($tables = '*')
{
	$return = "";
	$db = db_connect();
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = $db->query('SHOW TABLES');
		while($row = $result->fetch())
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = $db->query('SELECT * FROM '.$table);
		$num_fields = $result->columnCount();
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = $db->query('SHOW CREATE TABLE '.$table);
		$row2 = $row2->fetch();
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			for($x = 0 ; $row = $result->fetch() ; $x++)
			{
				if($x == 0) $return.= 'INSERT INTO '.$table.' VALUES';
				else 			$return.= ',';
				$return.= "\n(";
				for($j=0; $j < $num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = str_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j < ($num_fields-1)) { $return.= ','; }
				}
				$return.= ")";
			}
			if($x > 0) $return.= ';';
			
		}
		$return.="\n\n\n";
	}
	
	//save file
	$file_name = 'backup_transfer_'.date("d-m-y_H-i", time()).'.sql';
	$handle = fopen($file_name,'w+');
	fwrite($handle,$return);
	
	fclose($handle);
	
	
	header("Cache-Control: public");
   header("Content-Description: File Transfer");
   header("Content-Disposition: attachment; filename= " . $file_name);
   header("Content-Transfer-Encoding: binary");
   // Leggo il contenuto del file
   readfile($file_name);
   
   unlink($file_name);
}


function num_notti(&$data_arrivo, &$data_partenza) {
	$num_notti = 1;
	$buff = strtotime('+1 day', $data_arrivo);
	
	while($buff < $data_partenza) {
		$num_notti++;
	$buff = strtotime('+1 day', $buff);
	}
	return $num_notti;
}

function verifica_modifica(&$trans, $a_p) {
	// Recuperiamo il transfer da verificare
	$db = db_connect();
	$reponse = $db->query('SELECT * FROM transfer WHERE id='.$trans['id']);
	$old_trans = $reponse->fetchAll(PDO::FETCH_ASSOC);
	$db->connection = NULL;
	
	// Verifichiamo i dati comuni sia all'arrivo che alla partenza
	if($trans['nome'] 	!= $old_trans[0]['nome'] ||
		$trans['pax_ad'] 	!= $old_trans[0]['pax_ad'] ||
		$trans['pax_bam'] != $old_trans[0]['pax_bam'] ||
		$trans['camera'] 	!= $old_trans[0]['camera']) {
			
		$trans['ultima_mod_arr'] = time();
		$trans['ultima_mod_par'] = $trans['ultima_mod_arr'];
		return;
	}
	
	
	// Verifichiamo i dati specifici all'arrivo o alla partenza
	for($i = 0, $tip = 'arr' ; $i < 2 ; $i++) {
		
		if($trans['data_'.$tip]				 != $old_trans[0]['data_'.$tip] ||
			$trans['luogo_'.$tip]			 != $old_trans[0]['luogo_'.$tip] ||
			$trans['volo_'.$tip]				 != $old_trans[0]['volo_'.$tip] ||
			$trans['taxi_'.$tip] 			 != $old_trans[0]['taxi_'.$tip] ||
			$trans['ora_taxi_'.$tip] 		 != $old_trans[0]['ora_taxi_'.$tip] ||
			$trans['porto_partenza_'.$tip] != $old_trans[0]['porto_partenza_'.$tip] ||
			$trans['barca_'.$tip]			 != $old_trans[0]['barca_'.$tip] ||
			$trans['porto_arrivo_'.$tip]	 != $old_trans[0]['porto_arrivo_'.$tip] ||
			$trans['ora_barca_'.$tip]		 != $old_trans[0]['ora_barca_'.$tip] ||
			$trans['stato_'.$tip]			 != $old_trans[0]['stato_'.$tip] ||
			$trans['operatore_'.$tip]		 != $old_trans[0]['operatore_'.$tip]) {
			
			$trans['ultima_mod_'.$tip] = time();
		}
		// Modify the var to check for the departures
		$tip = 'par';
	}
}

function gestione_barche($lista_barche, $data) {
	$num_barche = count($lista_barche);
	$lista_finale = array();
	$giorno_dopo = strtotime('+1 day', $data);
	$num_conf = 0;
	
	for($i = 0 ; $i < $num_barche ; $i++) {
		// Se si tratta di un arrivo nella data prescelta
		if($lista_barche[$i]['ora_barca_arr'] >= $data && $lista_barche[$i]['ora_barca_arr'] <= $giorno_dopo) {
			if(($lista_barche[$i]['barca_arr'] == 1 && ($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 3 || $_SESSION['tipo_lista'] == 5)) ||
				($lista_barche[$i]['barca_arr'] == 2 && ($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 4 || $_SESSION['tipo_lista'] == 5))) {
				
				// Verifichiamo che il transfer non sia doppiato a causa di una partenza/arrivo lo stesso giorno sulla stessa barca
				for($j = 0, $doppione = 0 ; $j < $num_conf ; $j++) {
					if($lista_finale[$j]['id'] == $lista_barche[$i]['id'] && $lista_finale[$j]['ora_barca'] == $lista_barche[$i]['ora_barca_arr']) {
						$doppione = 1;
						break;
					}
				}
				
				if($doppione == 0) {
					$lista_finale[$num_conf]['id'] = $lista_barche[$i]['id'];
					$lista_finale[$num_conf]['tipo_commento'] = 1;
					$lista_finale[$num_conf]['tipo_transfer'] = $lista_barche[$i]['tipo_transfer'];
					$lista_finale[$num_conf]['stato'] = $lista_barche[$i]['stato_arr'];
					$lista_finale[$num_conf]['barca'] = $_SESSION['barche'][$lista_barche[$i]['barca_arr']];
					$lista_finale[$num_conf]['ora_barca'] = $lista_barche[$i]['ora_barca_arr'];
					$lista_finale[$num_conf]['nominativo'] = str_show($lista_barche[$i]['nome']);
					$lista_finale[$num_conf]['pax_ad'] = $lista_barche[$i]['pax_ad'];
					$lista_finale[$num_conf]['pax_bam'] = $lista_barche[$i]['pax_bam'];
					$lista_finale[$num_conf]['tragitto'] = $_SESSION['porti'][$lista_barche[$i]['porto_partenza_arr']].'/'.$_SESSION['porti'][$lista_barche[$i]['porto_arrivo_arr']];
					$lista_finale[$num_conf]['ultima_mod'] = $lista_barche[$i]['ultima_mod_arr'];
					$lista_finale[$num_conf]['link'] = 'tipo=arr&amp;barca='.$lista_barche[$i]['barca_arr'].'&amp;ora='.$lista_barche[$i]['ora_barca_arr'].'&amp;da='.$lista_barche[$i]['porto_partenza_arr'].'&amp;a='.$lista_barche[$i]['porto_arrivo_arr'];
					
					$num_conf++;
				}
			}
		}
		// Se si tratta di una partenza nella data prescelta
		if($lista_barche[$i]['ora_barca_par'] >= $data && $lista_barche[$i]['ora_barca_par'] <= $giorno_dopo) {
			if(($lista_barche[$i]['barca_par'] == 1 && ($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 3 || $_SESSION['tipo_lista'] == 5)) ||
				($lista_barche[$i]['barca_par'] == 2 && ($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 4 || $_SESSION['tipo_lista'] == 5))) {
				
				// Verifichiamo che il transfer non sia doppiato a causa di una partenza/arrivo lo stesso giorno sulla stessa barca
				for($j = 0, $doppione = 0 ; $j < $num_conf ; $j++) {
					if($lista_finale[$j]['id'] == $lista_barche[$i]['id'] && $lista_finale[$j]['ora_barca'] == $lista_barche[$i]['ora_barca_par']) {
						$doppione = 1;
						break;
					}
				}
				
				if($doppione == 0) {
					$lista_finale[$num_conf]['id'] = $lista_barche[$i]['id'];
					$lista_finale[$num_conf]['tipo_commento'] = 2;
					$lista_finale[$num_conf]['tipo_transfer'] = $lista_barche[$i]['tipo_transfer'];
					$lista_finale[$num_conf]['stato'] = $lista_barche[$i]['stato_par'];
					$lista_finale[$num_conf]['barca'] = $_SESSION['barche'][$lista_barche[$i]['barca_par']];
					$lista_finale[$num_conf]['ora_barca'] = $lista_barche[$i]['ora_barca_par'];
					$lista_finale[$num_conf]['nominativo'] = str_show($lista_barche[$i]['nome']);
					$lista_finale[$num_conf]['pax_ad'] = $lista_barche[$i]['pax_ad'];
					$lista_finale[$num_conf]['pax_bam'] = $lista_barche[$i]['pax_bam'];
					$lista_finale[$num_conf]['tragitto'] = $_SESSION['porti'][$lista_barche[$i]['porto_partenza_par']].'/'.$_SESSION['porti'][$lista_barche[$i]['porto_arrivo_par']];
					$lista_finale[$num_conf]['ultima_mod'] = $lista_barche[$i]['ultima_mod_par'];
					$lista_finale[$num_conf]['link'] = 'tipo=par&amp;barca='.$lista_barche[$i]['barca_par'].'&amp;ora='.$lista_barche[$i]['ora_barca_par'].'&amp;da='.$lista_barche[$i]['porto_partenza_par'].'&amp;a='.$lista_barche[$i]['porto_arrivo_par'];
					
					$num_conf++;
				}
			}
		}
	}

	// Riordiniamo le barche in ordine di data
	for($i = 0 ; $i < $num_conf ; $i++) {
		for($j = $i ; $j < $num_conf ; $j++) {
			if($lista_finale[$i]['ora_barca'] > $lista_finale[$j]['ora_barca']) {
				$buff = $lista_finale[$i];
				$lista_finale[$i] = $lista_finale[$j];
				$lista_finale[$j] = $buff;
			}
		}
	}
	
	
	// Riordiniamo le barche ordinando anche in funzione del tragitto
	for($i = 0 ; $i < $num_conf ; $i++) {
		
		for($j = $i ; $j < $num_conf ; $j++) {
			
			if($lista_finale[$i]['ora_barca'] == $lista_finale[$j]['ora_barca'] &&
				($lista_finale[$i]['barca'] != $lista_finale[$j]['barca'] ||
				$lista_finale[$i]['tragitto'] != $lista_finale[$j]['tragitto'])) {
				
				for($x = 0 ; $x < $num_conf ; $x++) {
					if($lista_finale[$x]['ora_barca'] == $lista_finale[$i]['ora_barca']
					&& $lista_finale[$x]['barca'] == $lista_finale[$i]['barca']
					&& $lista_finale[$x]['tragitto'] == $lista_finale[$i]['tragitto']) {
						
						$buff = $lista_finale[$j];
						$lista_finale[$j] = $lista_finale[$x];
						$lista_finale[$x] = $buff;
						
						break;
					}
				}
			
			}
		}
	}
	
	return $lista_finale;
}

// -1 = tutti i mezzi ;
function stato_mezzi($mezzo) {
	$link = 'lista_trans.php';
	if($mezzo != -1) {
		$where = 'WHERE mezzo='.$mezzo;
		if($mezzo == 1 || $mezzo == 2) $link = 'mezzi.php';
	}
	else 				  $where = '';
	
	$stati = array('NON DISPONIBILE', 'FERMA A CAVALLO', 'FERMA A ST. TE.', 'FERMA A PIANTA.', 'FERMA',
						'PARTITA DA CAVALLO', 'PARTITA DA ST. TE.', 'PARTITA DA PIANTA.', 'PARTITA', 'FUORI USO');
	$id_mezzi = array('fabirtre_time', 'fabirquattro_time', 'elicompany_time');
	$num_stati = count($stati);
	
	$time_now = time();

	$sql = 'SELECT mezzo,stato,timestamp_stato FROM stato_mezzi '.$where.' ORDER BY mezzo';

	$db = db_connect();
	try {
	 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$rep_stato_mezzi = $db->query($sql);
	}
	catch(Exception $e) {
		echo 'Stato mezzi Exception -> ';
	 	var_dump($e->getMessage());
	}
	$db->connection = NULL;

	$stato_mezzi = $rep_stato_mezzi->fetchAll(PDO::FETCH_ASSOC);
	$num_stato_mezzi = count($stato_mezzi);
	
	for($i = 0 ; $i < $num_stato_mezzi ; $i++) {
		echo '<div class="mezzo_box">';
			echo '<p class="titolo_mezzo">'.$_SESSION['barche_nostyle'][$stato_mezzi[$i]['mezzo']].'</p>';
				echo '<div class="stati_mezzo">';
					for($j = 0 ; $j < $num_stati ; $j++) {
						echo '<a class="tipo_stato stato_'.$j.'" href="'.$link.'?mezzo='.$stato_mezzi[$i]['mezzo'].'&amp;stato='.$j.'">'.$stati[$j].'</a>';
					}
				echo '</div>';
				echo '<p class="stato_attuale stato_'.$stato_mezzi[$i]['stato'].'">';
				echo $stati[$stato_mezzi[$i]['stato']];
				// Se rilevante stampiamo anche l'orario
				if($stato_mezzi[$i]['stato'] == 5 || $stato_mezzi[$i]['stato'] == 6 || $stato_mezzi[$i]['stato'] == 7 || $stato_mezzi[$i]['stato'] == 8) {
					echo '<span class="ora_stato" id="'.$id_mezzi[$i].'">'.floor(($time_now-$stato_mezzi[$i]['timestamp_stato'])/60).' min</span>';
				}
				echo '</p>';
		echo '</div>';
	}
	
	for($i = 0, $primo_script = TRUE ; $i < $num_stato_mezzi ; $i++) {
		if($stato_mezzi[$i]['timestamp_stato'] != 0) {
			if($primo_script == TRUE) {
				echo '<script type="text/javascript">';
				$primo_script = FALSE;
			}
			
			echo "function ".$id_mezzi[$i]."(){ \r\n" .
				  "var time_now = new Date();\r\n" .
				  "var minuti_trascorsi = Math.floor(((time_now.getTime()/1000) - ".$stato_mezzi[$i]["timestamp_stato"].")/60);\r\n" .
				  "document.getElementById(\"".$id_mezzi[$i]."\").innerHTML=minuti_trascorsi+\" min\"\r\n}\r\n";
		}
	}
	
	for($i = 0, $primo_script = TRUE ; $i < $num_stato_mezzi ; $i++) {
		if($stato_mezzi[$i]['timestamp_stato'] != 0) {
			if($primo_script == TRUE) {
				echo "window.onload=function(){\r\n";
				$primo_script = FALSE;
			}
			
			echo "setInterval(\"".$id_mezzi[$i]."()\", 5000)\r\n";
		}
	}
	if($primo_script == FALSE) echo "} </script>";
}

?>