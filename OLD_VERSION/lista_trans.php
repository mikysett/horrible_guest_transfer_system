<?php
session_start(); // Si lancia la sezione
require('funzioni_admin.php');

if(isset($_SESSION['tipo_user']) && $_SESSION['tipo_user'] == 0) { // Si verifica che sia l'amministratore


	// Se si è chiesto il backup
	if(isset($_GET['backup'])) {
		$backup = backup_tables('*');
	}

// Skippiamo tutto per il backup
else {

?>

<!--

Progetti per il futuro:
32x32 - PNG



    / \
	 / \
     |
     |
     |
   - |
	_-_-_
	
		
	
	
	
-->
<?php
	$azione = 0;
	$vuoto = '';
	$nome_ricerca = '';
	$_SESSION['tipo_lista'] = 0;
	$get_lista = '';
	$tot_trans = 0;
	$tot_ad = 0;
	$tot_bam = 0;
	$opzioni = '';
	
	if(!isset($_POST['cerca_nome']) || !isset($_POST['no_date']) || (isset($_POST['nome_ricerca']) && $_POST['nome_ricerca'] == NULL))
				if(isset($_GET['tipo_lista'])) $_SESSION['tipo_lista'] = $_GET['tipo_lista'];

	$colspan_com = array(15, 6, 5, 4, 4, 9, 9, 9);
	$condizioni_lista_arr = '';
	$condizioni_lista_par = '';
	
	// Se stiamo modificando lo stato di un mezzo
	if(isset($_GET['mezzo'])) {
		$mezzo = intval($_GET['mezzo']);
		$stato = intval($_GET['stato']);
		
		// Se lo stato richiede di conoscere l'orario
		if($stato >= 5 && $stato <= 8) $timestamp_stato = time();
		else 									 $timestamp_stato = 0;
		
		// Aggiorniamo il database
		$sql = 'UPDATE stato_mezzi SET stato='.$stato.',timestamp_stato='.$timestamp_stato.' WHERE mezzo='.$mezzo;
		
		$db = db_connect();
		try {
		 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$rep_stato_mezzi = $db->query($sql);
		}
		catch(Exception $e) {
			echo 'Exception -> ';
		 	var_dump($e->getMessage());
		}
		$db->connection = NULL;
	}
	
	// Paramentriamo il GET per il tipo di lista e settiamo le condizioni per rispettare la lista
	if(!isset($_POST['cerca_nome']) || !isset($_POST['no_date']) || (isset($_POST['nome_ricerca']) && $_POST['nome_ricerca'] == NULL)) {
		if(isset($_GET['tipo_lista'])) { $_SESSION['tipo_lista'] = $_GET['tipo_lista']; $get_lista = '?tipo_lista='.$_GET['tipo_lista']; }
		
		if($_SESSION['tipo_lista'] == 5) { $condizioni_lista_arr = ' AND barca_arr=5'; $condizioni_lista_par = ' AND barca_par=5'; } // ELICOMPANY
		if($_SESSION['tipo_lista'] == 6) { $condizioni_lista_arr = ' AND taxi_arr=1'; $condizioni_lista_par = ' AND taxi_par=1'; } // Massimi
		if($_SESSION['tipo_lista'] == 7) { $condizioni_lista_arr = ' AND taxi_arr=2'; $condizioni_lista_par = ' AND taxi_par=2'; } // Tommaso
		if($_SESSION['tipo_lista'] == 3) { $condizioni_lista_bar = ' AND (barca_arr=1 OR barca_par=1)'; } // Fabir 3
		if($_SESSION['tipo_lista'] == 4) { $condizioni_lista_bar = ' AND (barca_arr=2 OR barca_par=2)'; } // Fabir 4
		
		// Gestiamo le opzioni di visualizzazione
		if(isset($_GET['mostra_tutti'])) {
			$_SESSION['persone'] = 1;
			$_SESSION['esclusiva'] = 1;
			$_SESSION['merce'] = 1;
			$_SESSION['dipendenti'] = 1;
			$_SESSION['ville'] = 1;
			$_SESSION['proprietari'] = 1;
			$_SESSION['ristorante'] = 1;
			$_SESSION['altro'] = 1;
			
			$_SESSION['opz_hide'] = 0;
		}
		elseif(isset($_GET['nascondi_tutti'])) {
			$_SESSION['persone'] = 0;
			$_SESSION['esclusiva'] = 0;
			$_SESSION['merce'] = 0;
			$_SESSION['dipendenti'] = 0;
			$_SESSION['ville'] = 0;
			$_SESSION['proprietari'] = 0;
			$_SESSION['ristorante'] = 0;
			$_SESSION['altro'] = 0;
			
			$_SESSION['opz_hide'] = 8;
		}
		else {
			if(isset($_GET['persone'])) {
				$_SESSION['persone'] = intval($_GET['persone']);
				if($_SESSION['persone'] == 0) $_SESSION['opz_hide']++;
				else 									$_SESSION['opz_hide']--;
			}
			if(isset($_GET['esclusiva'])) {
				$_SESSION['esclusiva'] = intval($_GET['esclusiva']);
				if($_SESSION['esclusiva'] == 0) $_SESSION['opz_hide']++;
				else 									  $_SESSION['opz_hide']--;
			}
			if(isset($_GET['merce'])) {
				$_SESSION['merce'] = intval($_GET['merce']);
				if($_SESSION['merce'] == 0) $_SESSION['opz_hide']++;
				else 								 $_SESSION['opz_hide']--;
			}
			if(isset($_GET['dipendenti'])) {
				$_SESSION['dipendenti'] = intval($_GET['dipendenti']);
				if($_SESSION['dipendenti'] == 0) $_SESSION['opz_hide']++;
				else 										$_SESSION['opz_hide']--;
			}
			if(isset($_GET['ville'])) {
				$_SESSION['ville'] = intval($_GET['ville']);
				if($_SESSION['ville'] == 0) $_SESSION['opz_hide']++;
				else 								 $_SESSION['opz_hide']--;
			}
			if(isset($_GET['proprietari'])) {
				$_SESSION['proprietari'] = intval($_GET['proprietari']);
				if($_SESSION['proprietari'] == 0) $_SESSION['opz_hide']++;
				else 										 $_SESSION['opz_hide']--;
			}
			if(isset($_GET['ristorante'])) {
				$_SESSION['ristorante'] = intval($_GET['ristorante']);
				if($_SESSION['ristorante'] == 0) $_SESSION['opz_hide']++;
				else 										$_SESSION['opz_hide']--;
			}
			if(isset($_GET['altro'])) {
				$_SESSION['altro'] = intval($_GET['altro']);
				if($_SESSION['altro'] == 0) $_SESSION['opz_hide']++;
				else 								 $_SESSION['opz_hide']--;
			}
		}
		
		// Creiamo la parte di query per le opzioni
		if($_SESSION['persone'] == 0) $opzioni .= ' AND tipo_transfer != 0';
		if($_SESSION['esclusiva'] == 0) $opzioni .= ' AND tipo_transfer != 2';
		if($_SESSION['merce'] == 0) $opzioni .= ' AND tipo_transfer != 1';
		if($_SESSION['dipendenti'] == 0) $opzioni .= ' AND tipo_transfer != 3';
		if($_SESSION['ville'] == 0) $opzioni .= ' AND tipo_transfer != 4';
		if($_SESSION['proprietari'] == 0) $opzioni .= ' AND tipo_transfer != 5';
		if($_SESSION['ristorante'] == 0) $opzioni .= ' AND tipo_transfer != 6';
		if($_SESSION['altro'] == 0) $opzioni .= ' AND tipo_transfer != 7';
	}
	
	if(isset($_GET['data'])) $_SESSION['timestamp_lista'] = $_GET['data'];
	
	// Se si stanno assegnando le camere
	if(isset($_POST['assegna_camere'])) {
		$_SESSION['assegna_camere'] = TRUE;
	}
	elseif(isset($_POST['camere_assegnate'])) { // Se si stanno salvando le assegnazioni
		$_SESSION['assegna_camere'] = FALSE;
		$agg = FALSE;
		$adesso = time();
		$sql_agg_cam = 'INSERT INTO transfer (id, camera, ultima_mod_arr, ultima_mod_par) VALUES ';
		
		// creiamo la query
		$sql_query_arr = 'SELECT id,camera FROM transfer WHERE data_arr='.$_SESSION['timestamp_lista'].$condizioni_lista_arr.' ORDER BY ora_barca_arr_cal, nome';
		$sql_query_par = 'SELECT id,camera FROM transfer WHERE data_par='.$_SESSION['timestamp_lista'].$condizioni_lista_par.' ORDER BY ora_barca_par_cal, nome';
		
		$db = db_connect();
		$rep_arr = $db->query($sql_query_arr);
		$rep_par = $db->query($sql_query_par);
			
		$trans_arr = $rep_arr->fetchAll(PDO::FETCH_ASSOC);
		$trans_par = $rep_par->fetchAll(PDO::FETCH_ASSOC);
		$num_arrivi = count($trans_arr);
		$num_partenze = count($trans_par);
		
		for($i = 0 ; $i < $num_arrivi ; $i++) {
			// Se l'id è presente lo inseriamo negli aggiornamenti da fare solo se la camera è cambiata
			if(isset($_POST[$trans_arr[$i]['id']])) {
				if($trans_arr[$i]['camera'] != formatta_salvataggio($_POST[$trans_arr[$i]['id']])) {
					if($agg == TRUE) $sql_agg_cam .= ',';
					$sql_agg_cam .= '('.$trans_arr[$i]['id'].',\''.formatta_salvataggio($_POST[$trans_arr[$i]['id']]).'\','.$adesso.','.$adesso.')';
					$agg = TRUE;
				}
			}
		}
		
		for($i = 0 ; $i < $num_partenze ; $i++) {
			// Se l'id è presente lo inseriamo negli aggiornamenti da fare
			if(isset($_POST[$trans_par[$i]['id']])) {
				if($trans_par[$i]['camera'] != formatta_salvataggio($_POST[$trans_par[$i]['id']])) {
					if($agg == TRUE) $sql_agg_cam .= ',';
					$sql_agg_cam .= '('.$trans_par[$i]['id'].',\''.formatta_salvataggio($_POST[$trans_par[$i]['id']]).'\','.$adesso.','.$adesso.')';
					$agg = TRUE;
				}
			}
		}
		
		// Se ci sono aggiornamenti aggiorniamo il database
		if($agg == TRUE) {
			$sql_agg_cam .= 'ON DUPLICATE KEY UPDATE camera=VALUES(camera),ultima_mod_arr=VALUES(ultima_mod_arr),ultima_mod_par=VALUES(ultima_mod_par);';
			try {
			 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->query($sql_agg_cam);
			}
			catch(Exception $e) {
				echo 'Exception -> ';
			 	var_dump($e->getMessage());
			}
		}
		
		
		$db->connection = NULL;
	}
	
	// Se si sta aggiungendo una nota giornaliera
	if(isset($_POST['add_nota_gg'])) {
		$reparti = '';
		$val_reparti = '';
		if(isset($_POST['tutti_com']))		 { $reparti .= ', tutti'; $val_reparti .= ',1 '; }
		if(isset($_POST['reception_com']))	 { $reparti .= ', reception'; $val_reparti .= ',1 '; }
		if(isset($_POST['facchini_com']))	 { $reparti .= ', facchini'; $val_reparti .= ',1 '; }
		if(isset($_POST['barca_com']))		 { $reparti .= ', barca'; $val_reparti .= ',1 '; }
		if(isset($_POST['taxi_com']))			 { $reparti .= ', taxi'; $val_reparti .= ',1 '; }
		
		$db = db_connect();
		$db->query('INSERT INTO note_giornaliere (data,testo,data_creazione'.$reparti.') VALUES ('.$_SESSION['timestamp_lista'].',"'.formatta_salvataggio($_POST['testo_nota_gg']).'",'.time().$val_reparti.')');
		$db->connection = NULL;
	}
	
	
	// Se si è scelto di cancellare una nota giornaliera
	elseif(isset($_GET['clx_nota'])) {
		$db = db_connect();
		$db->query('DELETE FROM note_giornaliere WHERE id='.$_GET['clx_nota']);
		$db->connection = NULL;
	}
	
	
	elseif(isset($_POST['clx_trans']) || isset($_GET['clx_trans'])) {
		if(isset($_POST['clx_trans'])) $id_pre = intval($_POST['clx_trans']);
		else 									 $id_pre = intval($_GET['clx_trans']);
		
		$db = db_connect();
		try {
			 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$rispostaPrenotazione = $db->query('SELECT nome FROM transfer WHERE id=' . $id_pre);
		}
		catch(Exception $e) {
				echo 'Exception -> ';
			 	var_dump($e->getMessage());
		}
		$db->connection = NULL;
		
		$dati_pre_el 	= $rispostaPrenotazione->fetch(PDO::FETCH_ASSOC);
		$nome_el			= $dati_pre_el['nome'];
		
		$azione = 'ASK TO DELETE PRIVATE';
	}
	
	//se si tratta di eliminare e si é confermato si elimina
	if(isset($_POST['conferma_clx_trans'])) {
		$id_pre_clx = intval($_POST['conferma_clx_trans']);
		
		$db = db_connect();
		// $db->query('DELETE FROM transfer WHERE id='.$id_pre_clx); OLD
		// NEW
		$date_clx = time();
		$db->query('UPDATE transfer SET stato_arr=-1,stato_par=-1, ultima_mod_arr='.$date_clx.', ultima_mod_par='.$date_clx.' WHERE id='.$id_pre_clx);
		$db->query('DELETE FROM commenti WHERE id_trans='.$id_pre_clx);
		$db->query('DELETE FROM invii_email WHERE id_trans='.$id_pre_clx);
		$db->connection = NULL;
		
		$azione = 'PRIVATE DELETED';
	}
	
	// Se si è cercato per nome
	if(isset($_POST['cerca_nome']) && $_POST['nome_ricerca'] != '') {
		$nome_ricerca = formatta_salvataggio($_POST['nome_ricerca']);
		
		$sql_query = 'SELECT transfer.*, commenti.id AS com_id, commenti.id_transfer, commenti.tutti AS com_tutti,'//
						  .' commenti.reception AS com_reception, commenti.governante AS com_governante, commenti.ristorante AS com_ristorante, commenti.facchini AS com_facchini, commenti.barca AS com_barca,'//
						  .' commenti.taxi AS com_taxi, commenti.cliente AS com_cliente, commenti.tipo_commento AS com_tipo_commento, commenti.testo AS com_testo, commenti.operatore AS com_operatore, '//
						  .' commenti.data_creazione AS com_data_creazione FROM transfer LEFT JOIN commenti ON transfer.id=commenti.id_transfer WHERE nome LIKE \'%'.$nome_ricerca.'%\' ORDER BY ora_barca_arr_cal, nome';
		
		$db = db_connect();
		$rep_barche_arr1 = $db->query($sql_query);
		$trans_arr = $rep_barche_arr1->fetchAll(PDO::FETCH_ASSOC);
		$db->connection = NULL;
		$trans_par1 = NULL;
		$num_arrivi = count($trans_arr);
		$num_partenze = 0;
	}
	
	// Se ci si basa su una data
	else {
		// Se si è scelto di cambiare giorno in avanti
		if(isset($_POST['giorno_dopo'])) {
			$_SESSION['timestamp_lista'] = strtotime('+1 day', $_SESSION['timestamp_lista']);
		}
		
		// Se si è scelto di cambiare giorno in avanti
		elseif(isset($_POST['giorno_prima'])) {
			$_SESSION['timestamp_lista'] = strtotime('-1 day', $_SESSION['timestamp_lista']);
		}
		
		// Se si è scelto di cambiare giorno in avanti
		elseif(isset($_POST['cerca_data'])) {
			$_SESSION['timestamp_lista'] = controllo_data($_POST['data_cercata']);
			if($_SESSION['timestamp_lista'] == NULL) $_SESSION['timestamp_lista'] = $_SESSION['oggi'];
		}
		
		// creiamo la query
		$sql_query_arr = 'SELECT transfer.*, commenti.id AS com_id, commenti.id_transfer, commenti.tutti AS com_tutti,'//
							  .' commenti.reception AS com_reception, commenti.governante AS com_governante, commenti.ristorante AS com_ristorante, commenti.facchini AS com_facchini, commenti.barca AS com_barca,'//
							  .' commenti.taxi AS com_taxi, commenti.cliente AS com_cliente, commenti.tipo_commento AS com_tipo_commento, commenti.testo AS com_testo, commenti.operatore AS com_operatore, '//
							  .' commenti.data_creazione AS com_data_creazione FROM transfer LEFT JOIN commenti ON transfer.id=commenti.id_transfer WHERE data_arr='.$_SESSION['timestamp_lista'].$condizioni_lista_arr.$opzioni.' ORDER BY ora_barca_arr_cal, nome, id';
		$sql_query_par = 'SELECT transfer.*, commenti.id AS com_id, commenti.id_transfer, commenti.tutti AS com_tutti,'//
							  .' commenti.reception AS com_reception, commenti.governante AS com_governante, commenti.ristorante AS com_ristorante, commenti.facchini AS com_facchini, commenti.barca AS com_barca,'//
							  .' commenti.taxi AS com_taxi, commenti.cliente AS com_cliente, commenti.tipo_commento AS com_tipo_commento, commenti.testo AS com_testo, commenti.operatore AS com_operatore, '//
							  .' commenti.data_creazione AS com_data_creazione FROM transfer LEFT JOIN commenti ON transfer.id=commenti.id_transfer WHERE data_par='.$_SESSION['timestamp_lista'].$condizioni_lista_par.$opzioni.' ORDER BY ora_barca_par_cal, nome, id';
		
		$db = db_connect();
		
		// Per tutte le liste tranne i fabirs
		if($_SESSION['tipo_lista'] != 2 && $_SESSION['tipo_lista'] != 3 && $_SESSION['tipo_lista'] != 4) {
			try {
			 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$rep_arr = $db->query($sql_query_arr);
				$rep_par = $db->query($sql_query_par);
			}
			catch(Exception $e) {
				echo 'Exception -> ';
			 	var_dump($e->getMessage());
			}
			
			$trans_arr = $rep_arr->fetchAll(PDO::FETCH_ASSOC);
			$trans_par = $rep_par->fetchAll(PDO::FETCH_ASSOC);
			$num_arrivi = count($trans_arr);
			$num_partenze = count($trans_par);
		}
		// Per liste fabirs
		else {
			$giorno_dopo_barche = strtotime('+1 day', $_SESSION['timestamp_lista']);
			
			if($_SESSION['tipo_lista'] != 2) { // Questo metodo va bene unicamente quando la lista è sulla barca singola, fabir 3 o fabir 4
				$sql_query_bar_arr = 'SELECT transfer.* FROM transfer WHERE data_arr='.$_SESSION['timestamp_lista'].$condizioni_lista_bar.' ORDER BY data_arr,barca_arr';
				$sql_query_bar_par = 'SELECT transfer.* FROM transfer WHERE data_par='.$_SESSION['timestamp_lista'].$condizioni_lista_bar.' ORDER BY data_par,barca_par';
			}
			else { // Questo metodo va bene per la lista sulle due barche
				$sql_query_bar_arr = 'SELECT transfer.* FROM transfer WHERE data_arr='.$_SESSION['timestamp_lista'].
											' AND (barca_arr=1 OR barca_arr=2) ORDER BY data_arr,barca_arr';
				$sql_query_bar_par = 'SELECT transfer.* FROM transfer WHERE data_par='.$_SESSION['timestamp_lista'].
											' AND (barca_par=1 OR barca_par=2) ORDER BY data_par,barca_par';
			}
			$rep_barche_arr1 = $db->query($sql_query_bar_arr);
			$rep_barche_par1 = $db->query($sql_query_bar_par);

			$trans_arr1 = $rep_barche_arr1->fetchAll(PDO::FETCH_ASSOC);
			$trans_par1 = $rep_barche_par1->fetchAll(PDO::FETCH_ASSOC);
			
			$rep_barche1 = array_merge($trans_par1, $trans_arr1);
			$lista_barche = gestione_barche($rep_barche1, $_SESSION['timestamp_lista']);
			$num_barche = count($lista_barche);
		}
		
		$db->connection = NULL;
	}
	

	

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
	<title>LISTA TRANSFER</title>
	<link rel="stylesheet" href="layout/admin.css" type="text/css" /><?php
	if($_SESSION['tipo_lista'] == 1) echo '<link rel="stylesheet" href="layout/facchini.css" type="text/css" />';
	?><link rel="icon" href="favicon.png" type="image/png" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-language" content="it" />
	<meta name="author" content="michele" />
	<meta name="copyright" content="Michele Sessa" />
	<meta name="robots" content="none" />
</head>
<body><?php
	
	// Apriamo il form prima del menu per il bottone assegna camere
	echo '<form name="dati" action="lista_trans.php'.$get_lista.'" method="post" enctype="multipart/form-data">';
	
	menu_top(' | ');
?>
		<div id="corpo_a"><?php
		// STAMPIAMO LA LISTA DEI TRANS

			//echo colonna_sinistra();
			
			echo '<div class="cont_dx">';
			
			// Pulsanti per ricerca e navigazione
			echo '<div class="menu_nav">';
			echo '<div class="box_nav">';
			
			// Se la ricerca non è per nome inseriamo la navigazione per data
			if(!isset($_POST['cerca_nome']) || !isset($_POST['no_date']) || (isset($_POST['nome_ricerca']) && $_POST['nome_ricerca'] == NULL)) {
				echo '<div class="ricerca_data">';
				echo '<input class="bottone" name="giorno_prima" type="submit" value="<" />';
				echo '<span class="day_week">'.$_SESSION['giorni'][date('w', $_SESSION['timestamp_lista'])].'</span>';
				echo '<input placeholder="DATA" type="text" name="data_cercata" class="field" value="'.date('d/m/Y', $_SESSION['timestamp_lista']).'" />';
				echo '<input class="bottone" name="giorno_dopo" type="submit" value=">" />';
				echo '<input class="bottone" name="cerca_data" type="submit" value="VAI" />';
				echo '</div>';
			}
			
			// Inseriamo la navigazione per nome
			echo '<div class="ricerca_nome">';
			echo '<input class="bottone" name="cerca_nome" type="submit" value="VAI" />';
			echo '<input placeholder="NOME" type="text" name="nome_ricerca" class="field" value="'.formatta_visualizzazione($nome_ricerca).'" />';
			echo '</div>';
			
			echo '<div style="clear:both"></div>';
			echo '</div>';
			
			
			// Se la ricerca non è per nome inseriamo la possibilità di aggiungere note giornaliere
			if(!isset($_POST['cerca_nome']) || !isset($_POST['no_date']) || (isset($_POST['nome_ricerca']) && $_POST['nome_ricerca'] == NULL)) {
				echo '<div class="add_com_gg">';
				echo '<input placeholder="NOTE GIORNALIERE" type="text" name="testo_nota_gg" class="field add_nota" value="" />';
				echo '<input class="bottone bott_add_nota" name="add_nota_gg" type="submit" value="AGGIUNGI" />';
				echo '<p class="reparti">';
				echo ' <input type="checkbox" id="tutti_com" name="tutti_com" value="" />';
				echo '<label class="tutti_com" for="tutti_com"> TUTTI</label>';
				echo ' <input type="checkbox" id="reception_com" name="reception_com" value="" />';
				echo '<label class="reception_com" for="reception_com"> RECEPTION</label>';
				echo ' <input type="checkbox" id="facchini_com" name="facchini_com" value="" />';
				echo '<label class="facchini_com" for="facchini_com"> FACCHINI</label>';
				echo ' <input type="checkbox" id="barca_com" name="barca_com" value="" />';
				echo '<label class="barca_com" for="barca_com"> BARCA</label>';
				echo ' <input type="checkbox" id="taxi_com" name="taxi_com" value="" />';
				echo '<label class="taxi_com" for="taxi_com"> TAXI</label>';
				echo '</p>';
				echo '</div>';
				
				echo '</div>'; // Fine menu_nav
				
				// Recuperiamo le note giornaliere per il giorno scelto
				
				$db = db_connect();
				$note_brut = $db->query('SELECT * FROM note_giornaliere WHERE data='.$_SESSION['timestamp_lista']);
				$note_gg = $note_brut->fetchAll(PDO::FETCH_ASSOC);
				$db->connection = NULL;
				
				$num_note_gg = count($note_gg);
				
				// Se c'è almeno una nota
				if($num_note_gg > 0) {
					echo '<div class="box_note_gg">';
					for($i = 0 ; $i < $num_note_gg ; $i++) {
						if($_SESSION['tipo_lista'] == 0 ||
								  ($_SESSION['tipo_lista'] == 1 && $note_gg[$i]['facchini'] == 1) ||
								  ($_SESSION['tipo_lista'] == 5 && $note_gg[$i]['barca'] == 1) ||
								  (($_SESSION['tipo_lista'] == 6 || $_SESSION['tipo_lista'] == 7) && $note_gg[$i]['taxi'] == 1) ||
								  (($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 3 || $_SESSION['tipo_lista'] == 4) && $note_gg[$i]['barca'] == 1)) {
							
							echo '<p class="nota_gg">';
							
							echo '<span class="ora_com print_hide">';
							echo date('d/m', $note_gg[$i]['data_creazione']);
							echo '</span> ';
							
							echo '<span class="reparti print_hide">';
							if($note_gg[$i]['tutti'] == 1)		echo 'TUTTI ';
							if($note_gg[$i]['reception'] == 1)	echo 'RECEP ';
							if($note_gg[$i]['facchini'] == 1)	echo 'FACC ';
							if($note_gg[$i]['barca'] == 1)		echo 'BARCA ';
							if($note_gg[$i]['taxi'] == 1)			echo 'TAXI ';
							echo '</span>';
							
							echo '<span class="testo_note">'.formatta_visualizzazione($note_gg[$i]['testo']).'</span>';
							
							echo '<a class="clx_nota_gg" href="lista_trans.php?clx_nota='.$note_gg[$i]['id'].'">CLX</a>';
							
							echo '</p>';
						}
					}
					echo '</div>';
				}
			}
			
			else echo '</div>'; // Fine menu_nav
	
			// Se non si ricerca per nome inseriamo la colonna delle liste
			if(!isset($_POST['cerca_nome']) || !isset($_POST['no_date']) || (isset($_POST['nome_ricerca']) && $_POST['nome_ricerca'] == NULL)) {
				if(isset($_GET['tipo_lista'])) $_SESSION['tipo_lista'] = $_GET['tipo_lista'];
				
				echo '<div class="box_tipo_lista">';
				
				
				echo '<p class="titolo_lista">STATO MEZZI</p>';
				stato_mezzi(-1);

				
				echo '<div style="clear:both"></div>';
				echo '</div>';
			}
			
			if($azione === 'ASK TO DELETE PRIVATE') {
				echo '<div class="form1_all form_green">';
				echo '<p class="titolo">CONFERMA ELIMINAZIONE</p>';
			
				echo '<div class="cont_op"><p class="form_txt">sei sicuro di voler eliminare la prenotazione <span>' . $nome_el . '</span> definitivamente ?</p></div>';
				
				echo '<div class="pulsanti_bottom">';
				echo '<form style="display: inline;" name="dati" action="lista_trans.php" method="post" enctype="multipart/form-data">';
				echo '<input class="bottone" type="submit" value="NO" />';
				echo '</form>';
				
				echo '<form style="display: inline;" name="dati" action="lista_trans.php" method="post" enctype="multipart/form-data">';
				echo '<input class="bottone bottone_r" type="submit" value="SI" />';
				echo '<input type="hidden" name="conferma_clx_trans" value="' . $id_pre . '" />';
				echo '</form>';
				echo '</div>';
				echo '</div>';
			}
			
			elseif($azione === 'PRIVATE DELETED') {
				echo '<div class="form1_all form_green">';
				echo '<p class="titolo">TRANSFER ELIMINATO CON SUCCESSO</p>';
				echo '</div>';
			}


			echo '<p class="data_viaggi">VIAGGI DEL '.$_SESSION['giorni_long'][date('w', $_SESSION['timestamp_lista'])].' '.date('d/m', $_SESSION['timestamp_lista']).'</p>';



			// ARRIVI


			if($_SESSION['tipo_lista'] == 0) $hide_com = ' class="print_hide"'; // Per la lista reception nascondiamo i commenti
			else 						$hide_com = '';

			// Stampiamo la lista in un modo se sono: reception, facchini o taxi
			if($_SESSION['tipo_lista'] == 0 || $_SESSION['tipo_lista'] == 1 || $_SESSION['tipo_lista'] == 5 || $_SESSION['tipo_lista'] == 6 || $_SESSION['tipo_lista'] == 7) {
				
				echo '<div class="liste_box">';
				echo '<div class="arrivi_box">';
				
				echo '<p class="title_list">';
				
				if($_SESSION['tipo_lista'] == 6) 	 echo 'ARRIV&Eacute;ES';
				elseif($_SESSION['tipo_lista'] == 7 || $_SESSION['tipo_lista'] == 5) echo 'ARRIVI';
				else 							 echo '<span class="print_hide">ARRIVI</span>';
				// Calcoliamo la data di ultima modifica e la stampiamo
				if($num_arrivi > 0 && ($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7)) {
					for($i = 0, $ultima_mod_arrivi = 0 ; $i < $num_arrivi ; $i++) {
						if($ultima_mod_arrivi < $trans_arr[$i]['ultima_mod_arr']) $ultima_mod_arrivi = $trans_arr[$i]['ultima_mod_arr'];
					}
					
					echo ' <span class="ultima_mod_lista">ULTIMA MODIFICA '.date('d/m H:i', $ultima_mod_arrivi).'</span>';
				}
				echo '</p>';
				
				
				// Se non ci sono arrivi non stampiamo la lista
				if($num_arrivi > 0) {
					echo '<table class="lista_trans">';
					
					// Stampiamo la testata degli arrivi
					echo '<tr class="head_list_trans">';
					if($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th>A</th>';
					
					// Se la lista non è per massimi la stampiamo in francese
					if($_SESSION['tipo_lista'] == 6) {
						echo '<th>NOM</th>';
						echo '<th>ARRIV&Eacute;</th>';
						echo '<th>VOL</th>';
						echo '<th>HEURE</th>';
						echo '<th>TAXI</th>';
						echo '<th>HEURE</th>';
						echo '<th>BATEAU</th>';
						echo '<th>PORT</th>';
						echo '<th>HEURE</th>';
					}
					else {
						echo '<th>NOME</th>';
						if($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th class="num_cam">#</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>ARRIVO</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>VOLO</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>ORA</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>TAXI</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>ORA</th>';
						echo '<th>PORTO</th>';
						echo '<th>BARCA</th>';
						if($_SESSION['tipo_lista'] == 1) echo '<th>ARR</th>';
						else 						echo '<th>ORA</th>';
					}
					if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th><span class="print_hide">EMAIL</span></th>';
					if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th><span class="print_hide">TEL</span></th>';
					if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th class="print_hide">OP</th>';
					if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th class="print_hide">MOD.</th>';
					echo '</tr>';
					
					for($i = 0, $id_old_trans = -1 ; $i < $num_arrivi ; $i++) {
						
						// Se siamo su un nuovo transfer
						if($id_old_trans != $trans_arr[$i]['id']) {
							$id_old_trans = $trans_arr[$i]['id'];
							
							$tot_trans++; $tot_ad += $trans_arr[$i]['pax_ad']; $tot_bam += $trans_arr[$i]['pax_bam'];
							
							echo '<tr class="riga c_trans_'.$trans_arr[$i]['stato_arr'].'">';
							if($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<td><img class="tipo_trans_img" src="layout/img/trans_'.$trans_arr[$i]['tipo_transfer'].'.png"></td>';
							echo '<td><a href="ge_trans.php?mp='.$trans_arr[$i]['id'].'" target="_blank">'.$_SESSION['stato_trans'][$trans_arr[$i]['stato_arr']].$trans_arr[$i]['pax_ad'];
							if($trans_arr[$i]['pax_bam'] > 0) echo '+'.$trans_arr[$i]['pax_bam'];
							echo ' '.formatta_visualizzazione($trans_arr[$i]['nome']).'</a></td>';
							if($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) {
								if($_SESSION['assegna_camere'] == TRUE) echo '<td><input type="text" name="'.$trans_arr[$i]['id'].'" class="field field_cam" value="'.$trans_arr[$i]['camera'].'" /></td>';
								else 								 echo '<td class="num_cam_td">'.formatta_visualizzazione($trans_arr[$i]['camera']).'</td>';
							}
							if($_SESSION['tipo_lista'] != 1) echo '<td>'.formatta_visualizzazione($trans_arr[$i]['luogo_arr']).'</td>';
							if($_SESSION['tipo_lista'] != 1) echo '<td>'.formatta_visualizzazione($trans_arr[$i]['volo_arr']).'</td>';
							if($_SESSION['tipo_lista'] != 1) {
								if($trans_arr[$i]['ora_arr'] == NULL) echo '<td class="num_cam_td"></td>';
								else echo '<td class="num_cam_td">'.date('H:i', $trans_arr[$i]['ora_arr']).'</td>';
							}
							if($_SESSION['tipo_lista'] != 1) echo '<td>'.$_SESSION['taxi'][$trans_arr[$i]['taxi_arr']].'</td>';
							if($_SESSION['tipo_lista'] != 1) {
								if($trans_arr[$i]['ora_taxi_arr'] == NULL) echo '<td class="num_cam_td"></td>';
								else echo '<td class="num_cam_td">'.date('H:i', $trans_arr[$i]['ora_taxi_arr']).'</td>';
							}
							echo '<td>'.$_SESSION['porti'][$trans_arr[$i]['porto_partenza_arr']].'/'.$_SESSION['porti'][$trans_arr[$i]['porto_arrivo_arr']].'</td>';
							echo '<td>'.$_SESSION['barche'][$trans_arr[$i]['barca_arr']].'</td>';
							if($trans_arr[$i]['ora_barca_arr'] == NULL) echo '<td class="num_cam_td"></td>';
							elseif($_SESSION['tipo_lista'] != 1) {
								echo '<td class="num_cam_td">'.date('H:i', $trans_arr[$i]['ora_barca_arr']);
								if($trans_arr[$i]['ora_barca_arr_cal'] != $trans_arr[$i]['ora_barca_arr'])
									echo '>'.date('H:i', $trans_arr[$i]['ora_barca_arr_cal']);
								echo '</td>';
							}
							else { // Per la lista facchini stampiano unicamente la data di arrivo della barca a Cavallo
								echo '<td class="num_cam_td">';
								if($trans_arr[$i]['ora_barca_arr_cal'] != $trans_arr[$i]['ora_barca_arr'])
									echo date('H:i', $trans_arr[$i]['ora_barca_arr_cal']);
								else 
									echo '!PAR '.date('H:i', $trans_arr[$i]['ora_barca_arr']);
								echo '</td>';
							}
							echo '</td>';
							if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) {
								echo '<td class="small_col">';
								if($trans_arr[$i]['email'] != '') echo '<img class="email_img display_hide" src="layout/img/email.png">';
								echo '<span class="print_hide">'.formatta_visualizzazione($trans_arr[$i]['email']).'</span>';
								echo'</td>';
							}
							if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) {
								echo '<td class="small_col">';
								if($trans_arr[$i]['num_tel'] != '') echo '<img class="email_img display_hide" src="layout/img/tel.png">';
								echo '<span class="print_hide">'.formatta_visualizzazione($trans_arr[$i]['num_tel']).'</span>';
								echo'</td>';
								
							}
							if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<td class="print_hide">'.formatta_visualizzazione($trans_arr[$i]['operatore_arr']).'</td>';
							if($_SESSION['tipo_lista'] == 0) echo '<td class="print_hide">'.date('d/m H:i', $trans_arr[$i]['ultima_mod_arr']).'</td>';
							echo '</tr>';
							
							// Se c'è un commento
							if($trans_arr[$i]['com_id'] != NULL) {
								if(($trans_arr[$i]['com_tipo_commento'] == 1 || $trans_arr[$i]['com_tipo_commento'] == 0) &&
								  ($trans_arr[$i]['com_tutti'] == 1 || $_SESSION['tipo_lista'] == 0 ||
								  ($_SESSION['tipo_lista'] == 1 && $trans_arr[$i]['com_facchini'] == 1) ||
								  ($_SESSION['tipo_lista'] == 5 && $trans_arr[$i]['com_barca'] == 1) ||
								  (($_SESSION['tipo_lista'] == 6 || $_SESSION['tipo_lista'] == 7) && $trans_arr[$i]['com_taxi'] == 1))) {
								  
									echo '<tr'.$hide_com.'><td class="td_com" colspan="'.$colspan_com[$_SESSION['tipo_lista']].'">';
									if($_SESSION['tipo_lista'] == 0) { // Mostriamo i dettagli del commento solo per la reception
										echo '<span class="ora_com">'.date('d/m|H:i', $trans_arr[$i]['com_data_creazione']).'</span> ';
										echo '<span class="reparti">';
										if($trans_arr[$i]['com_tutti'] == 1)		echo 'TUTTI ';
										if($trans_arr[$i]['com_reception'] == 1)	echo 'RECEP ';
										if($trans_arr[$i]['com_governante'] == 1)	echo 'GOV ';
										if($trans_arr[$i]['com_ristorante'] == 1)	echo 'RISTO ';
										if($trans_arr[$i]['com_facchini'] == 1)	echo 'FACC ';
										if($trans_arr[$i]['com_barca'] == 1)		echo 'BARCA ';
										if($trans_arr[$i]['com_taxi'] == 1)			echo 'TAXI ';
										if($trans_arr[$i]['com_cliente'] == 1)		echo 'CLIENTE ';
										echo '</span>';
									}
									
									echo '<span class="testo_com">'.formatta_visualizzazione($trans_arr[$i]['com_testo']).'</span>';
									
									if($_SESSION['tipo_lista'] == 0 && $trans_arr[$i]['com_operatore'] != '') echo '<span class="op_com">-'.formatta_visualizzazione($trans_arr[$i]['com_operatore']).'</span>';
								  
								  echo '</td></tr>';
								}
							}
						}
						// Se siamo sul commento di un transfer
						else {
							if(($trans_arr[$i]['com_tipo_commento'] == 1 || $trans_arr[$i]['com_tipo_commento'] == 0) &&
								  ($trans_arr[$i]['com_tutti'] == 1 || $_SESSION['tipo_lista'] == 0 ||
								  ($_SESSION['tipo_lista'] == 1 && $trans_arr[$i]['com_facchini'] == 1) ||
								  ($_SESSION['tipo_lista'] == 5 && $trans_arr[$i]['com_barca'] == 1) ||
								  (($_SESSION['tipo_lista'] == 6 || $_SESSION['tipo_lista'] == 7) && $trans_arr[$i]['com_taxi'] == 1))) {
							  
								echo '<tr'.$hide_com.'><td class="td_com" colspan="'.$colspan_com[$_SESSION['tipo_lista']].'">';
								
								if($_SESSION['tipo_lista'] == 0) { // Mostriamo i dettagli del commento solo per la reception
									echo '<span class="ora_com">'.date('d/m|H:i', $trans_arr[$i]['com_data_creazione']).'</span> ';
									echo '<span class="reparti">';
									if($trans_arr[$i]['com_tutti'] == 1)		echo 'TUTTI ';
									if($trans_arr[$i]['com_reception'] == 1)	echo 'RECEP ';
									if($trans_arr[$i]['com_governante'] == 1)	echo 'GOV ';
									if($trans_arr[$i]['com_ristorante'] == 1)	echo 'RISTO ';
									if($trans_arr[$i]['com_facchini'] == 1)	echo 'FACC ';
									if($trans_arr[$i]['com_barca'] == 1)		echo 'BARCA ';
									if($trans_arr[$i]['com_taxi'] == 1)			echo 'TAXI ';
									if($trans_arr[$i]['com_cliente'] == 1)		echo 'CLIENTE ';
									echo '</span>';
								}
								
								echo '<span class="testo_com">'.formatta_visualizzazione($trans_arr[$i]['com_testo']).'</span>';
								
								if($_SESSION['tipo_lista'] == 0 && $trans_arr[$i]['com_operatore'] != '') echo '<span class="op_com">-'.formatta_visualizzazione($trans_arr[$i]['com_operatore']).'</span>';
							  
							  echo '</td></tr>';
							}
						}
						
						
					}
					// Stampiamo i totali
					echo '<tr><td class="td_com" colspan="'.$colspan_com[$_SESSION['tipo_lista']].'">';
					echo $tot_trans.' TRANSFER - '.$tot_ad.' ADULTI, '.$tot_bam.' BAMBINI';
					echo '</td></tr>';
					$tot_trans = 0; $tot_ad = 0; $tot_bam = 0;
					
					echo '</table>';
				}
				elseif($_SESSION['tipo_lista'] == 6) echo '<p class="no_arrivi">AUCUNE</p>';
				else 							 echo '<p class="no_arrivi">NESSUN ARRIVO</p>';
				
				echo '</div>';
			}
			
			
			
			
			
			
			
			// PARTENZE

			// Stampiamo la lista in un modo se sono: reception, facchini o taxi
			if($_SESSION['tipo_lista'] == 0 || $_SESSION['tipo_lista'] == 1 || $_SESSION['tipo_lista'] == 5 || $_SESSION['tipo_lista'] == 6 || $_SESSION['tipo_lista'] == 7) {
				
				echo '<div class="arrivi_box">';
				
				echo '<p class="title_list">';
				if($_SESSION['tipo_lista'] == 6)  	 echo 'D&Eacute;PARTS';
				elseif($_SESSION['tipo_lista'] == 5 || $_SESSION['tipo_lista'] == 7) echo 'PARTENZE';
				else 							 echo '<span class="print_hide">PARTENZE</span>';
				
				// Calcoliamo la data di ultima modifica e la stampiamo
				if($num_partenze > 0 && ($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7)) {
					for($i = 0, $ultima_mod_partenze = 0 ; $i < $num_partenze ; $i++) {
						if($ultima_mod_partenze < $trans_par[$i]['ultima_mod_par']) $ultima_mod_partenze = $trans_par[$i]['ultima_mod_par'];
					}
					
					echo ' <span class="ultima_mod_lista">ULTIMA MODIFICA '.date('d/m H:i', $ultima_mod_partenze).'</span>';
				}
				echo '</p>';
				
				// Se non ci sono partenze non stampiamo la lista
				if($num_partenze > 0) {
					echo '<table class="lista_trans">';
					
					// Stampiamo la testata degli arrivi
					echo '<tr class="head_list_trans">';
					if($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th>P</th>';
					
					if($_SESSION['tipo_lista'] == 6) {
						echo '<th>NOM</th>';
						echo '<th>DEPART</th>';
						echo '<th>BATEAU</th>';
						echo '<th>HEURE</th>';
						echo '<th>TAXI</th>';
						echo '<th>HEURE</th>';
						echo '<th>ARRIV&Eacute;</th>';
						echo '<th>VOL</th>';
						echo '<th>HEURE</th>';
					}
					else {
						echo '<th>NOME</th>';
						if($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th class="num_cam">#</th>';
						echo '<th>PARTENZA</th>';
						echo '<th>BARCA</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>ORA</th>';
						else 						echo '<th>PART</th>'; // Per i facchini
						if($_SESSION['tipo_lista'] != 1) echo '<th>TAXI</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>ORA</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>ARRIVO</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>VOLO</th>';
						if($_SESSION['tipo_lista'] != 1) echo '<th>ORA</th>';
					}
					if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th><span class="print_hide">EMAIL</span></th>';
					if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th><span class="print_hide">TEL</span></th>';
					if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th class="print_hide">OP</th>';
					if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<th class="print_hide">MOD.</th>';
					echo '</tr>';
					
					for($i = 0, $id_old_trans = -1 ; $i < $num_partenze ; $i++) {
						
						// Se siamo su un nuovo transfer
						if($id_old_trans != $trans_par[$i]['id']) {
							$id_old_trans = $trans_par[$i]['id'];
							
							$tot_trans++; $tot_ad += $trans_par[$i]['pax_ad']; $tot_bam += $trans_par[$i]['pax_bam'];
							
							echo '<tr class="riga c_trans_'.$trans_par[$i]['stato_par'].'">';
							if($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<td><img class="tipo_trans_img" src="layout/img/trans_'.$trans_par[$i]['tipo_transfer'].'.png"></td>';
							echo '<td><a href="ge_trans.php?mp='.$trans_par[$i]['id'].'" target="_blank">'.$_SESSION['stato_trans'][$trans_par[$i]['stato_par']].$trans_par[$i]['pax_ad'];
							if($trans_par[$i]['pax_bam'] > 0) echo '+'.$trans_par[$i]['pax_bam'];
							echo ' '.formatta_visualizzazione($trans_par[$i]['nome']).'</a></td>';
							if($_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) {
								if($_SESSION['assegna_camere'] == TRUE) echo '<td><input type="text" name="'.$trans_par[$i]['id'].'" class="field field_cam" value="'.$trans_par[$i]['camera'].'" /></td>';
								else 								 echo '<td class="num_cam_td">'.formatta_visualizzazione($trans_par[$i]['camera']).'</td>';
							}
							echo '<td>'.$_SESSION['porti'][$trans_par[$i]['porto_partenza_par']].'/'.$_SESSION['porti'][$trans_par[$i]['porto_arrivo_par']].'</td>';
							echo '<td>'.$_SESSION['barche'][$trans_par[$i]['barca_par']].'</td>';
							if($trans_par[$i]['ora_barca_par'] == NULL) echo '<td class="num_cam_td"></td>';
							elseif($_SESSION['tipo_lista'] != 1) {
								echo '<td class="num_cam_td">'.date('H:i', $trans_par[$i]['ora_barca_par']);
								if($trans_par[$i]['ora_barca_par_cal'] != $trans_par[$i]['ora_barca_par'])
									echo '>'.date('H:i', $trans_par[$i]['ora_barca_par_cal']);
								echo '</td>';
							}
							elseif($_SESSION['tipo_lista'] == 1) {
								echo '<td class="num_cam_td">'.date('H:i', $trans_par[$i]['ora_barca_par']);
								echo '</td>';
							}
							if($_SESSION['tipo_lista'] != 1) echo '<td>'.$_SESSION['taxi'][$trans_par[$i]['taxi_par']].'</td>';
							if($_SESSION['tipo_lista'] != 1) {
								if($trans_par[$i]['ora_taxi_par'] == NULL) echo '<td class="num_cam_td"></td>';
								else 													 echo '<td class="num_cam_td">'.date('H:i', $trans_par[$i]['ora_taxi_par']).'</td>';
							}
							if($_SESSION['tipo_lista'] != 1) echo '<td>'.formatta_visualizzazione($trans_par[$i]['luogo_par']).'</td>';
							if($_SESSION['tipo_lista'] != 1) echo '<td>'.formatta_visualizzazione($trans_par[$i]['volo_par']).'</td>';
							if($_SESSION['tipo_lista'] != 1) {
								if($trans_par[$i]['ora_par'] == NULL) echo '<td class="num_cam_td"></td>';
								else echo '<td class="num_cam_td">'.date('H:i', $trans_par[$i]['ora_par']).'</td>';
							}
							echo '</td>';
							if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) {
								echo '<td class="small_col">';
								if($trans_par[$i]['email'] != '') echo '<img class="email_img display_hide" src="layout/img/email.png">';
								echo '<span class="print_hide">'.formatta_visualizzazione($trans_par[$i]['email']).'</span>';
								echo'</td>';
							}
							if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) {
								echo '<td class="small_col">';
								if($trans_par[$i]['num_tel'] != '') echo '<img class="email_img display_hide" src="layout/img/tel.png">';
								echo '<span class="print_hide">'.formatta_visualizzazione($trans_par[$i]['num_tel']).'</span>';
								echo'</td>';
								
							}
							if($_SESSION['tipo_lista'] != 1 && $_SESSION['tipo_lista'] != 5 && $_SESSION['tipo_lista'] != 6 && $_SESSION['tipo_lista'] != 7) echo '<td class="print_hide">'.formatta_visualizzazione($trans_par[$i]['operatore_par']).'</td>';
							if($_SESSION['tipo_lista'] == 0) echo '<td class="print_hide">'.date('d/m H:i', $trans_par[$i]['ultima_mod_par']).'</td>';
							echo '</tr>';
							
							// Se c'è un commento
							if($trans_par[$i]['com_id'] != NULL) {
								if(($trans_par[$i]['com_tipo_commento'] == 2 || $trans_par[$i]['com_tipo_commento'] == 0) &&
								  ($trans_par[$i]['com_tutti'] == 1 || $_SESSION['tipo_lista'] == 0 ||
								  ($_SESSION['tipo_lista'] == 1 && $trans_par[$i]['com_facchini'] == 1) ||
								  ($_SESSION['tipo_lista'] == 5 && $trans_par[$i]['com_barca'] == 1) ||
								  (($_SESSION['tipo_lista'] == 6 || $_SESSION['tipo_lista'] == 7) && $trans_par[$i]['com_taxi'] == 1))) {
								  
									echo '<tr'.$hide_com.'><td class="td_com" colspan="'.$colspan_com[$_SESSION['tipo_lista']].'">';
									if($_SESSION['tipo_lista'] == 0) { // Mostriamo i dettagli del commento solo per la reception
										echo '<span class="ora_com">'.date('d/m|H:i', $trans_par[$i]['com_data_creazione']).'</span> ';
										echo '<span class="reparti">';
										if($trans_par[$i]['com_tutti'] == 1)		echo 'TUTTI ';
										if($trans_par[$i]['com_reception'] == 1)	echo 'RECEP ';
										if($trans_par[$i]['com_facchini'] == 1)	echo 'FACC ';
										if($trans_par[$i]['com_barca'] == 1)		echo 'BARCA ';
										if($trans_par[$i]['com_taxi'] == 1)			echo 'TAXI ';
										echo '</span>';
									}
									
									echo '<span class="testo_com">'.formatta_visualizzazione($trans_par[$i]['com_testo']).'</span>';
									
									if($_SESSION['tipo_lista'] == 0 && $trans_par[$i]['com_operatore'] != '') echo '<span class="op_com">-'.formatta_visualizzazione($trans_par[$i]['com_operatore']).'</span>';
								  
								  echo '</td></tr>';
								}
							}
						}
						// Se siamo sul commento di un transfer
						else {
							if(($trans_par[$i]['com_tipo_commento'] == 2 || $trans_par[$i]['com_tipo_commento'] == 0) &&
								  ($trans_par[$i]['com_tutti'] == 1 || $_SESSION['tipo_lista'] == 0 ||
								  ($_SESSION['tipo_lista'] == 1 && $trans_par[$i]['com_facchini'] == 1) ||
								  ($_SESSION['tipo_lista'] == 5 && $trans_par[$i]['com_barca'] == 1) ||
								  (($_SESSION['tipo_lista'] == 6 || $_SESSION['tipo_lista'] == 7) && $trans_par[$i]['com_taxi'] == 1))) {
							  
								echo '<tr'.$hide_com.'><td class="td_com" colspan="'.$colspan_com[$_SESSION['tipo_lista']].'">';
								
								if($_SESSION['tipo_lista'] == 0) { // Mostriamo i dettagli del commento solo per la reception
									echo '<span class="ora_com">'.date('d/m|H:i', $trans_par[$i]['com_data_creazione']).'</span> ';
									echo '<span class="reparti">';
									if($trans_par[$i]['com_tutti'] == 1)		echo 'TUTTI ';
									if($trans_par[$i]['com_reception'] == 1)	echo 'RECEP ';
									if($trans_par[$i]['com_governante'] == 1)	echo 'GOV ';
									if($trans_par[$i]['com_ristorante'] == 1)	echo 'RISTO ';
									if($trans_par[$i]['com_facchini'] == 1)	echo 'FACC ';
									if($trans_par[$i]['com_barca'] == 1)		echo 'BARCA ';
									if($trans_par[$i]['com_taxi'] == 1)			echo 'TAXI ';
									if($trans_par[$i]['com_cliente'] == 1)		echo 'CLIENTE ';
									echo '</span>';
								}
								
								echo '<span class="testo_com">'.formatta_visualizzazione($trans_par[$i]['com_testo']).'</span>';
								
								if($_SESSION['tipo_lista'] == 0 && $trans_par[$i]['com_operatore'] != '') echo '<span class="op_com">-'.formatta_visualizzazione($trans_par[$i]['com_operatore']).'</span>';
							  
							  echo '</td></tr>';
							}
						}
					}
					
					echo '<tr><td class="td_com" colspan="'.$colspan_com[$_SESSION['tipo_lista']].'">';
					echo $tot_trans.' TRANSFER - '.$tot_ad.' ADULTI, '.$tot_bam.' BAMBINI';
					echo '</td></tr>';
					
					echo '</table>';
				}
				elseif($_SESSION['tipo_lista'] == 6) echo '<p class="no_arrivi">AUCUN</p>';
				else 							 echo '<p class="no_arrivi">NESSUNA PARTENZA</p>';
				
				echo '</div>'; // Fine arrivi_box
				echo '</div>'; // Fine liste_box
			}
			
			
			
			


			// FABIRS

			// Stampiamo la lista in un modo se sono: fabirs, fabir3 o fabir4
			if($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 3 || $_SESSION['tipo_lista'] == 4) {
				
				echo '<div class="liste_box"><div class="arrivi_box">';
				
				echo '<p class="title_list">';
				
				if($_SESSION['tipo_lista'] == 2) echo 'FABIR 3 + FABIR 4';
				elseif($_SESSION['tipo_lista'] == 3) echo 'FABIR 3';
				elseif($_SESSION['tipo_lista'] == 4) echo 'FABIR 4';
				
				// Calcoliamo la data di ultima modifica e la stampiamo
				if($num_barche > 0) {
					for($i = 0, $ultima_mod_barche = 0 ; $i < $num_barche ; $i++) {
						if($ultima_mod_barche < $lista_barche[$i]['ultima_mod']) $ultima_mod_barche = $lista_barche[$i]['ultima_mod'];
					}
					
					echo ' <span class="ultima_mod_lista">ULTIMA MODIFICA '.date('d/m H:i', $ultima_mod_barche).'</span>';
				}
				echo '</p>';
				
				// Se non ci sono partenze non stampiamo la lista
				if($num_barche > 0) {
					echo '<table class="lista_trans lista_barche">';
					
					// Stampiamo la testata degli arrivi
					echo '<tr class="head_list_trans">';
					if($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 5) echo '<th>BARCA</th>';
					echo '<th>ORARIO</th>';
					echo '<th>TRAGITTO</th>';
					echo '<th>NOMINATIVI</th>';
					echo '<th>PAX</th>';
					echo '</tr>';
					
					
					// Creiamo la lista commenti						
					// AGG. A LISTA BARCHE ID TRANS E TIPO DI TRANS PER RECUPERARE COMMENTI
					$id_per_commenti = '';
					for($i = 0 ; $i < $num_barche ; $i++) {
						if($i == 0) $id_per_commenti .= ' WHERE (barca=1 OR tutti=1) AND (';
						else $id_per_commenti .= ' OR ';
						$id_per_commenti .= '(id_transfer='.$lista_barche[$i]['id'].' AND (tipo_commento='.$lista_barche[$i]['tipo_commento'].' OR tipo_commento=0))';
					}
					if($id_per_commenti != '') $id_per_commenti .= ')';
					
					$sql_com_barche = 'SELECT commenti.*, transfer.nome AS nome FROM commenti LEFT JOIN transfer ON id_transfer=transfer.id'.$id_per_commenti;

					$db = db_connect();
					$rep_com_barche = $db->query($sql_com_barche);
					$db->connection = NULL;
		
					$com_barche = $rep_com_barche->fetchAll(PDO::FETCH_ASSOC);
					$num_com_barche = count($com_barche);

					// Creiamo la lista barche
					for($i = 0, $num_righe = 0, $com_now = 0 ; $i < $num_barche ; $i++) {
						// Se siamo alla prima riga
						if($i == 0) {
							$riga[$num_righe]['barca'] = $lista_barche[$i]['barca'];
							$riga[$num_righe]['ora'] = $lista_barche[$i]['ora_barca'];
							$riga[$num_righe]['nominativo'] = '<img class="tipo_trans_img_bar" src="layout/img/trans_'.$lista_barche[$i]['tipo_transfer'].'.png"> ';
							$riga[$num_righe]['nominativo'] .= $_SESSION['stato_trans'][$lista_barche[$i]['stato']].' ';
							$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['pax_ad'];
							if($lista_barche[$i]['pax_bam'] > 0) $riga[$num_righe]['nominativo'] .= '+'.$lista_barche[$i]['pax_bam'];
							$riga[$num_righe]['nominativo'] .= ' <a href="ge_trans.php?mp='.$lista_barche[$i]['id'].'" target="_blank">'.$lista_barche[$i]['nominativo'].'</a>';
							$riga[$num_righe]['tragitto'] = $lista_barche[$i]['tragitto'];
							$riga[$num_righe]['pax_totali'] = $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
							$riga[$num_righe]['link'] = $lista_barche[$i]['link'];
							$riga[$num_righe]['commenti'] = '';
							
							// Se ci sono commenti da inserire li inseriamo
							for($j = 0 ; $j < $num_com_barche ; $j++) {
								if($lista_barche[$i]['id'] == $com_barche[$j]['id_transfer']) {
									$riga[$num_righe]['commenti'] .= '<tr><td class="td_com" colspan="'.$colspan_com[$_SESSION['tipo_lista']].'">';
									$riga[$num_righe]['commenti'] .= '<span class="nome_x_barche">'.$lista_barche[$i]['nominativo'].'</span> <span class="testo_com">'.formatta_visualizzazione($com_barche[$j]['testo']).'</span>';
									$riga[$num_righe]['commenti'] .= '</td></tr>';
								}
							}
						}
						// Se i viaggi sono sulla stessa barca per lo stesso posto alla stessa ora
						elseif($riga[$num_righe]['ora'] == $lista_barche[$i]['ora_barca'] && $riga[$num_righe]['barca'] == $lista_barche[$i]['barca']
							 && $riga[$num_righe]['tragitto'] == $lista_barche[$i]['tragitto']) {
							
							$riga[$num_righe]['nominativo'] .= ' <span class="sinbol_piu">+</span> ';
							$riga[$num_righe]['nominativo'] .= '<img class="tipo_trans_img_bar" src="layout/img/trans_'.$lista_barche[$i]['tipo_transfer'].'.png"> ';
							$riga[$num_righe]['nominativo'] .= $_SESSION['stato_trans'][$lista_barche[$i]['stato']].' ';
							$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['pax_ad'];
							if($lista_barche[$i]['pax_bam'] > 0) $riga[$num_righe]['nominativo'] .= '+'.$lista_barche[$i]['pax_bam'];
							$riga[$num_righe]['nominativo'] .= ' <a href="ge_trans.php?mp='.$lista_barche[$i]['id'].'" target="_blank">'.$lista_barche[$i]['nominativo'].'</a>';
							$riga[$num_righe]['pax_totali'] += $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
							
							
							// Se ci sono commenti da inserire li inseriamo
							for($j = 0 ; $j < $num_com_barche ; $j++) {
								if($lista_barche[$i]['id'] == $com_barche[$j]['id_transfer']) {
									$riga[$num_righe]['commenti'] .= '<tr><td class="td_com" colspan="'.$colspan_com[$_SESSION['tipo_lista']].'">';
									$riga[$num_righe]['commenti'] .= '<span class="nome_x_barche">'.$lista_barche[$i]['nominativo'].'</span> <span class="testo_com">'.formatta_visualizzazione($com_barche[$j]['testo']).'</span>';
									$riga[$num_righe]['commenti'] .= '</td></tr>';
								}
							}
						}
						
						// Se bisogna creare un'altra riga per un viaggio diverso
						else {
							$num_righe++;
							
							$riga[$num_righe]['barca'] = $lista_barche[$i]['barca'];
							$riga[$num_righe]['ora'] = $lista_barche[$i]['ora_barca'];
							$riga[$num_righe]['nominativo'] = '<img class="tipo_trans_img_bar" src="layout/img/trans_'.$lista_barche[$i]['tipo_transfer'].'.png"> ';
							$riga[$num_righe]['nominativo'] .= $_SESSION['stato_trans'][$lista_barche[$i]['stato']].' ';
							$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['pax_ad'];
							if($lista_barche[$i]['pax_bam'] > 0) $riga[$num_righe]['nominativo'] .= '+'.$lista_barche[$i]['pax_bam'];
							$riga[$num_righe]['nominativo'] .= ' <a href="ge_trans.php?mp='.$lista_barche[$i]['id'].'" target="_blank">'.$lista_barche[$i]['nominativo'].'</a>';
							$riga[$num_righe]['tragitto'] = $lista_barche[$i]['tragitto'];
							$riga[$num_righe]['pax_totali'] = $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
							$riga[$num_righe]['link'] = $lista_barche[$i]['link'];
							$riga[$num_righe]['commenti'] = '';
							
							// Se ci sono commenti da inserire li inseriamo
							for($j = 0 ; $j < $num_com_barche ; $j++) {
								if($lista_barche[$i]['id'] == $com_barche[$j]['id_transfer']) {
									$riga[$num_righe]['commenti'] .= '<tr><td class="td_com" colspan="'.$colspan_com[$_SESSION['tipo_lista']].'">';
									$riga[$num_righe]['commenti'] .= '<span class="nome_x_barche">'.$lista_barche[$i]['nominativo'].'</span> <span class="testo_com">'.formatta_visualizzazione($com_barche[$j]['testo']).'</span>';
									$riga[$num_righe]['commenti'] .= '</td></tr>';
								}
							}
						}
					}
					if($i > 0) $num_righe++;
					
					for($i = 0 ; $i < $num_righe ; $i++) {
						echo '<tr class="riga_barca">';
						if($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 5) echo '<td>'.$riga[$i]['barca'].'</td>';
						echo '<td style="text-align: center;"><a href="ge_trans.php?'.$riga[$i]['link'].'">'.date('H:i', $riga[$i]['ora']).'</a></td>';
						echo '<td>'.$riga[$i]['tragitto'].'</td>';
						echo '<td class="nominativi_barche">'.$riga[$i]['nominativo'].'</td>';
						echo '<td>'.$riga[$i]['pax_totali'].'</td>';
						echo '</tr>';
						if(isset($riga[$i]['commenti'])) echo $riga[$i]['commenti'];
					}
					
					echo '</table>';
				}
				else echo '<p class="no_arrivi">NESSUN VIAGGIO</p>';
				
				echo '</div>'; // Fine arrivi_box
				echo '</div>'; // Fine liste_box
			}


















			
			
			echo '</div>';
			echo '</form>';
			
		
		?></div>
	</body>
</html><?php
} // Fine dello skip per il backup
} // Fin de "Si l'admin s'est bien identifié"

else { // Si pass ou pseudo n'existent pas on renvoie à la page de login administrateur
	header('Location: index.php');
}
?>