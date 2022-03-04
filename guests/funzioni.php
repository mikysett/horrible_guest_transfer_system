<?php
// Per i test il valore dev'essere On, invice quando il sito è online deve essere off
ini_set('display_errors', 'On');
ini_set('default_charset', 'utf-8');
date_default_timezone_set('Europe/Rome');

// unico punto per connessione al database
function db_connect() {
	// dati per connessione al database in locale
	$db = new PDO('mysql:host='.$_SESSION['host'].';dbname='.$_SESSION['db_name'].';charset=utf8', $_SESSION['db_user'], $_SESSION['db_pass']);
}

// carica i valori di default
function valori_default() {
	// Per il Database
	$_SESSION['host'] 		= 'localhost';
	$_SESSION['db_name'] 	= 'trans';
	$_SESSION['db_user'] 	= 'phpmyadmin';
	$_SESSION['db_pass'] 	= 'root';

	$_SESSION['lingue'] 	= 'fr','it','en';
	
	
	// Recuperare tutti i testi per la sezione trans
	
	// IMPLEMENTARE!!!!
	
	
}

// formatta il testo per la corretta visualizzazione al momento della stampa su schermo
function formatta_visualizzazione(&$testo_grezzo) {
	return stripslashes(htmlspecialchars_decode($testo_grezzo));
}

// Formatta testo per visualizzazione + non breaking spaces
function set_visual(&$testo_grezzo) {
	return str_replace(' ', '&nbsp;', stripslashes(htmlspecialchars_decode($testo_grezzo)));
}

function formatta_salvataggio(&$testo_grezzo) {
	return htmlentities(strtoupper(addslashes($testo_grezzo)), ENT_COMPAT, "UTF-8");
}

function formatta_salvataggio_sensitive(&$testo_grezzo) {
	return htmlentities(addslashes($testo_grezzo), ENT_COMPAT, "UTF-8");
}

function formatta_salvataggio_nobreak(&$testo_grezzo) {
	return htmlentities(addslashes(str_replace(' ', '&nbsp;', $testo_grezzo)), ENT_COMPAT, "UTF-8");
}

function formatta_edizione(&$testo_grezzo) {
	return str_replace('"', '&quot;', $testo_grezzo);
}

function header_standard() { ?>
<link rel="stylesheet" href="layout/admin.css" type="text/css" />
<link rel="icon" href="favicon.png" type="image/png" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="it" />
<meta name="author" content="michele" />
<meta name="copyright" content="Michele Sessa" />
<meta name="robots" content="none" /><?php
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

function compara_prenotazioni(&$pre_1, &$pre_2) {
	if($pre_1['tipo_pre'] > 9 || $pre_1['stile_spe'] != 0
	   || $pre_1['nome'] != $pre_2['nome'] || $pre_1['tipo_pre'] != $pre_2['tipo_pre'] || $pre_1['camera'] != $pre_2['camera'] || $pre_1['vestizione'] != $pre_2['vestizione'] //
		|| $pre_1['tipologia'] != $pre_2['tipologia'] || $pre_1['pax'] != $pre_2['pax'] || $pre_1['arrangiamento'] != $pre_2['arrangiamento'] //
		|| $pre_1['primo_pasto'] != $pre_2['primo_pasto'] || $pre_1['ultimo_pasto'] != $pre_2['ultimo_pasto'] || $pre_1['data_arrivo'] != $pre_2['data_arrivo'] //
		|| $pre_1['data_partenza'] != $pre_2['data_partenza'] || $pre_1['note'] != $pre_2['note'] || $pre_1['colore_note'] != $pre_2['colore_note']) {
		return FALSE;		
	}
	return TRUE;
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
	if($trans['nome'] 	!= $old_trans[0]['nome']) { $trans['ultima_mod_arr'] = time(); $trans['ultima_mod_par'] = $trans['ultima_mod_arr']; return; }
	if($trans['pax_ad'] 	!= $old_trans[0]['pax_ad']) { $trans['ultima_mod_arr'] = time(); $trans['ultima_mod_par'] = $trans['ultima_mod_arr']; return; }
	if($trans['pax_bam'] != $old_trans[0]['pax_bam']) { $trans['ultima_mod_arr'] = time(); $trans['ultima_mod_par'] = $trans['ultima_mod_arr']; return; }
	if($trans['camera'] 	!= $old_trans[0]['camera']) { $trans['ultima_mod_arr'] = time(); $trans['ultima_mod_par'] = $trans['ultima_mod_arr']; return; }
	
	
	// Verifichiamo i dati specifici all'arrivo o alla partenza
	for($i = 0, $tip = 'arr', $a_p = 'ARRIVO'; $i < 2 ; $i++) {
		if($i == 1) { $tip = 'par'; }
		
		if($trans['data_'.$tip]				 != $old_trans[0]['data_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['luogo_'.$tip]			 != $old_trans[0]['luogo_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['volo_'.$tip]				 != $old_trans[0]['volo_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['taxi_'.$tip] 			 != $old_trans[0]['taxi_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['ora_taxi_'.$tip] 		 != $old_trans[0]['ora_taxi_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['porto_partenza_'.$tip] != $old_trans[0]['porto_partenza_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['barca_'.$tip]			 != $old_trans[0]['barca_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['porto_arrivo_'.$tip]	 != $old_trans[0]['porto_arrivo_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['ora_barca_'.$tip]		 != $old_trans[0]['ora_barca_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['stato_'.$tip]			 != $old_trans[0]['stato_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
		if($trans['operatore_'.$tip]		 != $old_trans[0]['operatore_'.$tip]) { $trans['ultima_mod_'.$tip] = time(); continue; }
	}
}

?>