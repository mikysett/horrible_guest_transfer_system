<?php
session_start(); // Si lancia la sezione
require('funzioni_admin.php');
require('funzioni_ge_trans.php');

if(isset($_SESSION['tipo_user']) && $_SESSION['tipo_user'] == 0) { // Si verifica che sia l'amministratore

	$azione = '';
	$stampa_problemi = '';
	$pre_update = 'UPDATE prenotazioni A inner join(';
	
	$pagina = '<div class="menu_top"><a href="ge_trans.php">NUOVO TRANSFER</a></div>';

	$db = db_connect();
	
		// Variabile che sta ad indicare che si agisce su una prenotazione già esistente
		$info_pre['id'] = '';
		
		$info_pre['tipo_transfer'] 		 = 0;
		$info_pre['titolo'] 					 = 0;
		$info_pre['nome'] 					 = NULL;
		$info_pre['pax_ad'] 					 = 2;
		$info_pre['pax_bam'] 				 = NULL;
		$info_pre['camera'] 					 = NULL;
		$info_pre['lingua'] 					 = 0;
		
		$info_pre['auto_remind'] 			 = 1;
		$info_pre['modificabile'] 			 = 10;
		
		$info_pre['num_tel'] 				 = NULL;
		$info_pre['num_tel_sec'] 			 = NULL;
		$info_pre['email'] 					 = NULL;
		$info_pre['email_sec'] 				 = NULL;
		
		$info_pre['data_arr'] 				 = NULL;
		$info_pre['ora_arr'] 				 = NULL;
		$info_pre['luogo_arr'] 				 = '';
		$info_pre['volo_arr']				 = NULL;
		$info_pre['taxi_arr']				 = NULL;
		$info_pre['ora_taxi_arr']			 = NULL;
		$info_pre['porto_partenza_arr']	 = 0;
		$info_pre['barca_arr']				 = 0;
		$info_pre['porto_arrivo_arr']		 = 1;
		$info_pre['ora_barca_arr']			 = NULL;
		$info_pre['stato_arr']				 = 1;
		$info_pre['operatore_arr']			 = NULL;
		$info_pre['ultima_mod_arr']		 = NULL;
		
		$info_pre['data_par'] 				 = NULL;
		$info_pre['ora_par'] 				 = NULL;
		$info_pre['luogo_par'] 				 = NULL;
		$info_pre['volo_par']				 = NULL;
		$info_pre['taxi_par']				 = 0;
		$info_pre['ora_taxi_par']			 = NULL;
		$info_pre['porto_partenza_par']	 = 1;
		$info_pre['barca_par']				 = 0;
		$info_pre['porto_arrivo_par']		 = 0;
		$info_pre['ora_barca_par']			 = NULL;
		$info_pre['stato_par']				 = 1;
		$info_pre['operatore_par']			 = NULL;
		$info_pre['ultima_mod_par']		 = NULL;
	
	if(isset($_POST['modifica']) || isset($_POST['duplica']) || isset($_GET['elimina']) || isset($_GET['mp'])) {
		$modifica_pre = TRUE;
	}
	else 		$modifica_pre = FALSE;

	// Se la prenotazione va modificata
	if(isset($_GET['mp'])) {
		$prenotazione_sel = intval($_GET['mp']);
	
		// Prendiamo i dati della prenotazione
		$reponse = $db->query('SELECT * FROM transfer WHERE id='.$prenotazione_sel);

		$pre_spe = $reponse->fetchAll(PDO::FETCH_ASSOC);

		// Variabile che sta ad indicare che si agisce su una prenotazione già esistente
		$info_pre['id'] = &$pre_spe[0]['id'];
		
		$info_pre['tipo_transfer'] 		 = &$pre_spe[0]['tipo_transfer'];
		$info_pre['titolo'] 					 = &$pre_spe[0]['titolo'];
		$info_pre['nome'] 					 = formatta_visualizzazione($pre_spe[0]['nome']);
		$info_pre['pax_ad'] 					 = &$pre_spe[0]['pax_ad'];
		$info_pre['pax_bam'] 				 = &$pre_spe[0]['pax_bam'];
		$info_pre['camera'] 					 = &$pre_spe[0]['camera'];
		$info_pre['lingua'] 					 = &$pre_spe[0]['lingua'];
		
		$info_pre['auto_remind'] 			 = &$pre_spe[0]['auto_remind'];
		$info_pre['modificabile'] 			 = &$pre_spe[0]['modificabile'];
		
		$info_pre['num_tel'] 				 = formatta_visualizzazione($pre_spe[0]['num_tel']);
		$info_pre['num_tel_sec'] 			 = formatta_visualizzazione($pre_spe[0]['num_tel_sec']);
		$info_pre['email'] 					 = formatta_visualizzazione($pre_spe[0]['email']);
		$info_pre['email_sec'] 				 = formatta_visualizzazione($pre_spe[0]['email_sec']);
		
		$info_pre['data_arr'] 				 = &$pre_spe[0]['data_arr'];
		$info_pre['ora_arr'] 				 = &$pre_spe[0]['ora_arr'];
		$info_pre['luogo_arr'] 				 = formatta_visualizzazione($pre_spe[0]['luogo_arr']);
		$info_pre['volo_arr']				 = formatta_visualizzazione($pre_spe[0]['volo_arr']);
		$info_pre['ora_taxi_arr']			 = &$pre_spe[0]['ora_taxi_arr'];
		$info_pre['taxi_arr']				 = formatta_visualizzazione($pre_spe[0]['taxi_arr']);
		$info_pre['porto_partenza_arr']	 = formatta_visualizzazione($pre_spe[0]['porto_partenza_arr']);
		$info_pre['barca_arr']				 = formatta_visualizzazione($pre_spe[0]['barca_arr']);
		$info_pre['porto_arrivo_arr']		 = formatta_visualizzazione($pre_spe[0]['porto_arrivo_arr']);
		$info_pre['ora_barca_arr']			 = &$pre_spe[0]['ora_barca_arr'];
		$info_pre['stato_arr']				 = &$pre_spe[0]['stato_arr'];
		$info_pre['operatore_arr']			 = formatta_visualizzazione($pre_spe[0]['operatore_arr']);
		$info_pre['ultima_mod_arr']		 = &$pre_spe[0]['ultima_mod_arr'];
		
		$info_pre['data_par'] 				 = &$pre_spe[0]['data_par'];
		if($info_pre['data_par'] == 0)	 $info_pre['data_par'] = NULL;
		
		$info_pre['ora_par'] 				 = &$pre_spe[0]['ora_par'];
		$info_pre['luogo_par'] 				 = formatta_visualizzazione($pre_spe[0]['luogo_par']);
		$info_pre['volo_par']				 = formatta_visualizzazione($pre_spe[0]['volo_par']);
		$info_pre['taxi_par']				 = formatta_visualizzazione($pre_spe[0]['taxi_par']);
		$info_pre['ora_taxi_par']			 = &$pre_spe[0]['ora_taxi_par'];
		$info_pre['porto_partenza_par']	 = formatta_visualizzazione($pre_spe[0]['porto_partenza_par']);
		$info_pre['barca_par']				 = formatta_visualizzazione($pre_spe[0]['barca_par']);
		$info_pre['porto_arrivo_par']		 = formatta_visualizzazione($pre_spe[0]['porto_arrivo_par']);
		$info_pre['ora_barca_par']			 = &$pre_spe[0]['ora_barca_par'];
		$info_pre['stato_par']				 = &$pre_spe[0]['stato_par'];
		$info_pre['operatore_par']			 = formatta_visualizzazione($pre_spe[0]['operatore_par']);
		$info_pre['ultima_mod_par']		 = &$pre_spe[0]['ultima_mod_par'];
		$info_pre['data_creazione']		 = &$pre_spe[0]['data_creazione'];
	}

	// Se il form è stato compilato e inviato
	elseif(isset($_POST['nome'])) {
		if(isset($_POST['id_prenotazione']))
					 $info_pre['id'] = intval($_POST['id_prenotazione']);
		else		 $info_pre['id'] = NULL;		
		
		$info_pre['tipo_transfer'] 		 = &$_POST['tipo_transfer'];
		$info_pre['titolo'] 					 = &$_POST['titolo'];
		$info_pre['nome'] 					 = formatta_salvataggio_sensitive($_POST['nome']);
		$info_pre['pax_ad'] 					 = &$_POST['pax_ad'];
		$info_pre['pax_bam'] 				 = &$_POST['pax_bam'];
		$info_pre['camera'] 					 = formatta_salvataggio($_POST['camera']);
		$info_pre['lingua'] 					 = &$_POST['lingua'];
		
		$info_pre['auto_remind'] 			 = &$_POST['auto_remind'];
		$info_pre['modificabile'] 			 = &$_POST['modificabile'];
		if($info_pre['modificabile'] == 1) $info_pre['modificabile'] = intval($_POST['modificabile_gg']);
		
		$info_pre['num_tel'] 				 = formatta_salvataggio_nobreak($_POST['num_tel']);
		$info_pre['num_tel_sec'] 			 = formatta_salvataggio_nobreak($_POST['num_tel_sec']);
		$info_pre['email'] 					 = formatta_salvataggio_nobreak($_POST['email']);
		$info_pre['email_sec'] 				 = formatta_salvataggio_nobreak($_POST['email_sec']);
		
		if($_POST['data_arr'] != '') 		 $info_pre['data_arr'] = controllo_data($_POST['data_arr']);
		else 								 		 $info_pre['data_arr'] = NULL;
		
		if($_POST['ora_hh_arr'] != '' && $_POST['ora_mm_arr'] != '') $info_pre['ora_arr'] = controllo_data_ora($_POST['data_arr'], $_POST['ora_hh_arr'].':'.$_POST['ora_mm_arr']);
		else 										 $info_pre['ora_arr'] = NULL;
		
		$info_pre['luogo_arr'] 				 = formatta_salvataggio($_POST['luogo_arr']);
		$info_pre['volo_arr']				 = formatta_salvataggio($_POST['volo_arr']);
		$info_pre['taxi_arr']				 = $_POST['taxi_arr'];
		
		if($_POST['ora_taxi_hh_arr'] != '' && $_POST['ora_taxi_mm_arr'] != '')	 $info_pre['ora_taxi_arr'] = controllo_data_ora($_POST['data_arr'], $_POST['ora_taxi_hh_arr'].':'.$_POST['ora_taxi_mm_arr']);
		else 										 $info_pre['ora_taxi_arr'] = NULL;
		
		$info_pre['porto_partenza_arr']	 = $_POST['porto_partenza_arr'];
		$info_pre['barca_arr']				 = $_POST['barca_arr'];
		$info_pre['porto_arrivo_arr']		 = $_POST['porto_arrivo_arr'];
		$info_pre['ora_barca_arr']			 = controllo_data_ora($_POST['data_arr'], $_POST['ora_barca_hh_arr'].':'.$_POST['ora_barca_mm_arr']);
		if($_POST['ora_barca_hh_arr'] != '' && $_POST['ora_barca_mm_arr'] != '') $info_pre['ora_barca_arr'] = controllo_data_ora($_POST['data_arr'], $_POST['ora_barca_hh_arr'].':'.$_POST['ora_barca_mm_arr']);
		else 										 $info_pre['ora_barca_arr'] = NULL;
		// Calcoliamo l'orario di arrivo della barca
		if($info_pre['ora_barca_arr'] != NULL) {
			$info_pre['ora_barca_arr_cal'] = orario_arrivo($info_pre['porto_partenza_arr'], $info_pre['porto_arrivo_arr'], $info_pre['ora_barca_arr'], $info_pre['barca_arr']);
		}
		else $info_pre['ora_barca_arr_cal'] = NULL;
		
		$info_pre['stato_arr']				 = &$_POST['stato_arr'];
		$info_pre['operatore_arr']			 = formatta_salvataggio($_POST['operatore_arr']);
		$info_pre['ultima_mod_arr']		 = &$_POST['ultima_mod_arr'];
		
		if($_POST['data_par'] != '') 		 $info_pre['data_par'] = controllo_data($_POST['data_par']);
		else 								 		 $info_pre['data_par'] = NULL;

		if($_POST['ora_hh_par'] != '' && $_POST['ora_mm_par'] != '') $info_pre['ora_par'] = controllo_data_ora($_POST['data_par'], $_POST['ora_hh_par'].':'.$_POST['ora_mm_par']);
		else 										 $info_pre['ora_par'] = NULL;
		
		$info_pre['luogo_par'] 				 = formatta_salvataggio($_POST['luogo_par']);
		$info_pre['volo_par']				 = formatta_salvataggio($_POST['volo_par']);
		$info_pre['taxi_par']				 = $_POST['taxi_par'];

		if($_POST['ora_taxi_hh_par'] != '' && $_POST['ora_taxi_mm_par'] != '')	 $info_pre['ora_taxi_par'] = controllo_data_ora($_POST['data_par'], $_POST['ora_taxi_hh_par'].':'.$_POST['ora_taxi_mm_par']);
		else 										 $info_pre['ora_taxi_par'] = NULL;

		$info_pre['porto_partenza_par']	 = $_POST['porto_partenza_par'];
		$info_pre['barca_par']				 = $_POST['barca_par'];
		$info_pre['porto_arrivo_par']		 = $_POST['porto_arrivo_par'];
		if($_POST['ora_barca_hh_par'] != '' && $_POST['ora_barca_mm_par'] != '') $info_pre['ora_barca_par'] = controllo_data_ora($_POST['data_par'], $_POST['ora_barca_hh_par'].':'.$_POST['ora_barca_mm_par']);
		else 										 $info_pre['ora_barca_par'] = NULL;
		// Calcoliamo l'orario di arrivo della barca
		if($info_pre['ora_barca_par'] != NULL) {
			$info_pre['ora_barca_par_cal'] = orario_arrivo($info_pre['porto_partenza_par'], $info_pre['porto_arrivo_par'], $info_pre['ora_barca_par'], $info_pre['barca_par']);
		}
		else $info_pre['ora_barca_par_cal'] = NULL;
		
		$info_pre['stato_par']				 = &$_POST['stato_par'];
		$info_pre['operatore_par']			 = formatta_salvataggio($_POST['operatore_par']);
		$info_pre['ultima_mod_par']		 = &$_POST['ultima_mod_par'];
		
		
		$info_pre['data_creazione']		 = &$_POST['data_creazione'];
	
		// Se la prenotazione va modificata
		if($info_pre['id'] != NULL && !isset($_POST['duplica'])) {
			$azione = 'RESERVATION MODIFIED';
			// Verifichiamo se bisogna aggiornare la data di ultima modifica del transfer
			verifica_modifica($info_pre, 'arr');
		}
		
		// Se la prenotazione va creata
		else {
			// Inseriamo come date di ultima modifica la data attuale
			$info_pre['ultima_mod_arr'] = time();
			$info_pre['ultima_mod_par'] = $info_pre['ultima_mod_arr'];
			
			$info_pre['data_creazione'] = $info_pre['ultima_mod_arr'];
			
			// Inseriamo una nuova voce nel database prenotazioni per ottenere l'id
			try {
			 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->query('INSERT INTO transfer (data_creazione) VALUES ('.$info_pre['data_creazione'].')');
			}
			catch(Exception $e) {
				echo 'Exception -> ';
			 	var_dump($e->getMessage());
			}
			
			// Recuperiamo l'id della prenotazione
			try {
			 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$request = $db->query('SELECT id FROM transfer ORDER BY id DESC LIMIT 0,1');
			}
			catch(Exception $e) {
				echo 'Exception -> ';
			 	var_dump($e->getMessage());
			}
		
			$info_id = $request->fetch(PDO::FETCH_ASSOC);
			$info_pre['id'] = $info_id['id'];
			
			$azione = 'RESERVATION ADDED';
		}
	}
	
	if(isset($_GET['tipo'])) {
		$gg = date('d', $_GET['ora']);
		$mm = date('n', $_GET['ora']);
		$aa = date('Y', $_GET['ora']);
		$data = mktime(0, 0, 0, $mm, $gg, $aa);
		
		if($_GET['tipo'] == 'arr') {
			$info_pre['data_arr'] 				 = $data;
			$info_pre['porto_partenza_arr']	 = $_GET['da'];
			$info_pre['barca_arr']				 = $_GET['barca'];
			$info_pre['porto_arrivo_arr']		 = $_GET['a'];
			$info_pre['ora_barca_arr']			 = $_GET['ora'];
			$info_pre['stato_arr']				 = 3;
		}
		elseif($_GET['tipo'] == 'par') {
			$info_pre['data_par'] 				 = $data;
			$info_pre['porto_partenza_par']	 = $_GET['da'];
			$info_pre['barca_par']				 = $_GET['barca'];
			$info_pre['porto_arrivo_par']		 = $_GET['a'];
			$info_pre['ora_barca_par']			 = $_GET['ora'];
			$info_pre['stato_par']				 = 3;
		}
	}
	
	
	// Se la prenotazione va modificata gestiamo i commenti
	if($modifica_pre == TRUE) {
			
		// Aggiorniamo tutti i dati necessari alla colonna di sinistra
		if($azione == 'RESERVATION ADDED' || $azione == 'RESERVATION MODIFIED') {
			
			$info_pre['link'] = md5($info_pre['id']);
			
			$sql_update =  'UPDATE transfer SET'
			.' tipo_transfer='.$info_pre['tipo_transfer'].','
			.' link=\''.$info_pre['link'].'\','
			.' titolo='.$info_pre['titolo'].','
			.' nome=\''.$info_pre['nome'].'\',';
			
			if($info_pre['pax_ad'] != NULL)  $sql_update .= ' pax_ad='.$info_pre['pax_ad'].',';
			else 										$sql_update .= ' pax_ad=0,';
			if($info_pre['pax_bam'] != NULL) $sql_update .= ' pax_bam='.$info_pre['pax_bam'].',';
			else 										$sql_update .= ' pax_bam=0,';
 $sql_update .= ' camera=\''.$info_pre['camera'].'\','
 					.' lingua='.$info_pre['lingua'].','
 					.' auto_remind='.$info_pre['auto_remind'].','
 					.' modificabile='.$info_pre['modificabile'].','
 					.' num_tel=\''.$info_pre['num_tel'].'\','
 					.' num_tel_sec=\''.$info_pre['num_tel_sec'].'\','
					.' email=\''.$info_pre['email'].'\','
					.' email_sec=\''.$info_pre['email_sec'].'\',';
			
			if($info_pre['data_arr'] != NULL) $sql_update .= ' data_arr='.$info_pre['data_arr'].',';
			else 										 $sql_update .= ' data_arr=NULL,';
			if($info_pre['ora_arr'] != NULL) $sql_update .= ' ora_arr='.$info_pre['ora_arr'].',';
			else 										$sql_update .= ' ora_arr=NULL,';
 $sql_update .= ' luogo_arr=\''.$info_pre['luogo_arr'].'\','
			.' volo_arr=\''.$info_pre['volo_arr'].'\','
			.' taxi_arr='.$info_pre['taxi_arr'].',';
			if($info_pre['ora_taxi_arr'] != NULL) $sql_update .= ' ora_taxi_arr='.$info_pre['ora_taxi_arr'].',';
			else 											  $sql_update .= ' ora_taxi_arr=NULL,';
 $sql_update .= ' porto_partenza_arr='.$info_pre['porto_partenza_arr'].','
			.' barca_arr='.$info_pre['barca_arr'].','
			.' porto_arrivo_arr='.$info_pre['porto_arrivo_arr'].',';
			if($info_pre['ora_barca_arr'] != NULL) $sql_update .= ' ora_barca_arr='.$info_pre['ora_barca_arr'].',';
			else 												$sql_update .= ' ora_barca_arr=NULL,';
			if($info_pre['ora_barca_arr_cal'] != NULL) $sql_update .= ' ora_barca_arr_cal='.$info_pre['ora_barca_arr_cal'].',';
			else 													 $sql_update .= ' ora_barca_arr_cal=NULL,';
 $sql_update .= ' stato_arr='.$info_pre['stato_arr'].','
			.' operatore_arr=\''.$info_pre['operatore_arr'].'\',';
			
			if($info_pre['ultima_mod_arr'] != NULL) $sql_update .= ' ultima_mod_arr='.$info_pre['ultima_mod_arr'].',';
			
			if($info_pre['data_par'] != NULL) $sql_update .= ' data_par='.$info_pre['data_par'].',';
			else 										 $sql_update .= ' data_par=NULL,';
			if($info_pre['ora_par'] != NULL) $sql_update .= ' ora_par='.$info_pre['ora_par'].',';
			else 										$sql_update .= ' ora_par=NULL,';
 $sql_update .= ' luogo_par=\''.$info_pre['luogo_par'].'\','
			.' volo_par=\''.$info_pre['volo_par'].'\','
			.' taxi_par='.$info_pre['taxi_par'].',';
			if($info_pre['ora_taxi_par'] != NULL) $sql_update .= ' ora_taxi_par='.$info_pre['ora_taxi_par'].',';
			else 											  $sql_update .= ' ora_taxi_par=NULL,';
 $sql_update .= ' porto_partenza_par='.$info_pre['porto_partenza_par'].','
			.' barca_par='.$info_pre['barca_par'].','
			.' porto_arrivo_par='.$info_pre['porto_arrivo_par'].',';
			if($info_pre['ora_barca_par'] != NULL) $sql_update .= ' ora_barca_par='.$info_pre['ora_barca_par'].',';
			else 												$sql_update .= ' ora_barca_par=NULL,';
			if($info_pre['ora_barca_par_cal'] != NULL) $sql_update .= ' ora_barca_par_cal='.$info_pre['ora_barca_par_cal'].',';
			else 													 $sql_update .= ' ora_barca_par_cal=NULL,';
 $sql_update .= ' stato_par='.$info_pre['stato_par'].','
			.' operatore_par=\''.$info_pre['operatore_par'].'\'';
			
			if($info_pre['ultima_mod_par'] != NULL) $sql_update .= ', ultima_mod_par='.$info_pre['ultima_mod_par'];
			
			$sql_update .= ' WHERE id='.$info_pre['id'].';';
			try {
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->query($sql_update);
			}
			catch(Exception $e) {
				echo 'Exception -> ';
				var_dump($e->getMessage());
			}
		}

		// Gestione dei commenti
		$data_adesso = time();
		
		// Se ci sono commenti da eliminare li eliminiamo (supporta massimo 20 commenti)
		for($i = 0 ; $i < 20 ; $i++) {
			if(isset($_POST['clx_com'.$i])) {
				$db = db_connect();
				$db->query('DELETE FROM commenti WHERE id='.$_POST['clx_com'.$i]);
				$db->connection = NULL;
				// Aggiorniamo data di ultima modifica trans !!!! NON IMPLEMENTATO
			}
		}

		// Se ci sono commenti da aggiungere li aggiungiamo
		for($i = 0 ; $i < 3 ; $i++) {
			if(isset($_POST['testo_com'.$i])) {
				// Se ci sono commenti a scelta rapida li aggiungiamo
				$num_com_rapidi = 4;
				$reparto_com_rapid = array(1, 1, 1, 1);
				
				for($j = 0 ; $j < $num_com_rapidi ; $j++) {
					if(isset($_POST[$i.'rapid_com'.$j])) {
						$db = db_connect();
						try {
						 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
							$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
						$db->query('INSERT INTO commenti (id_transfer,tipo_commento,testo,data_creazione,reception) VALUES ('.$info_pre['id'].','.$i.',"'.formatta_salvataggio($_POST[$i.'rapid_com'.$j]).'",'.$data_adesso.',1)');
						}
						catch(Exception $e) {
							echo 'Exception -> ';
						 	var_dump($e->getMessage());
						}
						$db->connection = NULL;
						
						// Aggiorniamo data di ultima modifica trans
						if($i == 0) {
							$info_pre['ultima_mod_arr'] = $data_adesso;
							$info_pre['ultima_mod_par'] = $data_adesso;
						}
						elseif($i == 1) {
							$info_pre['ultima_mod_arr'] = $data_adesso;
						}
						elseif($i == 2) {
							$info_pre['ultima_mod_par'] = $data_adesso;
						}
					}
					
				}
				
				// Se ci sono commenti normali li aggiungiamo
				if($_POST['testo_com'.$i] != '') {
					$reparti = '';
					$val_reparti = '';
					$num_reparti = 0;
					
					if(isset($_POST['tutti_com'.$i]))		 { $reparti .= ', tutti'; $val_reparti .= ',1 '; }
					if(isset($_POST['reception_com'.$i]))	 { $reparti .= ', reception'; $val_reparti .= ',1 '; }
					if(isset($_POST['governante_com'.$i]))	 { $reparti .= ', governante'; $val_reparti .= ',1 '; }
					if(isset($_POST['ristorante_com'.$i]))	 { $reparti .= ', ristorante'; $val_reparti .= ',1 '; }
					if(isset($_POST['facchini_com'.$i]))	 { $reparti .= ', facchini'; $val_reparti .= ',1 '; }
					if(isset($_POST['barca_com'.$i]))		 { $reparti .= ', barca'; $val_reparti .= ',1 '; }
					if(isset($_POST['taxi_com'.$i]))			 { $reparti .= ', taxi'; $val_reparti .= ',1 '; }
					if(isset($_POST['cliente_com'.$i]))		 { $reparti .= ', cliente'; $val_reparti .= ',1 '; }

					$db = db_connect();
					try {
					 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
					$db->query('INSERT INTO commenti (id_transfer,tipo_commento,testo,operatore,data_creazione'.$reparti.') VALUES ('.$info_pre['id'].','.$i.',"'.formatta_salvataggio_sensitive($_POST['testo_com'.$i]).'","'.formatta_salvataggio($_POST['operatore_com'.$i]).'",'.$data_adesso.$val_reparti.')');
					}
					catch(Exception $e) {
						echo 'Exception -> ';
					 	var_dump($e->getMessage());
					}
					$db->connection = NULL;
					
					// Aggiorniamo data di ultima modifica trans
					if($i == 0) {
						$info_pre['ultima_mod_arr'] = $data_adesso;
						$info_pre['ultima_mod_par'] = $data_adesso;
					}
					elseif($i == 1) {
						$info_pre['ultima_mod_arr'] = $data_adesso;
					}
					elseif($i == 2) {
						$info_pre['ultima_mod_par'] = $data_adesso;
					}
				}
			}
		}
		
		
		// Recuperiamo i dati dei commenti
		$db = db_connect();
		try {
		 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$com_brut = $db->query('SELECT * FROM commenti WHERE id_transfer='.$info_pre['id'].' ORDER BY tipo_commento');
		}
		catch(Exception $e) {
		echo 'Exception -> ';
		 	var_dump($e->getMessage());
		}
		$info_com = $com_brut->fetchAll(PDO::FETCH_ASSOC);
		$num_com = count($info_com);
		
	}
	
	$db->connection = NULL;
	
	// Formattiamo i possibili problemi
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head><?php
	echo '<title>';
	if(isset($_GET['mp']) || isset($_POST['nome']))  echo formatta_visualizzazione($info_pre['nome']) . ' - MODIFICA TRANSFER';
	else 							echo 'NUOVO TRANSFER';
	echo '</title>';
	header_standard();
	echo '<link rel="stylesheet" href="layout/ge_trans.css" type="text/css" />';
?></head>
<body style="min-height:750px"><?php
		menu_top($pagina); ?>

		<div id="corpo_a"><?php
		
		// Inizio formulario
		echo '<form name="dati" action="ge_trans.php" method="post" enctype="multipart/form-data">';
		
		echo '<div class="colonna">';
		
		if($azione != '' || $stampa_problemi != '') {
			
			echo '<div class="form1_650 form_ge_trans';
			
			if($azione == 'RESERVATION ADDED' || $azione == 'RESERVATION MODIFIED') echo  ' form_green';
			else	 echo  ' form_red';
			echo '">';
			
			
			switch($azione) {
				case 'RESERVATION ADDED':
				$titolo_operazione = '<p class="titolo">TRANSFER AGGIUNTO</p>';
				$contenuto_operazione = '';
				break;
	
				case 'RESERVATION MODIFIED':
				$titolo_operazione = '<p class="titolo">TRANSFER MODIFICATO</p>';
				$contenuto_operazione = '';
				break;
	
				case 'NOT FOUND':
				$titolo_operazione = '<p class="titolo">TRANSFER NON TROVATO</p>';
				$contenuto_operazione = '';
				break;
	
				case '':
				$titolo_operazione = '<p class="titolo">PROBLEMI</p>';
				$contenuto_operazione = '';
				break;
			}
			
			echo $titolo_operazione;
			
			if($contenuto_operazione != '') echo '<div class="cont_op">' . $contenuto_operazione . '</div>';
			if($stampa_problemi != '') echo $stampa_problemi;
			echo '</div>';
		}
		
		
		
		// Info comuni sia all'arrivo che alla partenza
		
		echo '<div class="form1_650  form_ge_trans ge_pre">';

		echo '<p class="titolo">';
			if($modifica_pre == TRUE)  echo 'MODIFICA TRANS ';
			else 								echo 'NUOVO TRANS ';
			
			echo '<select name="tipo_transfer" class="scelta_stato">';
			echo '<option '; if($info_pre['tipo_transfer'] == 0) echo 'selected="selected" '; echo 'value="0">CLIENTI HOTEL</option>';
			echo '<option '; if($info_pre['tipo_transfer'] == 1) echo 'selected="selected" '; echo 'value="1">MERCE</option>';
			echo '<option '; if($info_pre['tipo_transfer'] == 2) echo 'selected="selected" '; echo 'value="2">IN ESCLUSIVA</option>';
			echo '<option '; if($info_pre['tipo_transfer'] == 3) echo 'selected="selected" '; echo 'value="3">DIPENDENTI</option>';
			echo '<option '; if($info_pre['tipo_transfer'] == 4) echo 'selected="selected" '; echo 'value="4">VILLE</option>';
			echo '<option '; if($info_pre['tipo_transfer'] == 5) echo 'selected="selected" '; echo 'value="5">PROPRIETARI</option>';
			echo '<option '; if($info_pre['tipo_transfer'] == 6) echo 'selected="selected" '; echo 'value="6">RISTORANTE</option>';
			echo '<option '; if($info_pre['tipo_transfer'] == 7) echo 'selected="selected" '; echo 'value="7">ALTRO</option>';
			echo '</select>';
			
		echo '</p>';
		?><table>
				<tr>
					<?php
					echo '<td colspan="1">';
					echo '<select name="titolo">';
					echo '<option '; if($info_pre['titolo'] == 0) echo 'selected="selected" '; echo 'value="0">M.</option>';
					echo '<option '; if($info_pre['titolo'] == 1) echo 'selected="selected" '; echo 'value="1">Mme</option>';
					echo '</select>';
					echo '</td>';
					?>
					<td colspan="2" style="min-width: 230px">
						<input placeholder="NOME TRANSFER" type="text" name="nome" class="field" value="<?php echo formatta_visualizzazione($info_pre['nome']) ?>" autofocus />
					</td>
					<td colspan="1">
						<input placeholder="ADULTI" type="text" name="pax_ad" class="field" value="<?php echo $info_pre['pax_ad'] ?>" />
					</td>
					<td colspan="1">
						<input placeholder="BAMBINI" type="text" name="pax_bam" class="field" value="<?php echo $info_pre['pax_bam'] ?>" />
					</td>
					<td colspan="1">
						<input placeholder="CAMERA" type="text" name="camera" class="field" value="<?php echo $info_pre['camera'] ?>" />
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<input placeholder="EMAIL" type="text" name="email" class="field" value="<?php echo formatta_visualizzazione($info_pre['email']) ?>" />
					</td>
					<td colspan="3">
						<input placeholder="TELEFONO" type="text" name="num_tel" class="field" value="<?php echo formatta_visualizzazione($info_pre['num_tel']) ?>" />
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<input placeholder="EMAILS SECONDARIE" type="text" name="email_sec" class="field" value="<?php echo formatta_visualizzazione($info_pre['email_sec']) ?>" />
					</td>
					<td colspan="2">
						<input placeholder="TELEFONI SECONDARI" type="text" name="num_tel_sec" class="field" value="<?php echo formatta_visualizzazione($info_pre['num_tel_sec']) ?>" />
					</td><?php
					echo '<td colspan="1">';
					echo '<select name="lingua">';
					echo '<option '; if($info_pre['lingua'] == 0) echo 'selected="selected" '; echo 'value="0">FR</option>';
					echo '<option '; if($info_pre['lingua'] == 1) echo 'selected="selected" '; echo 'value="1">IT</option>';
					echo '<option '; if($info_pre['lingua'] == 2) echo 'selected="selected" '; echo 'value="2">EN</option>';
					echo '</select>';
					echo '</td>';
				echo '</tr>';
			
			echo '</table>';
		
		
		echo '<p class="titolo_com">OPZIONI PER INVIO EMAIL</p>';
		echo '<p class="opz_autofill">IN LISTA PER INVII EMAIL ';
		
		echo '<input type="radio" id="auto_remind_si" name="auto_remind" value="1"';
		if($info_pre['auto_remind'] == 1) echo ' checked';
		echo ' />';
		echo '<label for="auto_remind_si"> SI</label>';
		
		echo ' <input type="radio" id="auto_remind_no" name="auto_remind" value="0"';
		if($info_pre['auto_remind'] == 0) echo ' checked';
		echo ' />';
		echo '<label for="auto_remind_no"> NO</label>';
		
		echo '</p>';
		echo '<p class="opz_autofill">MODIFICABILE DAL CLIENTE ';
		
		echo '<input type="radio" id="modificabile_si" name="modificabile" value="1"';
		if($info_pre['modificabile'] >= 1) echo ' checked';
		echo ' />';
		echo '<label for="modificabile_si"> SI</label>';
		
		echo ' <input type="radio" id="modificabile_no" name="modificabile" value="0"';
		if($info_pre['modificabile'] == 0) echo ' checked';
		echo ' />';
		echo '<label for="modificabile_no"> NO</label>';
		
		echo '</p>';
		
		// Se il transfer è modificabile dal cliente
		if($info_pre['modificabile'] >= 1) {
			echo '<p class="opz_autofill">FINO A ';
			echo '<input type="text" name="modificabile_gg" class="field gg_mod" value="'.$info_pre['modificabile'].'" />';
			echo ' GIORNI PRIMA DELL\'ARRIVO</p>';
		}
		else { // Altrimenti nel caso si decida di renderlo nuovamente modificabile settiamo un valore di default di 10 giorni
			echo '<input type="hidden" name="modificabile_gg" value="10" />';
		}
		
		if(isset($info_pre['link']))
			echo '<p class="opz_autofill">LINK : '.$info_pre['link'].' </p>';
			
		stampa_commenti($info_com, $num_com, 0);
		ge_commenti(0);
		
		echo '</div>'; // Chiude form1_650
		
		echo '<div class="pulsanti_bottom">';
				
		// Se stiamo operando su una prenotazione già creata
		if($modifica_pre == TRUE) {
			echo '<input type="hidden" name="id_prenotazione" value="'.$info_pre['id'].'" />';
			echo '<input type="hidden" name="ultima_mod_arr" value="'.$info_pre['ultima_mod_arr'].'" />';
			echo '<input type="hidden" name="ultima_mod_par" value="'.$info_pre['ultima_mod_par'].'" />';
			echo '<input type="hidden" name="data_creazione" value="'.$info_pre['data_creazione'].'" />';
			
			echo '<p class="data_creazione">creato il <br />' .date('d/m/y \<\b\r \/\>\a\l\l\e H:i', $info_pre['data_creazione']) . '</p>';
			
			echo '<a class="bottone bottone_r" href="lista_trans.php?clx_trans='.$info_pre['id'].'">ELIMINA</a> ';
			echo '<a class="bottone" href="lista_trans.php">ANNULLA</a> ';
			echo '<input class="bottone" name="duplica" type="submit" value="DUPLICA" /> ';
			echo '<input class="bottone" name="modifica" type="submit" value="MODIFICA" />';
		}
		else {
			echo '<a class="bottone" href="lista_trans.php">ANNULLA</a> ';
			echo '<input class="bottone" name="modifica" type="submit" value="INSERISCI" />';
		}
					
				echo'</div>'; // Fine pulsanti_bottom
		
		echo '</div>'; // Fine colonna 1
		
		// Settiamo le variabili per la stampa
		$smp_data = array('','');
		$smp_luogo = array('','');
		$smp_taxi = array('','');
		$smp_mezzo = array('','');
		
		
		for($i = 0, $tip = 'arr', $a_p = 'ARRIVO'; $i < 2 ; $i++) {
			echo '<div class="colonna">';
			
			if($i == 1) { $tip = 'par'; $a_p = 'PARTENZA'; }
			
			echo '<div class="form1_650 form_ge_trans ge_pre bor_trans_'.$info_pre['stato_'.$tip].'">';
			
			echo '<p class="titolo bk_trans_'.$info_pre['stato_'.$tip].'">';
			echo $a_p.' ';
			
			echo '<select name="stato_'.$tip.'" class="scelta_stato">';
			echo '<option '; if($info_pre['stato_'.$tip] == 1) echo 'selected="selected" '; echo 'value="1">NON CONFERMATO</option>';
			echo '<option '; if($info_pre['stato_'.$tip] == 2) echo 'selected="selected" '; echo 'value="2">DA RICONFERMARE</option>';
			echo '<option '; if($info_pre['stato_'.$tip] == 3) echo 'selected="selected" '; echo 'value="3">CONFERMATO</option>';
			echo '<option '; if($info_pre['stato_'.$tip] == 4) echo 'selected="selected" '; echo 'value="4">COMPILATO DA CLIENTE</option>';
			echo '<option '; if($info_pre['stato_'.$tip] == 0) echo 'selected="selected" '; echo 'value="0">NON PREVISTO</option>';
			echo '</select>';
			
			
			if($modifica_pre == TRUE) {
				echo ' <span class="data_modifica">modificato il ' .date('d/m - H:i', $info_pre['ultima_mod_'.$tip]) . '</span></p>';
				
				// Recuperiamo le note giornaliere per il giorno scelto							
				if($info_pre['data_'.$tip] != NULL) {
					$db = db_connect();
					$note_brut = $db->query('SELECT * FROM note_giornaliere WHERE data='.$info_pre['data_'.$tip]);
					$note_gg = $note_brut->fetchAll(PDO::FETCH_ASSOC);
					$num_note_gg = count($note_gg);
					$db->connection = NULL;
					
					$num_note_gg = count($note_gg);
				}
				else $num_note_gg = 0;
				
				// Se c'è almeno una nota
				if($num_note_gg > 0) {
					echo '<div class="box_note_gg noclear">';
					for($x = 0 ; $x < $num_note_gg ; $x++) {
						echo '<p class="nota_gg small">';
						
						echo '<span class="ora_com blue_com">'.date('d/m', $note_gg[$x]['data_creazione']).'</span> ';
						
						echo '<span class="reparti">';
						if($note_gg[$x]['tutti'] == 1)		echo 'TUTTI ';
						if($note_gg[$x]['reception'] == 1)	echo 'RECEP ';
						if($note_gg[$x]['facchini'] == 1)	echo 'FACC ';
						if($note_gg[$x]['barca'] == 1)		echo 'BARCA ';
						if($note_gg[$x]['taxi'] == 1)			echo 'TAXI ';
						echo '</span>';
						
						echo '<span class="testo_note">'.formatta_visualizzazione($note_gg[$x]['testo']).'</span>';
						
						echo '</p>';
					}
					echo '</div>';
				}
			} // Fine di if modifica_pre
			
			else echo '</p>';
			
			if($info_pre['stato_'.$tip] == 0) echo '<div class="show_spe">';
			
			echo '<table>';
			
			
			$smp_data[$i] .= '<td colspan="1">';
			if($info_pre['data_'.$tip] != NULL) {
				$smp_data[$i] .= '<span class="day_week_ge_trans">'.$_SESSION['giorni'][date('w', $info_pre['data_'.$tip])].'</span>';
				$smp_data[$i] .= '<input placeholder="DATA '.$a_p.'" type="text" name="data_'.$tip.'" class="field data_trans" value="'.date('d/m/y', $info_pre['data_'.$tip]).'" />';
			}	
			else {
				$smp_data[$i] .= '<input placeholder="DATA '.$a_p.'" type="text" name="data_'.$tip.'" class="field" value="" />';
			}	
			$smp_data[$i] .= '</td>';
			
			$smp_luogo[$i] .= '<td colspan="1">';
			$smp_luogo[$i] .= '<input placeholder="LUOGO ARRIVO" type="text" name="luogo_'.$tip.'" class="field" value="'.$info_pre['luogo_'.$tip].'" />';
			$smp_luogo[$i] .= '</td>';
			$smp_luogo[$i] .= '<td colspan="1">';
			$smp_luogo[$i] .= '<input placeholder="N. VOLO" type="text" name="volo_'.$tip.'" class="field" value="'.$info_pre['volo_'.$tip].'" />';
			$smp_luogo[$i] .= '</td>';
			if($info_pre['ora_'.$tip] != NULL && $info_pre['ora_'.$tip] != 0) {
				$smp_luogo[$i] .= '<td><input placeholder="HH" type="text" name="ora_hh_'.$tip.'" class="field" value="'.date('H', $info_pre['ora_'.$tip]).'" /></td>';
				$smp_luogo[$i] .= '<td><input placeholder="MM" type="text" name="ora_mm_'.$tip.'" class="field" value="'.date('i', $info_pre['ora_'.$tip]).'" /></td>';
			}
			else {
				$smp_luogo[$i] .= '<td><input placeholder="HH" type="text" name="ora_hh_'.$tip.'" class="field" value="" /></td>';
				$smp_luogo[$i] .= '<td><input placeholder="MM" type="text" name="ora_mm_'.$tip.'" class="field" value="" /></td>';
			}
			
			$smp_taxi[$i] .= '<tr>';
			$smp_taxi[$i] .= '<td colspan="2"></td>';
			$smp_taxi[$i] .= '<td colspan="1">';
			$smp_taxi[$i] .= '<select name="taxi_'.$tip.'">';
			$smp_taxi[$i] .= '<option '; if($info_pre['taxi_'.$tip] == 0) $smp_taxi[$i] .= 'selected="selected" '; $smp_taxi[$i] .= 'value="0">NO TAXI</option>';
			$smp_taxi[$i] .= '<option '; if($info_pre['taxi_'.$tip] == 1) $smp_taxi[$i] .= 'selected="selected" '; $smp_taxi[$i] .= 'value="1">MASSIMI</option>';
			$smp_taxi[$i] .= '<option '; if($info_pre['taxi_'.$tip] == 2) $smp_taxi[$i] .= 'selected="selected" '; $smp_taxi[$i] .= 'value="2">TOMMASO</option>';
			$smp_taxi[$i] .= '<option '; if($info_pre['taxi_'.$tip] == 3) $smp_taxi[$i] .= 'selected="selected" '; $smp_taxi[$i] .= 'value="3">AUTO PROPRIA</option>';
			$smp_taxi[$i] .= '<option '; if($info_pre['taxi_'.$tip] == 4) $smp_taxi[$i] .= 'selected="selected" '; $smp_taxi[$i] .= 'value="4">ALTRO</option>';
			$smp_taxi[$i] .= '</select>';
			$smp_taxi[$i] .= '</td>';
			if($info_pre['ora_taxi_'.$tip] != NULL && $info_pre['ora_taxi_'.$tip] != 0) {
				$smp_taxi[$i] .= '<td colspan="1"><input placeholder="HH" type="text" name="ora_taxi_hh_'.$tip.'" class="field" value="'.date('H', $info_pre['ora_taxi_'.$tip]).'" /></td>';
				$smp_taxi[$i] .= '<td colspan="1"><input placeholder="MM" type="text" name="ora_taxi_mm_'.$tip.'" class="field" value="'.date('i', $info_pre['ora_taxi_'.$tip]).'" /></td>';
			}
			else {
				$smp_taxi[$i] .= '<td colspan="1"><input placeholder="HH" type="text" name="ora_taxi_hh_'.$tip.'" class="field" value="" /></td>';
				$smp_taxi[$i] .= '<td colspan="1"><input placeholder="MM" type="text" name="ora_taxi_mm_'.$tip.'" class="field" value="" /></td>';
			}
			$smp_taxi[$i] .= '</tr>';

			$smp_mezzo[$i] .= '<tr>';
			$smp_mezzo[$i] .= '<td colspan="1">';
			$smp_mezzo[$i] .= '<select name="barca_'.$tip.'">';
			$smp_mezzo[$i] .= '<option '; if($info_pre['barca_'.$tip] == 0) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="0">NESSUNO</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['barca_'.$tip] == 1) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="1">FABIR 3</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['barca_'.$tip] == 2) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="2">FABIR 4</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['barca_'.$tip] == 3) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="3">ROTATION</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['barca_'.$tip] == 4) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="4">PRIVATO</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['barca_'.$tip] == 5) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="5">ELICOMPANY</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['barca_'.$tip] == 6) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="6">HELISUD</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['barca_'.$tip] == 7) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="7">ELICO</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['barca_'.$tip] == 8) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="8">ALTRO</option>';
			$smp_mezzo[$i] .= '</select>';
			$smp_mezzo[$i] .= '</td>';
			
			$smp_mezzo[$i] .= '<td colspan="1">';
			$smp_mezzo[$i] .= '<select name="porto_partenza_'.$tip.'">';
			$smp_mezzo[$i] .= '<option '; if($info_pre['porto_partenza_'.$tip] == 0) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="0">NESSUNO</option>';
			if($i != 0) // Se è un arrivo togliamo Cavallo
				$smp_mezzo[$i] .= '<option '; if($info_pre['porto_partenza_'.$tip] == 1) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="1">CAVALLO</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['porto_partenza_'.$tip] == 2) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="2">PIANTARELLA</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['porto_partenza_'.$tip] == 3) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="3">ST. TERESA</option>';
			$smp_mezzo[$i] .= '</select>';
			$smp_mezzo[$i] .= '</td>';
			$smp_mezzo[$i] .= '<td colspan="1">';
			$smp_mezzo[$i] .= '<select name="porto_arrivo_'.$tip.'">';
			$smp_mezzo[$i] .= '<option '; if($info_pre['porto_arrivo_'.$tip] == 0) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="0">NESSUNO</option>';
			
			if($i == 0) // Se è una partenza togliamo Cavallo
				$smp_mezzo[$i] .= '<option '; if($info_pre['porto_arrivo_'.$tip] == 1) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="1">CAVALLO</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['porto_arrivo_'.$tip] == 2) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="2">PIANTARELLA</option>';
			$smp_mezzo[$i] .= '<option '; if($info_pre['porto_arrivo_'.$tip] == 3) $smp_mezzo[$i] .= 'selected="selected" '; $smp_mezzo[$i] .= 'value="3">ST. TERESA</option>';
			$smp_mezzo[$i] .= '</select>';
			$smp_mezzo[$i] .= '</td>';
			if($info_pre['ora_barca_'.$tip] != NULL && $info_pre['ora_barca_'.$tip] != 0) {
				$smp_mezzo[$i] .= '<td><input placeholder="HH" type="text" name="ora_barca_hh_'.$tip.'" class="field" value="'.date('H', $info_pre['ora_barca_'.$tip]).'" /></td>';
				$smp_mezzo[$i] .= '<td><input placeholder="MM" type="text" name="ora_barca_mm_'.$tip.'" class="field" value="'.date('i', $info_pre['ora_barca_'.$tip]).'" /></td>';
			}
			else {
				$smp_mezzo[$i] .= '<td><input placeholder="HH" type="text" name="ora_barca_hh_'.$tip.'" class="field" value="" /></td>';
				$smp_mezzo[$i] .= '<td><input placeholder="MM" type="text" name="ora_barca_mm_'.$tip.'" class="field" value="" /></td>';
			}
			$smp_mezzo[$i] .= '</tr>';
			
			// Stampiamo la scheda arrivo
			if($i == 0) {
				echo '<tr>';
				echo $smp_data[0].$smp_luogo[0];
				echo '</tr>';
				echo $smp_taxi[0];
				echo '<tr><td>MEZZO</td></tr>';
				echo $smp_mezzo[0];
				
			}
			
			// Stampiamo la scheda partenza
			else {
				echo '<tr>'.$smp_data[1].'<td></td><td></td><td colspan="2">MEZZO</td></tr>';
				echo $smp_mezzo[1];
				echo $smp_taxi[1];
				echo '<tr><td></td>'.$smp_luogo[1].'</tr>';
			}





			echo '<tr><td></td><td></td><td></td><td></td>';						
			echo '<td colspan="1">';
			echo '<input placeholder="OPERATORE" type="text" name="operatore_'.$tip.'" class="field" value="'.$info_pre['operatore_'.$tip].'" />';
			echo '</td>';
			echo '</tr>';
			
			echo '</table>';
			if($info_pre['stato_'.$tip] == 0) echo '</div>'; // Si chiude show_spe se il transfer è nascosto
			
			stampa_commenti($info_com, $num_com, $i+1);
			ge_commenti($i+1);
			
			echo '</div>'; // Chiudiamo form1_650
			
			
			lista_barche($info_pre, $tip, $db);
			
			echo '</div>'; // Chiudiamo la colonna
		}

		echo '</form>';
		
		echo '</div>'; // Fine corpo_a
		
	echo '</body></html>';

} // Fin de "Si l'admin s'est bien identifié"

else { // Si pass ou pseudo n'existent pas on renvoie à la page de login administrateur
	header('Location: index.php');
}
?>