<?php
session_start(); // Si lancia la sezione
require('funzioni_admin.php');

// Si consente l'accesso unicamente all'amministratore oppure alle barche
if(isset($_SESSION['tipo_user']) && (($_SESSION['tipo_user'] >= 3 && $_SESSION['tipo_user'] <= 4) || $_SESSION['tipo_user'] == 0)) {
	
	$db = db_connect();
	
	$data_now = time();
	
	$mezzo = 1; // Di default siamo sul fabir 3
	$azione = 0;
	$vuoto = '';
	
	$get_lista = '';
	$tot_trans = 0;
	$tot_ad = 0;
	$tot_bam = 0;
	$opzioni = '';
	$mod_last_minute = 21600;
	
	
	if($_SESSION['tipo_user'] == 3)		{ $_SESSION['tipo_lista'] = 3; $nome_mezzo = 'FABIR 3'; $mezzo = 1; } // FABIR 3
	elseif($_SESSION['tipo_user'] == 4) { $_SESSION['tipo_lista'] = 4; $nome_mezzo = 'FABIR 4'; $mezzo = 2; } // FABIR 4
	
	// Se stiamo modificando lo stato di un mezzo
	if(isset($_GET['mezzo'])) {
		$mezzo = intval($_GET['mezzo']);
		$stato = intval($_GET['stato']);
		
		// Se lo stato richiede di conoscere l'orario
		if($stato >= 5 && $stato <= 8) $timestamp_stato = $data_now;
		else 									 $timestamp_stato = 0;
		
		// Aggiorniamo il database
		$sql = 'UPDATE stato_mezzi SET stato='.$stato.',timestamp_stato='.$timestamp_stato.' WHERE mezzo='.$mezzo;
		
		try {
		 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$rep_stato_mezzi = $db->query($sql);
		}
		catch(Exception $e) {
			echo 'Exception -> ';
		 	var_dump($e->getMessage());
		}
	}

	
	if($_SESSION['tipo_lista'] == 3) { $condizioni_lista_bar = ' AND (barca_arr=1 OR barca_par=1)'; } // Fabir 3
	if($_SESSION['tipo_lista'] == 4) { $condizioni_lista_bar = ' AND (barca_arr=2 OR barca_par=2)'; } // Fabir 4
	
	// Se si è scelto di cambiare giorno in avanti
	if(isset($_POST['giorno_dopo'])) {
		$_SESSION['timestamp_lista'] = strtotime('+1 day', $_SESSION['timestamp_lista']);
	}
	
	// Se si è scelto di cambiare giorno indietro
	elseif(isset($_POST['giorno_prima'])) {
		$_SESSION['timestamp_lista'] = strtotime('-1 day', $_SESSION['timestamp_lista']);
	}
	
	// Se si è scelto di tornare ad oggi
	elseif(isset($_POST['data_oggi'])) {
		$_SESSION['timestamp_lista'] = controllo_data($_POST['data_cercata']);
		if($_SESSION['timestamp_lista'] == NULL) $_SESSION['timestamp_lista'] = $_SESSION['oggi'];
	}
	
	// Per liste fabirs e dipendenti
	$giorno_dopo_barche = strtotime('+1 day', $_SESSION['timestamp_lista']);
	
	$sql_query_bar_arr = 'SELECT transfer.* FROM transfer WHERE data_arr='.$_SESSION['timestamp_lista'].
								' AND (barca_arr=1 OR barca_arr=2) ORDER BY data_arr,barca_arr';
	$sql_query_bar_par = 'SELECT transfer.* FROM transfer WHERE data_par='.$_SESSION['timestamp_lista'].
								' AND (barca_par=1 OR barca_par=2) ORDER BY data_par,barca_par';

	$rep_barche_arr1 = $db->query($sql_query_bar_arr);
	$rep_barche_par1 = $db->query($sql_query_bar_par);

	$trans_arr1 = $rep_barche_arr1->fetchAll(PDO::FETCH_ASSOC);
	$trans_par1 = $rep_barche_par1->fetchAll(PDO::FETCH_ASSOC);
	
	$rep_barche1 = array_merge($trans_par1, $trans_arr1);
	$lista_barche = gestione_barche($rep_barche1, $_SESSION['timestamp_lista']);
	$num_barche = count($lista_barche);
	
	// Recuperiamo le note giornaliere per il giorno scelto
	$note_brut = $db->query('SELECT * FROM note_giornaliere WHERE data='.$_SESSION['timestamp_lista'].' AND (barca=1 OR tutti=1)');
	$note_gg = $note_brut->fetchAll(PDO::FETCH_ASSOC);
	
	$db->connection = NULL;
	

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head><?php
	echo '<title>'.$_SESSION['barche_nostyle'][$mezzo].' - TRANSFERS</title>';
	?><link rel="stylesheet" href="layout/mezzi.css" type="text/css" />
	<link rel="icon" href="favicon.png" type="image/png" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-language" content="it" />
	<meta name="author" content="michele" />
	<meta name="copyright" content="Michele Sessa" />
	<meta name="robots" content="none" />
	<meta http-equiv="refresh" content="20;URL=mezzi.php">
</head>
<body><?php
	
	// Apriamo il form prima del menu per il bottone assegna camere
	echo '<form name="dati" action="mezzi.php'.$get_lista.'" method="post" enctype="multipart/form-data">';
?>
		<div id="corpo_a"><?php
		// STAMPIAMO LA LISTA DEI TRANS

			//echo colonna_sinistra();
			
			echo '<div class="cont_dx">';
			
			// Pulsanti per ricerca e navigazione
			echo '<div class="menu_nav">';
			
			stato_mezzi($mezzo);
			
			// Calcoliamo la data di ultima modifica e la stampiamo
			if($num_barche > 0) {
				for($i = 0, $ultima_mod_barche = 0 ; $i < $num_barche ; $i++) {
					if($ultima_mod_barche < $lista_barche[$i]['ultima_mod']) $ultima_mod_barche = $lista_barche[$i]['ultima_mod'];
				}
				
				echo ' <span class="ultima_mod_lista';
				if($data_now - $ultima_mod_barche < $mod_last_minute / 2) echo ' riga_red';
				elseif($data_now - $ultima_mod_barche < $mod_last_minute)	 echo ' riga_spe';
				echo '">MOD. IL '.date('d/m H:i', $ultima_mod_barche).'</span>';
			}
			
			echo '<div class="box_nav">';
			
			// Inseriamo la navigazione per data
			echo '<div class="ricerca_data">';
			echo '<input class="bottone" name="giorno_prima" type="submit" value="<" />';
			echo '<span class="day_week">'.$_SESSION['giorni_long'][date('w', $_SESSION['timestamp_lista'])];
			echo ' '.date('d/m', $_SESSION['timestamp_lista']);
			echo '</span>';
			echo '<input class="bottone" name="giorno_dopo" type="submit" value=">" />';
			echo '</div>';
			
			echo '<div style="clear:both"></div>';
			echo '</div>';
			
			echo '</div>'; // Fine menu_nav
	
			// Se non si ricerca per nome inseriamo la colonna delle liste
			if(isset($_GET['tipo_lista'])) $_SESSION['tipo_lista'] = $_GET['tipo_lista'];

			// FABIRS

			// Stampiamo la lista in un modo se sono fabir3 o fabir4
				
			echo '<div class="liste_box"><div class="arrivi_box">';
			
			// GESTIAMO LE NOTE GIORNALIERE
			
			$num_note_gg = count($note_gg);
			
			// Se c'è almeno una nota
			if($num_note_gg > 0) {
				echo '<div class="box_note_gg">';
				for($i = 0 ; $i < $num_note_gg ; $i++) {
					echo '<p class="nota_gg">';
					echo '<span class="testo_note">'.formatta_visualizzazione($note_gg[$i]['testo']).'</span>';
					echo '</p>';
				}
				echo '</div>';
			}
			
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
						if($lista_barche[$i]['pax_ad'] > 0 || $lista_barche[$i]['pax_bam'])
							$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['pax_ad'];
						if($lista_barche[$i]['pax_bam'] > 0)
							$riga[$num_righe]['nominativo'] .= '+'.$lista_barche[$i]['pax_bam'];
						if($lista_barche[$i]['pax_ad'] > 0 || $lista_barche[$i]['pax_bam'])
							$riga[$num_righe]['nominativo'] .= ' ';
						$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['nominativo'];
						$riga[$num_righe]['tragitto'] = $lista_barche[$i]['tragitto'];
						$riga[$num_righe]['pax_totali'] = $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
						$riga[$num_righe]['link'] = $lista_barche[$i]['link'];
						$riga[$num_righe]['commenti'] = '';
						
						$riga[$num_righe]['riga_spe'] = 0;
						
						// Se la modifica è a meno di 3 o 6 ore dall'orario di stampa
						if($riga[$num_righe]['riga_spe'] != 2) {
							if($data_now - $lista_barche[$i]['ultima_mod'] < $mod_last_minute / 2) $riga[$num_righe]['riga_spe'] = 2; // 3 ore
							elseif($data_now - $lista_barche[$i]['ultima_mod'] < $mod_last_minute) $riga[$num_righe]['riga_spe'] = 1; // 6 ore
						}
						
						// Se ci sono commenti da inserire li inseriamo
						for($j = 0 ; $j < $num_com_barche ; $j++) {
							if($lista_barche[$i]['id'] == $com_barche[$j]['id_transfer']) {
								$riga[$num_righe]['commenti'] .= '<tr><td class="td_com" colspan="4">';
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
						if($lista_barche[$i]['pax_ad'] > 0 || $lista_barche[$i]['pax_bam'])
							$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['pax_ad'];
						if($lista_barche[$i]['pax_bam'] > 0)
							$riga[$num_righe]['nominativo'] .= '+'.$lista_barche[$i]['pax_bam'];
						if($lista_barche[$i]['pax_ad'] > 0 || $lista_barche[$i]['pax_bam'])
							$riga[$num_righe]['nominativo'] .= ' ';
						$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['nominativo'];
						$riga[$num_righe]['pax_totali'] += $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
						
						// Se la modifica è a meno di 3 o 6 ore dall'orario di stampa
						if($riga[$num_righe]['riga_spe'] != 2) {
							if($data_now - $lista_barche[$i]['ultima_mod'] < $mod_last_minute / 2) $riga[$num_righe]['riga_spe'] = 2; // 3 ore
							elseif($data_now - $lista_barche[$i]['ultima_mod'] < $mod_last_minute) $riga[$num_righe]['riga_spe'] = 1; // 6 ore
						}
						
						// Se ci sono commenti da inserire li inseriamo
						for($j = 0 ; $j < $num_com_barche ; $j++) {
							if($lista_barche[$i]['id'] == $com_barche[$j]['id_transfer']) {
								$riga[$num_righe]['commenti'] .= '<tr><td class="td_com" colspan="4">';
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
						if($lista_barche[$i]['pax_ad'] > 0 || $lista_barche[$i]['pax_bam'])
							$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['pax_ad'];
						if($lista_barche[$i]['pax_bam'] > 0)
							$riga[$num_righe]['nominativo'] .= '+'.$lista_barche[$i]['pax_bam'];
						if($lista_barche[$i]['pax_ad'] > 0 || $lista_barche[$i]['pax_bam'])
							$riga[$num_righe]['nominativo'] .= ' ';
						$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['nominativo'];
						$riga[$num_righe]['tragitto'] = $lista_barche[$i]['tragitto'];
						$riga[$num_righe]['pax_totali'] = $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
						$riga[$num_righe]['link'] = $lista_barche[$i]['link'];
						$riga[$num_righe]['commenti'] = '';$riga[$num_righe]['riga_spe'] = 0;
						$riga[$num_righe]['riga_spe'] = 0;
						
						// Se la modifica è a meno di 3 o 6 ore dall'orario di stampa
						if($riga[$num_righe]['riga_spe'] != 2) {
							if($data_now - $lista_barche[$i]['ultima_mod'] < $mod_last_minute / 2) $riga[$num_righe]['riga_spe'] = 2; // 3 ore
							elseif($data_now - $lista_barche[$i]['ultima_mod'] < $mod_last_minute) $riga[$num_righe]['riga_spe'] = 1; // 6 ore
						}
						
						// Se ci sono commenti da inserire li inseriamo
						for($j = 0 ; $j < $num_com_barche ; $j++) {
							if($lista_barche[$i]['id'] == $com_barche[$j]['id_transfer']) {
								$riga[$num_righe]['commenti'] .= '<tr><td class="td_com" colspan="4">';
								$riga[$num_righe]['commenti'] .= '<span class="nome_x_barche">'.$lista_barche[$i]['nominativo'].'</span> <span class="testo_com">'.formatta_visualizzazione($com_barche[$j]['testo']).'</span>';
								$riga[$num_righe]['commenti'] .= '</td></tr>';
							}
						}
					}
				}
				if($i > 0) $num_righe++;
				
				for($i = 0 ; $i < $num_righe ; $i++) {
					echo '<tr class="riga_barca';
					if($riga[$i]['riga_spe'] == 1) echo ' riga_spe';
					if($riga[$i]['riga_spe'] == 2) echo ' riga_red';
					
					elseif($i % 2 == 1)				 echo ' riga_blu';
					echo '">';
					if($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 5) echo '<td>'.$riga[$i]['barca'].'</td>';
					echo '<td class="small_col">'.date('H:i', $riga[$i]['ora']).'</td>';
					echo '<td class="med_col">'.$riga[$i]['tragitto'].'</td>';
					echo '<td>'.$riga[$i]['nominativo'].'</td>';
					echo '<td class="small_col">'.$riga[$i]['pax_totali'].'</td>';
					echo '</tr>';
					if(isset($riga[$i]['commenti'])) echo $riga[$i]['commenti'];
				}
				
				echo '</table>';
			}
			else echo '<p class="no_arrivi">NESSUN VIAGGIO CONFERMATO</p>';
			
			echo '</div>'; // Fine arrivi_box
			echo '</div>'; // Fine liste_box
			
			echo '</div>';
			echo '</form>';
			
		
		?></div>
	</body>
</html><?php
} // Fin de "Si l'admin s'est bien identifié"

else { // Si pass ou pseudo n'existent pas on renvoie à la page de login administrateur
	header('Location: index.php');
}
?>