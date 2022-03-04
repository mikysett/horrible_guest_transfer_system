<?php
session_start(); // Si lancia la sezione
require('funzioni_admin.php');

if(isset($_SESSION['tipo_user']) && $_SESSION['tipo_user'] == 0) { // Si verifica che sia l'amministratore
	$data = time();
	
	// INIZIALIZZIAMO LE VARIABILI PER SEGMENTAZIONE
	$tipo_transfer = NULL;
	$tratta = 0;
	$porto = 0;
	$mezzo = NULL;
	$taxi = NULL;
	$porto = NULL;
	$luogo_arrivo = NULL;
	
	$num_trans = 0;
	$num_trans_par = 0;
	
	// INIZIALIZZIAMO L'ARRAY PER LE STATISTICHE
	$mezzi = array();
	for($i = 0 ; $i < $_SESSION['num_barche'] ; $i++) {
		$mezzi[$i][0] = 0; // Prenotazioni/Transfer
		$mezzi[$i][1] = 0; // Numero Viaggi
		$mezzi[$i][2] = 0; // Numero Adulti
		$mezzi[$i][3] = 0; // Numero Bambini
		$mezzi[$i][4] = 0; // Totale Pax
	}
	
	// Definiamo la prima e l'ultima data
	if(isset($_POST['data_arrivo'])) {
		$prima_data = controllo_data($_POST['data_arrivo']);
		$ultima_data = controllo_data($_POST['data_partenza']);
	}
	else {
		$prima_data = NULL;
		$ultima_data = NULL;
	}
	
	// Se si è già inviato il form se controllato i dati inseriti
	if(isset($_POST['mezzo'])) {
		// Nel caso si voglia prendere i dati di un'unico mezzo
		if($_POST['tratta'] != 0) 			 $tratta = intval($_POST['tratta']);
		if($_POST['mezzo'] != 0) 			 $mezzo = intval($_POST['mezzo']);
		if($_POST['taxi'] != -1) 			 $taxi = intval($_POST['taxi']);
		if($_POST['porto'] != -1) 			 $porto = intval($_POST['porto']);
		if($_POST['tipo_transfer'] != -1) $tipo_transfer = intval($_POST['tipo_transfer']);
		if($_POST['luogo_arrivo'] != '')  $luogo_arrivo = formatta_salvataggio($_POST['luogo_arrivo']);	
	}
	
	// Controlliamo la validità delle date
	if($prima_data == NULL) {
		$prima_data = $_SESSION['oggi'];
		$ultima_data = strtotime('+30 day', $prima_data);
	}
	elseif($ultima_data == NULL || $ultima_data <= $prima_data) $ultima_data = strtotime('+30 day', $prima_data);
	
	// Recuperiamo i transfer ordniandoli per data
	$trans_select = 'WHERE (';
	
	// Per Arrivi
	if($tratta == 0 || $tratta == 1) { // Se si vogliono sia arrivi e partenze o solo arrivi
		$trans_select .= '(data_arr>='.$prima_data.' AND data_arr<='.$ultima_data.' AND stato_arr != 0';
	
		// Se le statistiche sono per uno specifico mezzo
		if($mezzo !== NULL) 			 $trans_select .= ' AND barca_arr='.$mezzo;
		if($luogo_arrivo !== NULL)  $trans_select .= ' AND luogo_arr=\''.$luogo_arrivo.'\'';
		if($taxi !== NULL)			 $trans_select .= ' AND taxi_arr='.$taxi;
		if($porto !== NULL)			 $trans_select .= ' AND porto_partenza_arr='.$porto;
		if($tipo_transfer !== NULL) $trans_select .= ' AND tipo_transfer='.$tipo_transfer;
	
		$trans_select .= ')';
	}
	
	if($tratta == 0) $trans_select .=' OR '; // Se si vogliono sia arrivi che partenze
	
	// Per Partenze
	if($tratta == 0 || $tratta == 2) { // Se si vogliono sia arrivi e partenze o solo partenze
		$trans_select .= '(data_par>=' . $prima_data . ' AND data_par<=' . $ultima_data.' AND stato_par != 0';
		
		// Se le statistiche sono per uno specifico mezzo
		if($mezzo !== NULL) 			 $trans_select .= ' AND barca_par='.$mezzo;
		if($luogo_arrivo !== NULL)  $trans_select .= ' AND luogo_par=\''.$luogo_arrivo.'\'';
		if($taxi !== NULL)			 $trans_select .= ' AND taxi_par='.$taxi;
		if($porto !== NULL)			 $trans_select .= ' AND porto_arrivo_par='.$porto;
		if($tipo_transfer !== NULL) $trans_select .= ' AND tipo_transfer='.$tipo_transfer;
		
		$trans_select .= ')';
	}
	
	$trans_select .= ')';

	$db = db_connect();
try {
 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$trans_grezzi = $db->query('SELECT * FROM transfer '.$trans_select.' ORDER BY ora_barca_arr, porto_partenza_arr, porto_arrivo_arr, barca_arr');
	if($tratta == 0 || $tratta == 2) {
		$trans_grezzi_par = $db->query('SELECT * FROM transfer '.$trans_select.' ORDER BY ora_barca_par, porto_partenza_par, porto_arrivo_par, barca_par');
		$trans_par = $trans_grezzi_par->fetchAll(PDO::FETCH_ASSOC);
		$num_trans_par = count($trans_par);
	}
}
catch(Exception $e) {
	echo 'Exception -> ';
 	var_dump($e->getMessage());
}
	$db->connection = NULL;
	
	$trans = $trans_grezzi->fetchAll(PDO::FETCH_ASSOC);
	$num_trans = count($trans);
	$trans_rimasti = $num_trans;
	
	
	// Se esistono effettivamente dei transfer per il periodo indicato
	if($num_trans > 0) {
		for($i = 0 ; $i < $num_trans ; $i++) {
			// Arrivi
			if(($trans[$i]['stato_arr'] != 0) &&
				($trans[$i]['data_arr'] >= $prima_data && $trans[$i]['data_arr'] <= $ultima_data) &&
				($tratta == 0 || $tratta == 1) &&
				($mezzo === NULL || $trans[$i]['barca_arr'] == $mezzo) &&
				($taxi === NULL || $trans[$i]['taxi_arr'] == $taxi) &&
				($porto === NULL || $trans[$i]['porto_partenza_arr'] == $porto) &&
				($luogo_arrivo === NULL || $trans[$i]['luogo_arr'] == $luogo_arrivo)
			  ) {
				$mezzi[$trans[$i]['barca_arr']][0]++;
				$mezzi[$trans[$i]['barca_arr']][2] += $trans[$i]['pax_ad'];
				$mezzi[$trans[$i]['barca_arr']][3] += $trans[$i]['pax_bam'];
				$mezzi[$trans[$i]['barca_arr']][4] += $trans[$i]['pax_ad']+$trans[$i]['pax_bam'];
			}
			// Partenze
			if(($trans[$i]['stato_par'] != 0) &&
				($trans[$i]['data_par'] >= $prima_data && $trans[$i]['data_par'] <= $ultima_data) &&
				($tratta == 0 || $tratta == 2) &&
				($mezzo === NULL || $trans[$i]['barca_par'] == $mezzo) &&
				($taxi === NULL || $trans[$i]['taxi_par'] == $taxi) &&
				($porto === NULL || $trans[$i]['porto_arrivo_par'] == $porto) &&
				($luogo_arrivo === NULL || $trans[$i]['luogo_par'] == $luogo_arrivo)
			  ) {
				$mezzi[$trans[$i]['barca_par']][0]++;
				$mezzi[$trans[$i]['barca_par']][2] += $trans[$i]['pax_ad'];
				$mezzi[$trans[$i]['barca_par']][3] += $trans[$i]['pax_bam'];
				$mezzi[$trans[$i]['barca_par']][4] += $trans[$i]['pax_ad']+$trans[$i]['pax_bam'];
			}
		}
		
		
		// Per calcolo numero viaggi arrivi
		for($i = 0, $tot_viaggi_arr = 0 ; $i < $num_trans ; $i++) {
			if(($trans[$i]['stato_arr'] != 0) &&
				($trans[$i]['data_arr'] >= $prima_data && $trans[$i]['data_arr'] <= $ultima_data) &&
				($tratta == 0 || $tratta == 1) &&
				($mezzo === NULL || $trans[$i]['barca_arr'] == $mezzo) &&
				($taxi === NULL || $trans[$i]['taxi_arr'] == $taxi) &&
				($porto === NULL || $trans[$i]['porto_partenza_arr'] == $porto) &&
				($luogo_arrivo === NULL || $trans[$i]['luogo_arr'] == $luogo_arrivo)
			  ) {
				if($tot_viaggi_arr == 0) { $mezzi[$trans[$i]['barca_arr']][1]++; $tot_viaggi_arr++; } // Aggiungiamo un viaggio al mezzo prescelto
				elseif(($trans[$i-1]['barca_arr'] != $trans[$i]['barca_arr']) ||
						 ($trans[$i-1]['ora_barca_arr'] != $trans[$i]['ora_barca_arr']) ||
						 ($trans[$i-1]['porto_partenza_arr'] != $trans[$i]['porto_partenza_arr']) ||
						 ($trans[$i-1]['porto_arrivo_arr'] != $trans[$i]['porto_arrivo_arr'])
						) {
					$mezzi[$trans[$i]['barca_arr']][1]++; // Aggiungiamo un viaggio al mezzo prescelto
					$tot_viaggi_arr++;
				}
			}
		}
		
		
		// Per calcolo numero viaggi partenze
		for($i = 0, $tot_viaggi_par = 0 ; $i < $num_trans_par ; $i++) {
			if(($trans_par[$i]['stato_par'] != 0) &&
				($trans_par[$i]['data_par'] >= $prima_data && $trans_par[$i]['data_par'] <= $ultima_data) &&
				($tratta == 0 || $tratta == 2) &&
				($mezzo === NULL || $trans_par[$i]['barca_par'] == $mezzo) &&
				($taxi === NULL || $trans_par[$i]['taxi_par'] == $taxi) &&
				($porto === NULL || $trans_par[$i]['porto_partenza_par'] == $porto) &&
				($luogo_arrivo === NULL || $trans_par[$i]['luogo_par'] == $luogo_arrivo)
			  ) {
				if($tot_viaggi_par == 0) { $mezzi[$trans_par[$i]['barca_par']][1]++; $tot_viaggi_par++; } // Aggiungiamo un viaggio al mezzo prescelto
				elseif(($trans_par[$i-1]['barca_par'] != $trans_par[$i]['barca_par']) ||
						 ($trans_par[$i-1]['ora_barca_par'] != $trans_par[$i]['ora_barca_par']) ||
						 ($trans_par[$i-1]['porto_partenza_par'] != $trans_par[$i]['porto_partenza_par']) ||
						 ($trans_par[$i-1]['porto_arrivo_par'] != $trans_par[$i]['porto_arrivo_par'])
						) {
					$mezzi[$trans_par[$i]['barca_par']][1]++; // Aggiungiamo un viaggio al mezzo prescelto
					$tot_viaggi_par++;
				}
			}
		}
	}
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head><?php
	echo '<title>';
	echo 'STATISTICHE';
	echo '</title>';

	header_standard();
?></head>
<body><?php
		// Stampiamo il menu
		menu_top('<div class="menu_top">STATISTICHE</div>');
		
		echo '<div id="corpo_a">';
		
		echo '<form name="dati" action="statistiche.php" method="post" enctype="multipart/form-data">';

		echo '<div class="box_tipo_lista">';

		echo ' DAL ';
		echo '<input type="text" class="field_stat" name="data_arrivo" class="data_menu_print" value="'.date('d/m/y', $prima_data).'" />';
		echo ' AL ';
		echo '<input type="text" class="field_stat" name="data_partenza" class="data_menu_print" value="'.date('d/m/y', $ultima_data).'" />';
		
		echo '<br /><br />';
		
		echo ' TRATTA ';
		echo '<select name="tratta">';
		echo '<option '; if($tratta == 0) echo 'selected="selected" '; echo 'value="0">NON SPECIFICATO</option>';
		echo '<option '; if($tratta == 1) echo 'selected="selected" '; echo 'value="1">SOLO ARRIVI</option>';
		echo '<option '; if($tratta == 2) echo 'selected="selected" '; echo 'value="2">SOLO PARTENZE</option>';
		echo '</select>';
		
		echo '<br /><br />';
		
		echo ' MEZZO ';
		echo '<select name="mezzo">';
		echo '<option '; if($mezzo == 1) echo 'selected="selected" '; echo 'value="1">FABIR 3</option>';
		echo '<option '; if($mezzo == 2) echo 'selected="selected" '; echo 'value="2">FABIR 4</option>';
		echo '<option '; if($mezzo == 3) echo 'selected="selected" '; echo 'value="3">ROTATION</option>';
		echo '<option '; if($mezzo == 4) echo 'selected="selected" '; echo 'value="4">PRIVATO</option>';
		echo '<option '; if($mezzo == 5) echo 'selected="selected" '; echo 'value="5">ELICOMPANY</option>';
		echo '<option '; if($mezzo == 6) echo 'selected="selected" '; echo 'value="6">HELISUD</option>';
		echo '<option '; if($mezzo == 7) echo 'selected="selected" '; echo 'value="7">ELICO</option>';
		echo '<option '; if($mezzo == 8) echo 'selected="selected" '; echo 'value="8">ALTRO</option>';
		echo '<option '; if($mezzo == NULL) echo 'selected="selected" '; echo 'value="0">NON SPECIFICATO</option>';
		echo '</select>';
		
		echo '<br /><br />';
		
		echo ' TIPO TRANSFER ';
		echo '<select name="tipo_transfer">';
		echo '<option '; if($tipo_transfer === NULL) echo 'selected="selected" '; echo 'value="-1">NON SPECIFICATO</option>';
		echo '<option '; if($tipo_transfer === 0) echo 'selected="selected" '; echo 'value="0">CLIENTI HOTEL</option>';
		echo '<option '; if($tipo_transfer == 1) echo 'selected="selected" '; echo 'value="1">MERCE</option>';
		echo '<option '; if($tipo_transfer == 2) echo 'selected="selected" '; echo 'value="2">IN ESCLUSIVA</option>';
		echo '<option '; if($tipo_transfer == 3) echo 'selected="selected" '; echo 'value="3">DIPENDENTI</option>';
		echo '<option '; if($tipo_transfer == 4) echo 'selected="selected" '; echo 'value="4">VILLE</option>';
		echo '<option '; if($tipo_transfer == 5) echo 'selected="selected" '; echo 'value="5">PROPRIETARI</option>';
		echo '<option '; if($tipo_transfer == 6) echo 'selected="selected" '; echo 'value="6">RISTORANTE</option>';
		echo '<option '; if($tipo_transfer == 7) echo 'selected="selected" '; echo 'value="7">ALTRO</option>';
		echo '</select>';
		
		echo '<br /><br />';
		
		echo ' TAXI ';
		echo '<select name="taxi">';
		echo '<option '; if($taxi === NULL) echo 'selected="selected" '; echo 'value="-1">NON SPECIFICATO</option>';
		echo '<option '; if($taxi === 0) echo 'selected="selected" '; echo 'value="0">NESSUNO</option>';
		echo '<option '; if($taxi == 1) echo 'selected="selected" '; echo 'value="1">MASSIMI</option>';
		echo '<option '; if($taxi == 2) echo 'selected="selected" '; echo 'value="2">TOMMASO</option>';
		echo '<option '; if($taxi == 3) echo 'selected="selected" '; echo 'value="3">AUTO PROPRIA</option>';
		echo '<option '; if($taxi == 4) echo 'selected="selected" '; echo 'value="4">ALTRO</option>';
		echo '</select>';
		
		echo '<br /><br />';
		
		echo ' PORTO ';
		echo '<select name="porto">';
		echo '<option '; if($porto === NULL) echo 'selected="selected" '; echo 'value="-1">NON SPECIFICATO</option>';
		echo '<option '; if($porto === 0) echo 'selected="selected" '; echo 'value="0">NESSUNO</option>';
		echo '<option '; if($porto == 2) echo 'selected="selected" '; echo 'value="2">PIANTARELLA</option>';
		echo '<option '; if($porto == 3) echo 'selected="selected" '; echo 'value="3">SANTA TERESA</option>';
		echo '<option '; if($porto == 4) echo 'selected="selected" '; echo 'value="4">ALTRO</option>';
		echo '</select>';
		
		echo '<br /><br />';
		
		echo ' LUOGO ARR/PAR ';
		echo '<input type="text" name="luogo_arrivo" class="field_stat" value="'.formatta_visualizzazione($luogo_arrivo).'" />';
		
		echo '<br />';
		
		echo '<input class="bottone" name="aggiorna" type="submit" value="AGGIORNA" />';
		echo '</form>';
		echo '</div>';
		
		// Stampiamo la testata
		echo '<div class="menu">';
		
		echo '</div>';
		
		// Stampiamo la testata delle statistiche
		
		echo '<div class="liste_box">';
		echo '<div class="arrivi_box">';
		echo '<div class="title_list">STATISTICHE';
		if($mezzo != '') echo ' PER '.formatta_visualizzazione($mezzo);
		
		echo ' DAL '.date('d/m/Y', $prima_data).' AL '.date('d/m/Y', $ultima_data);
		
		echo '</div>';
		
		if($num_trans == 0) {
			echo '<div class="testa_stat">NESSUN DATO TROVATO</div>';
		}
		else {
			$tot_viaggi = 0;
			$tot_trans = 0;
			$tot_ad = 0;
			$tot_bam = 0;
			$tot_pax = 0;
			
			// Stampiamo le statistiche
			echo '<table class="lista_trans stat_tab">';
			echo '<tr><th>MEZZO</th><th>VIAGGI</th><th>TRANSFER</th><th>ADULTI</th><th>BAMBINI</th><th>TOT PAX</th><th>PAX/VIAGGIO</th></tr>';
			
			for($i = 0 ; $i < $_SESSION['num_barche'] ; $i++) {
				
				echo '<tr>';
				if($i != 0)	echo '<td>'.$_SESSION['barche'][$i].'</td>'; // Nome mezzo
				else 			echo '<td>NESSUNO</td>'; // Se nessun mezzo è selezionato
				
				if($i != 0 && $i != 8) echo '<td>'.$mezzi[$i][1].'</td>'; // Numero viaggi
				else						  echo '<td></td>'; // Se il numero di viaggi non è pertinente
				
				echo '<td>'.$mezzi[$i][0].'</td>'; // Numero Prenotazioni
				echo '<td>'.$mezzi[$i][2].'</td>'; // Numero Adulti
				echo '<td>'.$mezzi[$i][3].'</td>'; // Numero Bambini
				echo '<td>'.$mezzi[$i][4].'</td>'; // Totale Pax
				
				echo '<td style="text-align:right">';
				if($mezzi[$i][4] != 0 && $i != 0 && $i != 8) {
					echo round($mezzi[$i][4]/$mezzi[$i][1], 2); // Totale Pax / Numero Viaggi
				}
				echo '</td>';
				echo '</tr>';
	
				$tot_viaggi += $mezzi[$i][1];
				$tot_trans += $mezzi[$i][0];
				$tot_ad += $mezzi[$i][2];
				$tot_bam += $mezzi[$i][3];
				$tot_pax += $mezzi[$i][4];
			}
			
			echo '<tr style="font-weight:bold">';
			echo '<td>TOTALI</td><td>'.$tot_viaggi.'</td>';
			echo '<td>'.$tot_trans.'</td>';
			echo '<td>'.$tot_ad.'</td>';
			echo '<td>'.$tot_bam.'</td>';
			echo '<td>'.$tot_pax.'</td>';
			echo '<td></td></tr>';
			
			echo '</div>'; // liste_box
			echo '</div>'; // arrivi_box
			echo '</table>';
			
			echo '</div>'; // corpo_a
		}
		
	?></body>
</html><?php
} // Fin de "Si l'admin s'est bien identifié"

else { // Si pass ou pseudo n'existent pas on renvoie à la page de login administrateur
	header('Location: index.php');
}
?>