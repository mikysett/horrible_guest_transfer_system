<?php

// load configuration file
require_once('funzioni_admin.php');

// load error handling module
// require_once('error_handler.php');

// Chat class that contains server-side chat functionality
class Trans
{
  // database handler
  private $db;
  private $tipo;
  
  // constructor opens database connection
  function __construct() 
  {   
    // connect to the database
    $this->db = db_connect();
    // Set the two types of transfers
    $this->tipo = array('arr', 'par');   
  }
 
  // destructor closes database connection  
  public function __destruct() 
  {
    $this->db->connection = NULL;
  }

  /*
   The retrieveNewTrans method retrieves the new trans that have 
   been posted to the server. 
   - the $id parameter is sent by the client and it
   represents the id of the last trans received by the client. Trans
   more recent by $id will be fetched from the database and returned to
   the client in JSON format.
  */
  // op = operazione
  public function retrieveNewTrans($date_list, $last_update, $op=NULL) 
  {
  	// build the JSON response
	$response = array();
	
	if($last_update == NULL) $last_update = 0;
  	
  	// Settiamo il tipo di operazione da ritornare al client
  	if($op != NULL) $response['op'] = $op;
  	
    // if there is no default value for date_list
    if($date_list == NULL) $date_list = $_SESSION['oggi'];
    // !! object Date in js works in milliseconds, in php seconds !!
	
	// Inseriamo la data attuale come data dell'ultimo aggiornamento
	$response['last_update'] = time();
	// Array con gli elementi eliminati da aggiornare
	$clear = array();
	    
    // compose the SQL query that retrieves new trans
	$select = 'SELECT transfer.*, commenti.id AS com_id, commenti.id_transfer, commenti.tutti AS com_tutti,'//
					 .' commenti.reception AS com_reception, commenti.governante AS com_governante, commenti.ristorante AS com_ristorante, commenti.facchini AS com_facchini, commenti.barca AS com_barca,'//
					 .' commenti.taxi AS com_taxi, commenti.cliente AS com_cliente, commenti.tipo_commento AS com_tipo_commento, commenti.testo AS com_testo, commenti.operatore AS com_operatore, '//
					 .' commenti.data_creazione AS com_data_creazione'
					 .' FROM transfer LEFT JOIN commenti ON transfer.id=commenti.id_transfer';
	
	$query_arr = $select . ' WHERE data_arr='.$date_list.' AND ultima_mod_arr>'.$last_update.' ORDER BY ora_barca_arr_cal, nome, id';
	$query_par = $select . ' WHERE data_par='.$date_list.' AND ultima_mod_par>'.$last_update.' ORDER BY ora_barca_par, nome, id';
	$query_note = 'SELECT * FROM note_giornaliere WHERE data='.$date_list.' AND data_creazione> '.$last_update.' ORDER BY data_creazione ASC';
	$query_mezzi = 'SELECT id,mezzo,stato,timestamp_stato FROM stato_mezzi WHERE mezzo < 3 ORDER BY mezzo';
   

	try {
	 	$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   // execute the query
	   $result_arr = $this->db->query($query_arr);
	   $result_par = $this->db->query($query_par);
		$result_note_gg = $this->db->query($query_note);	
		$result_mezzi = $this->db->query($query_mezzi);	
	}
	catch(Exception $e) {
		echo 'Take new trans Exception -> ';
	 	var_dump($e->getMessage());
	}
	
	$result['arr'] = $result_arr->fetchAll(PDO::FETCH_ASSOC);
	$result['par'] = $result_par->fetchAll(PDO::FETCH_ASSOC);
	$result['note_gg'] = $result_note_gg->fetchAll(PDO::FETCH_ASSOC);
	$result['mezzi'] = $result_mezzi->fetchAll(PDO::FETCH_ASSOC);
	
	
	// Inseriamo le note giornaliere se ci sono
	$num_note = count($result['note_gg']);
	if($num_note > 0) {
		$response['note_gg'] = array();
				
		for($j = 0 ; $j < $num_note ; $j++) {
			$nota_sel = &$result['note_gg'][$j];
			
			// Se la nota non è vuota la aggiungiamo alla lista delle note
			if(!empty($nota_sel["testo"]))
				array_push($response['note_gg'],$nota_sel);
			// Se la nota è vuota la aggiungiamo alla lista delle note da eliminare
			else {
				// Se la lista è richiesta per la prima volta gli elementi eliminati non sono necessari
				if($last_update == 0) continue;
				
				$clear[] = array(
					"list" => "nota_gg",
					"id" => $nota_sel["id"]
				);
			}
		}
	}
	
	// Gestione dello stato mezzi
	$num_stato_mezzi = count($result['mezzi']);
	
	if($num_stato_mezzi > 0) {
		$response['mezzi'] = array();
				
		for($j = 0 ; $j < $num_stato_mezzi ; $j++) {
			array_push($response['mezzi'],$result['mezzi'][$j]);  
		}
	}
	
	// Gestione della query per i trans di arrivi e partenze
	for($i = 0 ; $i < 2 ; $i++) {
		$list_sel = &$result[$this->tipo[$i]];
		$num_trans = count($list_sel);
		
		if($num_trans) {
			$response['trans_'.$this->tipo[$i]] = array();
				
			for($j = 0, $num_com = 0, $pointer = 0 ; $j < $num_trans ; $j++) {
				$trans_sel = &$list_sel[$j];
				
				// Se il transfer è eliminato e va segnalato
				if($trans_sel["stato_arr"] == -1 || $trans_sel["stato_par"] == -1) {
					// Se la lista è richiesta per la prima volta gli elementi eliminati non sono necessari
					if($last_update == 0) continue;
					
					$clear[] = array(
						"list" => $this->tipo[$i],
						"id" => $trans_sel["id"]
					);
				}
				
				// I commenti vengono estratti e inseriti come sotto array del transfer
				elseif($trans_sel["com_id"] != NULL) {
					// Organizziamo i commenti come sotto array del transfer
					setCommenti($list_sel, $j);
					
					$num_comm = count($trans_sel["comm"]);
					
					// Si aggiunge il trans con i commenti all'array JSON
					array_push($response['trans_'.$this->tipo[$i]],array_filter($trans_sel));
					
					// Si fa avanzare il puntatore
					$j += $num_comm - 1; // -1 perché $j verrà incrementato a inizio loop
				}
				// Se il transfer non ha commenti
				else {
					array_push($response['trans_'.$this->tipo[$i]],array_filter($trans_sel));
				}
			}
		}
		
		// Se ci sono elementi cancellati da segnalare li aggiungiamo alla risposta
		$num_clx = count($clear);
		if($num_clx > 0) {
			$response['clx'] = array();
			
			for($z = 0 ; $z < $num_clx ; $z++) array_push($response['clx'], $clear[$z]);
		}
	}
    
    // return the JSON response
    return $response;    
  }
}

// Inserisce i commenti di un transfer come sotto insieme del transfer stesso
function setCommenti(&$list_sel, $pointer) {
	$trans_sel = &$list_sel[$pointer];
	$id_trans = &$trans_sel['id'];
	$num_trans = count($list_sel);
	$commenti = array();
	
	// Finchè ci sono commenti inerenti a questo transfer
	for($z = $pointer ; $z < $num_trans ; $z++) {
		$comm_sel = &$list_sel[$z];
		
		if($comm_sel["id"] == $id_trans) {
			$commenti[] = array(
				"id" => $comm_sel["com_id"],
				"tutti" => $comm_sel["com_tutti"],
				"reception" => $comm_sel["com_reception"],
				"governante" => $comm_sel["com_governante"],
				"ristorante" => $comm_sel["com_ristorante"],
				"barca" => $comm_sel["com_barca"],
				"facchini" => $comm_sel["com_facchini"],
				"taxi" => $comm_sel["com_taxi"],
				"cliente" => $comm_sel["com_cliente"],
				"tipo_commento" => $comm_sel["com_tipo_commento"],
				"testo" => $comm_sel["com_testo"],
				"operatore" => $comm_sel["com_operatore"],
				"data" => $comm_sel["com_data_creazione"]
			);
		}
		else break;
	}
	
	// Eliminiamo il primo commento dal transfer e poi aggiungiamo il sub array commenti al transfer
	unset($trans_sel["com_id"]);
	unset($trans_sel["id_transfer"]);
	unset($trans_sel["com_tutti"]);
	unset($trans_sel["com_reception"]);
	unset($trans_sel["com_governante"]);
	unset($trans_sel["com_ristorante"]);
	unset($trans_sel["com_barca"]);
	unset($trans_sel["com_facchini"]);
	unset($trans_sel["com_taxi"]);
	unset($trans_sel["com_cliente"]);
	unset($trans_sel["com_tipo_commento"]);
	unset($trans_sel["com_testo"]);
	unset($trans_sel["com_operatore"]);
	unset($trans_sel["com_data_creazione"]);
	
	$trans_sel["comm"] = $commenti;
}
?>
