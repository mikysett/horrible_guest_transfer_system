<?php
session_start(); // Si lancia la sezione

// reference the file containing the Chat class
require_once("trans.class.php");
// retrieve the operation to be performed
$mode = $_POST['mode'];
$now = time();
$op = NULL;
/*
OPERAZIONI
NULL :
1 : Stato mezzo cambiato
2 : Numeri camere aggiornati
3 : Nota giornaliera aggiunta
4 : Nota giornaliera cancellata
5 : Transfer aggiunto
6 : Transfer dublicato
7 : Transfer modificato
8 : Transfer eliminato
9 : Commento di un transfer cancellato
10 : Recuperiamo i dati di un transfer specifico se le date di modifica sono più recenti di quello esaminato



*/

// Se ci sono parametri extra facciamo operazioni extra
if(isset($_POST['extra'])) {
	$type = $_POST['extra']['type'];
	$data = $_POST['extra']['data'];
}

// Se l'operazione è di eliminare un commento
if($mode == "RemoveComm") {
	// build the JSON response
	$response = array();
	$response['op'] = 9;
	
	// Prepariamo la query per aggiornare la data di ultima modifica del transfer
	
	// Se abbiamo eliminato un commento generico
	if($data['tipo'] == 0) $transUpdate = 'UPDATE transfer SET ultima_mod_arr='.$now.',ultima_mod_par='.$now.' WHERE id='.$data['idTrans'];
	// Se abbiamo eliminato un commento per gli arrivi
	if($data['tipo'] == 1) $transUpdate = 'UPDATE transfer SET ultima_mod_arr='.$now.' WHERE id='.$data['idTrans'];
	// Se abbiamo eliminato un commento per le partenze
	if($data['tipo'] == 2) $transUpdate = 'UPDATE transfer SET ultima_mod_par='.$now.' WHERE id='.$data['idTrans'];
	
	$db = db_connect();
	try {
	 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$db->query('DELETE FROM commenti WHERE id='.$data['id']); // Eliminiamo il commento
		$db->query($transUpdate); // Aggiorniamo la data di ultima modifica del transfer
	}
	catch(Exception $e) {
		echo 'RemoveComm Exception -> ';
	 	var_dump($e->getMessage());
	}
	$db->connection = NULL;
}

// Recuperiamo i dati di un transfer e li inviamo al cliente se le date di modifica sono superiori
elseif($mode == "CheckTrans") {
	// build the JSON response
	$response = array();
	$id = $data['id'];
	$ultima_mod_arr = $data['ultima_mod_arr'];
	$ultima_mod_par = $data['ultima_mod_par'];
	
	// compose the SQL query that retrieves new trans
	$select = 'SELECT transfer.*, commenti.id AS com_id, commenti.id_transfer, commenti.tutti AS com_tutti,'//
					 .' commenti.reception AS com_reception, commenti.governante AS com_governante, commenti.ristorante AS com_ristorante, commenti.facchini AS com_facchini, commenti.barca AS com_barca,'//
					 .' commenti.taxi AS com_taxi, commenti.cliente AS com_cliente, commenti.tipo_commento AS com_tipo_commento, commenti.testo AS com_testo, commenti.operatore AS com_operatore, '//
					 .' commenti.data_creazione AS com_data_creazione'
					 .' FROM transfer LEFT JOIN commenti ON transfer.id=commenti.id_transfer';
	
	$query = $select . ' WHERE transfer.id='.$id.' AND (ultima_mod_arr>'.$ultima_mod_arr.' OR ultima_mod_par>'.$ultima_mod_par.')';
	
	$db = db_connect();
	try {
	 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$result = $db->query($query); // Aggiorniamo la data di ultima modifica del transfer
	}
	catch(Exception $e) {
		echo 'CheckTrans Exception -> ';
	 	var_dump($e->getMessage());
	}
	$db->connection = NULL;
	
	$result = $result->fetchAll(PDO::FETCH_ASSOC);
	$num_row = count($result);
	
	// Se c'è una versione più recente del transfer transferiamo l'informazione
	// Senza cambiare la data di lastupdate perché riguarda la parte del trans non inerente alla lista in questione (o almeno nn per forza)
	if($num_row > 0) {
		$trans_sel = &$result[0];
		
		if($trans_sel['com_id'] != NULL) {
			// Organizziamo i commenti come sotto array del transfer
			setCommenti($result, 0);
		}
		$response['op'] = 10;
		$response['transUpdated'] = $trans_sel;
		$response['info'][0] = $data['lista'];
		$response['info'][1] = $trans_sel['id'];
	}
}

// if the operation is Retrieve
elseif($mode == 'RetrieveNew') {

	// create a new Transfer instance
	$trans = new Trans();

	// retrieve the action parameters used to add a new trans
	$date_list = intval($_POST['date_list'])/1000;
	$last_update = intval($_POST['last_update']);
	
	// Se ci sono parametri extra facciamo operazioni extra
	if(isset($type)) {
		$type = $_POST['extra']['type'];
		$data = $_POST['extra']['data'];
		
		// Se stiamo cambiando lo stato di un mezzo
		if($type == 'changeMezzo') {
			$op = 1;
			$data = explode("_", $data);
			$mezzo = intval($data[0]); // Mezzo
			$stato = intval($data[1]); // Stato mezzo
			
			// Se lo stato richiede di conoscere l'orario
			if($stato >= 5 && $stato <= 8) $ts_stato = $now;
			else 									   $ts_stato = 0;
			
			// Aggiorniamo il database
			$sql = 'UPDATE stato_mezzi SET stato='.$stato.',timestamp_stato='.$ts_stato.',ts_mod='.$now.' WHERE mezzo='.$mezzo;
			
			$db = db_connect();
			try {
			 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$rep_stato_mezzi = $db->query($sql);
			}
			catch(Exception $e) {
				echo 'RetrieveNew Exception -> ';
			 	var_dump($e->getMessage());
			}
			$db->connection = NULL;
		}
		
		// Se stiamo aggiornando in blocco i numeri di camera
		if($type == 'updateCam') {
			$op = 2;
			$agg = FALSE;
			$num_cam = count($data);
			$sql_agg_cam = 'INSERT INTO transfer (id, camera, ultima_mod_arr, ultima_mod_par) VALUES ';
			
			// Prendiamo l'id e la camera dei transfer del giorno per vedere se il numero di camera è stato cambiato
			$sql_query = 'SELECT id,camera FROM transfer WHERE data_arr='.$date_list.' OR data_par='.$date_list.' ORDER BY ora_barca_arr_cal, nome';
			
			// Formattiamo i dati ricevuti e li mettiamo in sicurezza
			for($i = 0 ; $i < $num_cam ; $i++) {
				$data[$i]['id'] = intval($data[$i]['id']);
				$data[$i]['cam'] = str_save($data[$i]['cam']);
			}
			
			$db = db_connect();
			$rep = $db->query($sql_query);
				
			$all_trans = $rep->fetchAll(PDO::FETCH_ASSOC);
			$num_trans = count($all_trans);
			
			for($i = 0 ; $i < $num_trans ; $i++) {
				
				for($j = 0 ; $j < $num_cam ; $j++) {
					// Se l'id corrisponde e la camera è diversa procediamo con il salvataggio
					if($all_trans[$i]['id'] == $data[$j]['id'] && $all_trans[$i]['camera'] != $data[$j]['cam']) {
						if($agg == TRUE) $sql_agg_cam .= ','; else $agg = TRUE;
							
						$sql_agg_cam .= '('.$data[$j]['id'].',\''.$data[$j]['cam'].'\','.$now.','.$now.')';
					}
				}
			}
			
			// Se ci sono aggiornamenti aggiorniamo il database
			if($agg == TRUE) {
				$sql_agg_cam .= ' ON DUPLICATE KEY UPDATE camera=VALUES(camera),ultima_mod_arr=VALUES(ultima_mod_arr),ultima_mod_par=VALUES(ultima_mod_par);';
				try {
				 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
					$db->query($sql_agg_cam);
				}
				catch(Exception $e) {
					echo 'UpdateCam Exception -> ';
				 	var_dump($e->getMessage());
				}
			}
			
			$db->connection = NULL;
		}
		
		// Se stiamo aggiungendo una nota giornaliera
		if($type == "addNotaGG") {
			$op = 3;
			$reparti = "";
			$val_reparti = "";
			$num_reparti = count($data["rep"]);
			
			// Inseriamo i reparti da podificare
			for($i = 0 ; $i < $num_reparti ; $i++) {
				$reparti .= ",".str_save($data["rep"][$i]);
				$val_reparti .= ",1";
			}
			
			// Aggiorniamo i reparti
			$db = db_connect();
			$db->query('INSERT INTO note_giornaliere (data,testo,data_creazione'.$reparti.') VALUES ('.$date_list.',"'.str_save($data["text"]).'",'.$now.$val_reparti.')');
			$db->connection = NULL;
		}
		
		// Se stiamo cancellando una nota giornaliera
		if($type == "clxnota") {
			$op = 4;
			$db = db_connect();
			try {
			 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->query('UPDATE note_giornaliere SET testo="",data_creazione='.$now.' WHERE id='.$data);
			}
			catch(Exception $e) {
				echo 'clxnota Exception -> ';
			 	var_dump($e->getMessage());
			}
			$db->connection = NULL;
		}
		
		// Se stiamo modificando un trans
		if($type == "modificaTrans") {
			$op = 7;
			$transSel = &$data;
			
			// Transformiamo l'array in un dizionario
			$numParam = count($transSel);
			for($i = 0 ; $i < $numParam ; $i++) {
				$transSel[$transSel[$i]["name"]] = $transSel[$i]["value"];
				unset($transSel[$i]);
			}
			// Dati non transferiti tramite AJAX
			$transSel["ultima_mod_arr"] = 0;
			$transSel["ultima_mod_par"] = 0;
			
			// Escape and secure retreive data
			escapeToSave($transSel);
			
			// Verifichiamo se bisogna aggiornare la data di ultima modifica del transfer
			verifica_modifica($transSel, 'arr');
			
			$sql_update = sqlTransUpdate($transSel).' WHERE id='.$transSel['id'].';';
			$db = db_connect();
			try {
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->query($sql_update);
			}
			catch(Exception $e) {
				echo 'modificaTrans Exception -> ';
				var_dump($e->getMessage());
			}
			$db->connection = NULL;
		}
	
	}
}

// Clear the output
if(ob_get_length()) ob_clean();
// Headers are sent to prevent browsers from caching
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT'); 
header('Cache-Control: no-cache, must-revalidate'); 
header('Pragma: no-cache');
header('Content-Type: application/json');
// retrieve new trans from the server
if($mode == "RetrieveNew")	 echo json_encode($trans->retrieveNewTrans($date_list, $last_update, $op));
elseif($mode == "RemoveComm" || $mode == "CheckTrans") echo json_encode($response);
?>
