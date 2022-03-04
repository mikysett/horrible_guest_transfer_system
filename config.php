<?php
// Per i test il valore dev'essere On, invice quando il sito è online deve essere off
ini_set('display_errors', 'On');
ini_set('default_charset', 'utf-8');
date_default_timezone_set('Europe/Rome');



// unico punto per connessione al database
function db_connect($query=NULL) {
	// dati per connessione al database
	$db = new PDO('mysql:host='.$_SESSION['host'].';dbname='.$_SESSION['db_name'].';charset=utf8', $_SESSION['db_user'], $_SESSION['db_pass']);
	
	// Se non è stato richiesto di eseguire una query
	if($query == NULL) return $db;	
	
	// Se si vuole eseguire una query restituiamo il risultato e chiudiamo la connessione al db
	try {
	 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$queryResult = $db->query($query);
	}
	catch(Exception $e) {
		echo 'DB Login Exception -> ';
	 	var_dump($e->getMessage());
	}
	$db->connection = NULL;
	
	return $queryResult;
}

function valori_default() {
	// Per il Database
	$_SESSION['host'] 		= 'localhost';
	$_SESSION['db_name'] 	= 'trans';
	$_SESSION['db_user'] 	= 'phpmyadmin';
	$_SESSION['db_pass'] 	= 'root';

	// Per le date
	$_SESSION['mesi'] = array('GEN', 'FEB', 'MAR', 'APR', 'MAG', 'GIU', 'LUG', 'AGO', 'SET', 'OTT', 'NOV', 'DIC');
	$_SESSION['giorni'] = array('Do', 'Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa');
	$_SESSION['giorni_long'] = array('DOMENICA', 'LUNEDI', 'MARTEDI', 'MERCOLEDI', 'GIOVEDI', 'VENERDI', 'SABATO');
	
	$_SESSION['tipo_transfer'] = array('PERSONE', 'MERCE', 'IN ESCLUSIVA', 'DIPENDENTI', 'VILLE', 'PROPRIETARI', 'RISTORANTE', 'ALTRO');
	$_SESSION['barche'] = array('', '<span class="danger">FABIR3</span>', '<i>FABIR4</i>', 'ROTATION', 'B. PRIVE', 'ELICOMPANY', 'HELISUD', 'ELICO', 'ALTRO');
	$_SESSION['barche_nostyle'] = array('', 'FABIR3', 'FABIR4', 'ROTATION', 'B. PRIVE', 'ELICOMPANY', 'HELISUD', 'ELICO', 'ALTRO');
	$_SESSION['num_barche'] = count($_SESSION['barche']);
	$_SESSION['porti'] = array('', 'CAV', 'PIANT', '<span class="danger">ST. TE</span>', 'ALTRO');
	$_SESSION['taxi'] = array('', 'MASSIMI', 'TOMMASO', 'AUTO PROPRIA', 'ALTRO');
	$_SESSION['tipo_lista'] = 0;
	$_SESSION['assegna_camere'] = FALSE;
	
	$_SESSION['stato_trans'] = array('', '<span class="sinbol_boh_boh">??</span>', '<span class="sinbol_boh">?</span>', '', '');
		
	$_SESSION['num_barche'] = count($_SESSION['barche']);
	$_SESSION['num_taxi'] = count($_SESSION['taxi']);
	
	$data_ora_oggi = time();
	
	// Si crea una variabile globale da riutilizzare nelle varie funzioni
	$_SESSION['oggi_giorno'] = date('d', $data_ora_oggi);
	$_SESSION['oggi_mese'] = date('n', $data_ora_oggi);
	$_SESSION['oggi_anno'] = date('Y', $data_ora_oggi);
	$_SESSION['oggi'] = mktime(0, 0, 0, $_SESSION['oggi_mese'], $_SESSION['oggi_giorno'], $_SESSION['oggi_anno']);
	
	$_SESSION['timestamp_lista'] = $_SESSION['oggi'];
	
	// Per la visualizzazione liste
	$_SESSION['persone']		 = 1;
	$_SESSION['esclusiva']	 = 1;
	$_SESSION['merce']		 = 1;
	$_SESSION['dipendenti']	 = 1;
	$_SESSION['ville']		 = 1;
	$_SESSION['proprietari'] = 1;
	$_SESSION['ristorante']	 = 1;
	$_SESSION['altro']		 = 1;
	
	$_SESSION['opz_hide']	 = 0;
}
?>