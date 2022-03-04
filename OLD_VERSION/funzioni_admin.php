<?php
// Per i test il valore dev'essere On, invice quando il sito è online deve essere off
ini_set('display_errors', 'On');
ini_set('default_charset', 'utf-8');
date_default_timezone_set('Europe/Rome');

// unico punto per connessione al database
function db_connect() {
	// dati per connessione al database
	$db = new PDO('mysql:host='.$_SESSION['host'].';dbname='.$_SESSION['db_name'].';charset=utf8', $_SESSION['db_user'], $_SESSION['db_pass']);
	return $db;
}

// carica i valori di default
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

function header_standard() { ?>
<link rel="stylesheet" href="layout/admin.css" type="text/css" />
<link rel="icon" href="favicon.png" type="image/png" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="it" />
<meta name="author" content="michele" />
<meta name="copyright" content="Michele Sessa" />
<meta name="robots" content="none" /><?php
}

function header_testi_trans() { ?>
<link rel="stylesheet" href="../layout/admin.css" type="text/css" />
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

function menu_top($pagina) {
	$giorni_settimana = array("DOM", "LUN", "MAR", "MER", "GIO", "VEN", "SAB");
	
	echo '<div class="testata_booking">';
	
	// INFO GENERICHE
	echo '<div class="info_generiche">i';
	echo '<div class="tutte_info">';
	
	
	/*echo '<b>ROTATION ASIC DI LUGLIO/AGOSTO</b><br /><br />';
	echo '<b>DAL LUNEDÌ AL SABATO</b><br />';
	echo 'PIANTARELLA/CAVALLO : 07:30 - (09:30) - 13:00 - 16:00 - 18:30 - 20:00<br />';
	echo 'CAVALLO/PIANTARELLA : 07:00 - (09:00) - 12:30 - 15:30 - 18:00 - 19:30<br /><br />';
	
	echo '<b>GIOVEDÌ (BONIFACIO) <span style="color:red">€ 25,00</span></b><br />';
	echo 'BONIFACIO/CAVALLO : 11:30<br />';
	echo 'CAVALLO/BONIFACIO : 09:00<br /><br />';
	
	echo '<b>DOMENICA</b><br />';
	echo 'PIANTARELLA/CAVALLO : 07:30 - 09:30 - 13:00 - 18:30<br />';
	echo 'CAVALLO/PIANTARELLA : 07:00 - 09:00 - 12:30 - 18:00<br /><br />';
	
	
	echo '<span style="color:blue">';
	echo '<b>NAVETTA MASSIMI (€ 5,00) TUTTI I GIORNI FINO AL 31/08</b><br />';
	echo 'PIANTARELLA/BONIFACIO : 09:30 - 11:00 - 13:00 - 16:00 - 18:30 - 20:00<br />';
	echo 'BONIFACIO/PIANTARELLA : 08:45 - 10:15 - 12:15 - 15:15 - 17:45 - 19:15<br /><br />';
	echo '</span>';
	
	echo '<b>ROTATION ASIC DAL 01 AL 09 SETTEMBRE</b><br /><br />';
	echo '<b>DAL LUNEDÌ AL SABATO</b><br />';
	echo 'PIANTARELLA/CAVALLO : 07:30 - 09:30 - 13:00 - 16:00 - 18:30<br />';
	echo 'CAVALLO/PIANTARELLA : 07:00 - 09:00 - 12:30 - 15:30 - 18:00<br /><br />';
	
	echo '<b>DOMENICA</b><br />';
	echo 'PIANTARELLA/CAVALLO : 07:30 - 09:30 - 13:00 - 18:30<br />';
	echo 'CAVALLO/PIANTARELLA : 07:00 - 09:00 - 12:30 - 18:00<br /><br />';*/
	
	echo '<b>ROTATION ASIC DAL 10 AL 29 SETTEMBRE</b><br /><br />';
	echo '<b>DAL LUNEDÌ AL SABATO</b><br />';
	echo 'PIANTARELLA/CAVALLO : 07:30 - 12:15 - 17:15<br />';
	echo 'CAVALLO/PIANTARELLA : 07:00 - 12:00 - 17:00<br /><br />';
	
	echo '<b>DOMENICA NESSUN VIAGGIO</b><br /><br />';
	
	echo '<table>';
	//echo '<tr><td>SPA</td><td>10:00-20:00</td></tr>';
	echo '<tr><td>SPA</td><td>10:00-13:00 / 15:00-20:00</td></tr>';
	echo '<tr><td>BOUTIQUE</td><td>10:00-20:00</td></tr>';
	echo '<tr><td>DIVING</td><td>08:00-13:00 / 15:00-20:00</td></tr>';
	echo '<tr><td>EPICERIE</td><td>08:30-13:00 / 16:00-20:00</td></tr>';
	echo '</table>';
	
	echo '</div>';
	echo '</div>';
	
	
	
	echo '<div class="menu_top"><a href="/OLD_VERSION/lista_trans.php">HOME</a></div>';

	if($pagina != ' | ') echo $pagina;
	
	// Se la pagina è quella principale stampiamo un menu con più opzioni
	if($pagina == ' | ') {
		echo '<div class="menu_top">';
			echo '<p class="titolo_menu">LISTE</p>';
				echo '<div class="voci_menu">';
					echo '<p class="titolo_lista">LISTE HOTEL</p>';
					echo '<a class="tipo_lista';if($_SESSION['tipo_lista'] == 0) echo ' lista_sel';echo '" href="lista_trans.php?tipo_lista=0">RECEPTION</a>';
					echo '<a class="tipo_lista';if($_SESSION['tipo_lista'] == 1) echo ' lista_sel';echo '" href="lista_trans.php?tipo_lista=1">FACCHINI</a>';
					echo '<a class="tipo_lista';if($_SESSION['tipo_lista'] == 2) echo ' lista_sel';echo '" href="lista_trans.php?tipo_lista=2">FABIRS</a>';
					echo '<a class="tipo_lista';if($_SESSION['tipo_lista'] == 3) echo ' lista_sel';echo '" href="lista_trans.php?tipo_lista=3">FABIR 3</a>';
					echo '<a class="tipo_lista';if($_SESSION['tipo_lista'] == 4) echo ' lista_sel';echo '" href="lista_trans.php?tipo_lista=4">FABIR 4</a>';
					echo '<a class="tipo_lista';if($_SESSION['tipo_lista'] == 5) echo ' lista_sel';echo '" href="lista_trans.php?tipo_lista=5">ELICOMPANY</a>';
					echo '<a class="tipo_lista';if($_SESSION['tipo_lista'] == 8) echo ' lista_sel';echo '" href="lista_dipendenti.php">DIPENDENTI</a>';
					
					echo '<p class="titolo_lista">LISTE TAXI</p>';
					echo '<a class="tipo_lista';if($_SESSION['tipo_lista'] == 6) echo ' lista_sel';echo '" href="lista_trans.php?tipo_lista=6">MASSIMI</a>';
					echo '<a class="tipo_lista';if($_SESSION['tipo_lista'] == 7) echo ' lista_sel';echo '" href="lista_trans.php?tipo_lista=7">TOMMASO</a>';
				echo '</div>';
		echo '</div>';
		
		echo '<div class="menu_top">';
			echo '<p class="titolo_menu">OPZIONI';
			if($_SESSION['opz_hide'] > 0) echo ' <span class="num_hide">'.$_SESSION['opz_hide'].'</span>';
			echo '</p>';
			echo '<div class="voci_menu">';
				if($_SESSION['persone'] == 1)		echo '<a class ="opz_si" href="lista_trans.php?persone=0">PERSONE</a>';
				else 										echo '<a class ="opz_no" href="lista_trans.php?persone=1">PERSONE</a>';
				if($_SESSION['esclusiva'] == 1)	echo '<a class ="opz_si" href="lista_trans.php?esclusiva=0">IN ESCLUSIVA</a>';
				else 									 	echo '<a class ="opz_no" href="lista_trans.php?esclusiva=1">IN ESCLUSIVA</a>';
				if($_SESSION['merce'] == 1)	 	echo '<a class ="opz_si" href="lista_trans.php?merce=0">MERCE</a>';
				else 										echo '<a class ="opz_no" href="lista_trans.php?merce=1">MERCE</a>';
				if($_SESSION['dipendenti'] == 1) echo '<a class ="opz_si" href="lista_trans.php?dipendenti=0">DIPENDENTI</a>';
				else 										echo '<a class ="opz_no" href="lista_trans.php?dipendenti=1">DIPENDENTI</a>';
				if($_SESSION['ville'] == 1)		echo '<a class ="opz_si" href="lista_trans.php?ville=0">VILLE</a>';
				else 										echo '<a class ="opz_no" href="lista_trans.php?ville=1">VILLE</a>';
				if($_SESSION['proprietari'] == 1) echo '<a class ="opz_si" href="lista_trans.php?proprietari=0">PROPRIETARI</a>';
				else 										echo '<a class ="opz_no" href="lista_trans.php?proprietari=1">PROPRIETARI</a>';
				if($_SESSION['ristorante'] == 1) echo '<a class ="opz_si" href="lista_trans.php?ristorante=0">RISTORANTE</a>';
				else 										echo '<a class ="opz_no" href="lista_trans.php?ristorante=1">RISTORANTE</a>';
				if($_SESSION['altro'] == 1)		echo '<a class ="opz_si" href="lista_trans.php?altro=0">ALTRO</a>';
				else 										echo '<a class ="opz_no" href="lista_trans.php?altro=1">ALTRO</a>';
				echo '<p class="titolo_lista"></p>';
				echo '<a class ="tipo_lista" href="lista_trans.php?mostra_tutti=0">MOSTRA TUTTI</a>';
				echo '<a class ="opz_si" href="lista_trans.php?nascondi_tutti=0">NASCONDI TUTTI</a>';
			echo '</div>';
		echo '</div>';
		
		echo '<div class="menu_top">';
			echo '<p class="titolo_menu">AVANZATE</p>';
				echo '<div class="voci_menu">';
					if($_SESSION['assegna_camere'] == FALSE) {
						echo '<input class="bottone" name="assegna_camere" type="submit" value="ASSEGNA CAMERE" />';
					}
					else {
						echo '<input class="bottone" name="camere_assegnate" type="submit" value="SALVA CAMERE" />';
					}
					echo '<a class="tipo_lista" href="statistiche.php">STATISTICHE</a>';
					echo '<a class="tipo_lista" href="lista_trans.php?backup=1">SCARICA BACKUP</a>';
					echo '<a class="tipo_lista" href="/OLD_VERSION/disconnettersi.php">LOGOUT</a>';
				echo '</div>';
		echo '</div>';
		
		
		echo '<div class="menu_top"><a href="ge_trans.php">NUOVO TRANSFER</a></div>';
	}
	
	echo '<p class="data_refresh">';
	echo $giorni_settimana[date("w", $_SESSION['oggi'])].' '.$_SESSION['oggi_giorno'].'/'.date('H:i');
	if($pagina != ' | ') echo ' | <a href="/OLD_VERSION/disconnettersi.php">LOGOUT</a>';
	echo '</p>';
	
	echo '</div>';
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

function formatta_periodo(&$data_arrivo, &$data_partenza) {
	$arrivo_tab = explode('/', date('d/m/Y', $data_arrivo));
	$partenza_tab = explode('/', date('d/m/Y', $data_partenza));
	
	// Se rimaniamo nello stesso mese
	if($arrivo_tab[1] == $partenza_tab[1]) {
		// Se siamo nel medesimo anno di quello attuale
		if($partenza_tab[2] == $_SESSION['oggi_anno'])
			return '<b>'.$_SESSION['giorni'][date('w', $data_arrivo)].'</b> '.$arrivo_tab[0].' <b>'.$_SESSION['giorni'][date('w', $data_partenza)].'</b> '.$partenza_tab[0].' '.$_SESSION['mesi'][$partenza_tab[1]-1];
		else
			return '<b>'.$_SESSION['giorni'][date('w', $data_arrivo)].'</b> '.$arrivo_tab[0].' <b>'.$_SESSION['giorni'][date('w', $data_partenza)].'</b> '.$partenza_tab[0].' '.$_SESSION['mesi'][$partenza_tab[1]-1].' '.substr($partenza_tab[2], 2);
	}
	else {
		// Se le due date sono sullo stesso anno
		if($arrivo_tab[2] == $partenza_tab[2]) {
			// Se siamo sull'anno corrente
			if($partenza_tab[2] == $_SESSION['oggi_anno']) return '<b>'.$_SESSION['giorni'][date('w', $data_arrivo)].'</b> '.$arrivo_tab[0].'/'.$arrivo_tab[1].' <b>'.$_SESSION['giorni'][date('w', $data_partenza)].'</b> '.$partenza_tab[0].'/'.$partenza_tab[1];
			else 														  return '<b>'.$_SESSION['giorni'][date('w', $data_arrivo)].'</b> '.$arrivo_tab[0].'/'.$arrivo_tab[1].' <b>'.$_SESSION['giorni'][date('w', $data_partenza)].'</b> '.$partenza_tab[0].'/'.$partenza_tab[1].'/'.substr($partenza_tab[2], 2);
			
		}
		else  return '<b>'.$_SESSION['giorni'][date('w', $data_arrivo)].'</b> '.$arrivo_tab[0].'/'.$arrivo_tab[1].' <b>'.$_SESSION['giorni'][date('w', $data_partenza)].'</b> '.$partenza_tab[0].'/'.$partenza_tab[1].'/'.substr($partenza_tab[2], 2);
	}
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
					$lista_finale[$num_conf]['nominativo'] = formatta_visualizzazione($lista_barche[$i]['nome']);
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
					$lista_finale[$num_conf]['nominativo'] = formatta_visualizzazione($lista_barche[$i]['nome']);
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