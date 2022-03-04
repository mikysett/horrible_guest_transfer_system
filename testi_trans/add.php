<?php
session_start(); // Si lancia la sezione
require('../funzioni_admin.php');

if(isset($_SESSION['tipo_user']) && $_SESSION['tipo_user'] == 0) { // Si verifica che sia l'amministratore

	$azione = '';
	$stampa_problemi = '';
	$testo_update = 'UPDATE testi_trans A inner join(';
	$spe_update = '';
	

	$db = db_connect();
	
	if(isset($_POST['modifica']) || isset($_POST['duplica']) || isset($_GET['elimina']) || isset($_GET['mp'])) {
		$modifica_testo = TRUE;
	}
	else 		$modifica_testo = FALSE;

	// Se il testo va modificato
	if(isset($_GET['mp'])) {
		$testo_sel = intval($_GET['mp']);
	
		// Prendiamo i dati del testo
		$reponse = $db->query('SELECT * FROM testi_trans WHERE id='.$testo_sel);

		$testo = $reponse->fetchAll(PDO::FETCH_ASSOC);


		$info_testo['id'] 			 = &$testo_sel;
		$info_testo['a_scadenza']	 = $testo[0]['a_scadenza'];
		$info_testo['inizio_validita'] = $testo[0]['inizio_validita'];
		$info_testo['fine_validita']	 = $testo[0]['fine_validita'];
		$info_testo['sezione'] 		 = formatta_visualizzazione($testo[0]['sezione']);
		$info_testo['titolo'] 		 = formatta_visualizzazione($testo[0]['titolo']);
		$info_testo['fr'] 			 = formatta_visualizzazione($testo[0]['fr']);
		$info_testo['it'] 			 = formatta_visualizzazione($testo[0]['it']);
		$info_testo['en'] 			 = formatta_visualizzazione($testo[0]['en']);
		$info_testo['note_interne'] = formatta_visualizzazione($testo[0]['note_interne']);
			
	}


	// Se il form è stato compilato e inviato
	elseif(isset($_POST['it'])) {
		if(isset($_POST['id_testo']))
					 $info_testo['id'] = intval($_POST['id_testo']);
		else		 $info_testo['id'] = NULL;
		
		$info_testo['a_scadenza']		 = $_POST['a_scadenza'];
		$info_testo['inizio_validita'] = controllo_data($_POST['inizio_validita']);
		$info_testo['fine_validita']	 = controllo_data($_POST['fine_validita']);
		$info_testo['sezione']			 = formatta_salvataggio_sensitive($_POST['sezione']);
		$info_testo['titolo']			 = formatta_salvataggio_sensitive($_POST['titolo']);
		$info_testo['fr'] 				 = formatta_salvataggio_sensitive($_POST['fr']);
		$info_testo['it'] 				 = formatta_salvataggio_sensitive($_POST['it']);
		$info_testo['en'] 				 = formatta_salvataggio_sensitive($_POST['en']);
		$info_testo['note_interne']	 = formatta_salvataggio_sensitive($_POST['note_interne']);
		
		// Se il testo va modificato
		if($info_testo['id'] != NULL && !isset($_POST['duplica'])) {
			$azione = 'TESTO MODIFIED';
		}
		
		// Se il TESTO va creato
		else {
			
			// Inseriamo una nuova voce nel database testi_trans per ottenere l'id
			$db->query('INSERT INTO testi_trans (a_scadenza) VALUES (0)');
			
			// Recuperiamo l'id del piatto
			$info_id_grezza = $db->query('SELECT id FROM testi_trans ORDER BY id DESC LIMIT 0,1');
		
			$info_id = $info_id_grezza->fetch(PDO::FETCH_ASSOC);
			$info_testo['id'] = $info_id['id'];
			
			$azione = 'TESTO ADDED';
		}
	}
	
	// Se si è appena arrivati sulla pagina di aggiunta transfer
	else {
		$info_testo['a_scadenza']		 = 0;
		$info_testo['inizio_validita'] = NULL;
		$info_testo['fine_validita']	 = NULL;
		$info_testo['sezione']			 = '';
		$info_testo['titolo']			 = '';
		$info_testo['fr'] 				 = '';
		$info_testo['it'] 				 = '';
		$info_testo['en'] 				 = '';
		$info_testo['note_interne']	 = '';
	}
	
	$db->connection = NULL;
	
	
	// Formattiamo i possibili problemi
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head><?php
	echo '<title>';
	if(isset($_GET['mp']) || isset($_POST['it']))  echo formatta_visualizzazione($info_testo['sezione']) . ' - MODIFICA TESTO';
	else 							echo 'NUOVO TESTO';
	echo '</title>';
			
	header_testi_trans();
?></head>
<body style="min-height:1000px"><?php
		$testo_menu_top = ' | <a href="index.php">LISTA TESTI</a>';
		menu_top($testo_menu_top); ?>

		<div id="corpo_a"><?php

		if($azione != '' || $stampa_problemi != '') {
			
			echo '<div class="form1_650';
			
			if($azione == 'TESTO ADDED' || $azione == 'TESTO MODIFIED') echo  ' form_green';
			else	 echo  ' form_red';
			echo '">';
			
			
			switch($azione) {
				case 'TESTO ADDED':
				$titolo_operazione = '<p class="titolo">TESTO AGGIUNTO';
				$contenuto_operazione = '';
				break;
	
				case 'TESTO MODIFIED':
				$titolo_operazione = '<p class="titolo">TESTO MODIFICATO';
				$contenuto_operazione = '';
				break;
	
				case 'NOT FOUND':
				$titolo_operazione = '<p class="titolo">TESTO NON TROVATO</p>';
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
		
		?>

	    	<form name="dati" action="add.php" method="post" enctype="multipart/form-data">
				<div class="form1_650 ge_pre"><?php
			
			echo '<p class="titolo">';
				if($modifica_testo == TRUE)  echo 'MODIFICA TESTO';
				else 								  echo 'NUOVO TESTO';
			
			echo '</p>';
			
			echo '<table>';
			
			echo '<tr><td colspan="3"><input placeholder="SEZIONE" type="text" name="sezione" class="field" value="'.formatta_visualizzazione($info_testo['sezione']).'" autofocus /></td></tr>';
			echo '<tr><td colspan="3"><input placeholder="TITOLO" type="text" name="titolo" class="field" value="'.formatta_visualizzazione($info_testo['titolo']).'" /></td></tr>';
			
			echo '<tr>';
			
			echo '<td>';
			echo 'A SCADENZA<br />';
			echo '<input type="radio" id="a_scadenza_si" name="a_scadenza" value="1"';
			if($info_testo['a_scadenza'] == 1) echo ' checked';
			echo ' />';
			echo '<label for="a_scadenza_si"> SI</label>';
			
			echo ' <input type="radio" id="a_scadenza_no" name="a_scadenza" value="0"';
			if($info_testo['a_scadenza'] == 0) echo ' checked';
			echo ' />';
			echo '<label for="a_scadenza_no"> NO</label>';
			echo '</td>';
			
			echo '<td><input placeholder="INIZIO VALIDIT&Agrave;" type="text" name="inizio_validita" class="field data_trans" value="';
			if($info_testo['inizio_validita'] != NULL) echo date('d/m/y', $info_testo['inizio_validita']);
			echo '" /></td>';
			
			echo '<td><input placeholder="FINE VALIDIT&Agrave;" type="text" name="fine_validita" class="field data_trans" value="';
			if($info_testo['fine_validita'] != NULL) echo date('d/m/y', $info_testo['fine_validita']);
			echo '" /></td>';
			
			echo '</tr>';
			
			
			echo '<tr><td colspan="3"><textarea placeholder="FRANCESE" name="fr">'.formatta_visualizzazione($info_testo['fr']).'</textarea></td></tr>';
			echo '<tr><td colspan="3"><textarea placeholder="ITALIANO" name="it">'.formatta_visualizzazione($info_testo['it']).'</textarea></td></tr>';
			echo '<tr><td colspan="3"><textarea placeholder="INGLESE" name="en">'.formatta_visualizzazione($info_testo['en']).'</textarea></td></tr>';
			echo '<tr><td colspan="3"><textarea placeholder="NOTE A USO INTERNO" name="note_interne">'.formatta_visualizzazione($info_testo['note_interne']).'</textarea></td></tr>';
			
			echo '</table>';
					
					
			echo '<div class="pulsanti_bottom">';
					
			// Se stiamo operando su una prenotazione già creata
			if($modifica_testo == TRUE) {
				echo '<input type="hidden" name="id_testo" value="'.$info_testo['id'].'" />';
				
				echo '<a class="bottone bottone_r" href="index.php?clx='.$info_testo['id'].'">ELIMINA</a> ';
				echo '<a class="bottone" href="index.php">ANNULLA</a> ';
				
				echo '<input class="bottone" name="duplica" type="submit" value="DUPLICA" /> ';
				echo '<input class="bottone" name="modifica" type="submit" value="MODIFICA" />';
			}
			else {
				echo '<a class="bottone" href="menu.php">ANNULLA</a> ';
				echo '<input class="bottone" name="modifica" type="submit" value="INSERISCI" />';
			}
						
			echo'</div></div>';
				
			echo '</form>';
	
			if($modifica_testo == TRUE) {
			
				$sql_update =  'UPDATE testi_trans SET';
			
				$sql_update .= ' a_scadenza='.$info_testo['a_scadenza'].',';
				
				if($info_testo['inizio_validita'] != NULL) $sql_update .= ' inizio_validita='.$info_testo['inizio_validita'].',';
				else 													 $sql_update .= ' inizio_validita=NULL,';
				
				if($info_testo['fine_validita'] != NULL)  $sql_update .= ' fine_validita='.$info_testo['fine_validita'].',';
				else 													$sql_update .= ' fine_validita=NULL,';
				
				$sql_update .= ' sezione=\''.$info_testo['sezione'].'\','
				 				.' titolo=\''.$info_testo['titolo'].'\','
				 				.' fr=\''.$info_testo['fr'].'\','
				 				.' it=\''.$info_testo['it'].'\','
				 				.' en=\''.$info_testo['en'].'\','
				 				.' note_interne=\''.$info_testo['note_interne'].'\'';
				
				$sql_update .= ' WHERE id='.$info_testo['id'].';';
				try {
					$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
					$db->query($sql_update);
				}
				catch(Exception $e) {
					echo 'Exception -> ';
					var_dump($e->getMessage());
				}

				$db = db_connect();
				$db->query($sql_update);
				$db->connection = NULL;
			}
		echo '</div>'; // Fine corpo
		
	echo '</body></html>';

} // Fin de "Si l'admin s'est bien identifié"

else { // Si pass ou pseudo n'existent pas on renvoie à la page de login administrateur
	header('Location: index.php');
}
?>