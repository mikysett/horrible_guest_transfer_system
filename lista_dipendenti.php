<?php
session_start(); // Si lancia la sezione
require('funzioni_admin.php');

if(isset($_SESSION['tipo_user']) && $_SESSION['tipo_user'] == 0) { // Si verifica che sia l'amministratore


	$azione = 0;
	$vuoto = '';
	$nome_ricerca = '';
	$_SESSION['tipo_lista'] = 2;
	$get_lista = '';

	$colspan_com = array(15, 6, 5, 4, 4, 6, 9, 9);
	

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
	<title>LISTA DIPENDENTI</title><?php
	header_standard();
?></head>
<body><?php
	menu_top(' | LISTA DIPENDENTI');
?>
		<div id="corpo_a"><?php
		
			echo '<div class="box_tipo_lista">';
				echo '<p class="titolo_lista">LISTE HOTEL</p>';
				echo '<a class="tipo_lista" href="lista_trans.php?tipo_lista=0">RECEPTION</a>';
				echo '<a class="tipo_lista" href="lista_trans.php?tipo_lista=1">FACCHINI</a>';
				echo '<a class="tipo_lista" href="lista_trans.php?tipo_lista=2">FABIRS</a>';
				echo '<a class="tipo_lista" href="lista_trans.php?tipo_lista=3">FABIR 3</a>';
				echo '<a class="tipo_lista" href="lista_trans.php?tipo_lista=4">FABIR 4</a>';
				echo '<a class="tipo_lista lista_sel" href="lista_dipendenti.php">DIPENDENTI</a>';
				
				
				echo '<p class="titolo_lista">LISTE TAXI</p>';
				echo '<a class="tipo_lista" href="lista_trans.php?tipo_lista=6">MASSIMI</a>';
				echo '<a class="tipo_lista" href="lista_trans.php?tipo_lista=7">TOMMASO</a>';
				
				echo '<div style="clear:both"></div>';
				echo '</div>';
		
			// STAMPIAMO LA LISTA DEI TRANS PER I DIPENDENTI
			echo '<div class="cont_dx">';
			
			
			// FABIRS

			// Stampiamo la lista su tre giorni
			$db = db_connect();
			$data_ora_oggi = time();
			$giorno_scelto = mktime(0, 0, 0, date('n', $data_ora_oggi), date('j', $data_ora_oggi), date('Y', $data_ora_oggi));
			
			for($x = 0 ; $x < 3 ; $x++) {
				
				if($x != 0) $giorno_scelto = strtotime('+1 day', $giorno_scelto);
			
				// Recupediamo le informazioni per un dato giorno
				$giorno_dopo_barche = strtotime('+1 day', $giorno_scelto);
				
				$sql_query_bar_arr = 'SELECT * FROM transfer WHERE data_arr='.$giorno_scelto.' AND (barca_arr=1 OR barca_arr=2) ORDER BY data_arr,barca_arr';
				$sql_query_bar_par = 'SELECT * FROM transfer WHERE data_par='.$giorno_scelto.' AND (barca_par=1 OR barca_par=2) ORDER BY data_par,barca_par';
				$rep_barche_arr1 = $db->query($sql_query_bar_arr);
				$rep_barche_par1 = $db->query($sql_query_bar_par);
		
				$trans_arr1 = $rep_barche_arr1->fetchAll(PDO::FETCH_ASSOC);
				$trans_par1 = $rep_barche_par1->fetchAll(PDO::FETCH_ASSOC);
				
				$rep_barche1 = array_merge($trans_par1, $trans_arr1);
				$lista_barche = gestione_barche($rep_barche1, $giorno_scelto);
				$num_barche = count($lista_barche);
				
				echo '<div class="arrivi_box dip_box">';
				
				echo '<p class="title_list">';
				
				echo 'VIAGGI DEL '.$_SESSION['giorni'][date('w', $giorno_scelto)].' '.date('d/m', $giorno_scelto);
				
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
					echo '<table class="lista_trans">';
					
					// Stampiamo la testata degli arrivi
					echo '<tr class="head_list_trans">';
					echo '<th>BARCA</th>';
					echo '<th>ORARIO</th>';
					echo '<th>NOMINATIVI</th>';
					echo '<th>PAX</th>';
					echo '<th>TRAGITTO</th>';
					echo '</tr>';
					
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
							$riga[$num_righe]['tragitto'] = $lista_barche[$i]['tragitto'];
							$riga[$num_righe]['pax_totali'] = $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
						}
						// Se i viaggi sono sulla stessa barca per lo stesso posto alla stessa ora
						elseif($riga[$num_righe]['ora'] == $lista_barche[$i]['ora_barca'] && $riga[$num_righe]['barca'] == $lista_barche[$i]['barca']
							 && $riga[$num_righe]['tragitto'] == $lista_barche[$i]['tragitto']) {
							
							$riga[$num_righe]['nominativo'] .= ' <span class="sinbol_piu">+</span> ';
							$riga[$num_righe]['nominativo'] .= '<img class="tipo_trans_img_bar" src="layout/img/trans_'.$lista_barche[$i]['tipo_transfer'].'.png"> ';
							$riga[$num_righe]['nominativo'] .= $_SESSION['stato_trans'][$lista_barche[$i]['stato']].' ';
							$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['pax_ad'];
							if($lista_barche[$i]['pax_bam'] > 0) $riga[$num_righe]['nominativo'] .= '+'.$lista_barche[$i]['pax_bam'];
							$riga[$num_righe]['pax_totali'] += $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
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
							$riga[$num_righe]['tragitto'] = $lista_barche[$i]['tragitto'];
							$riga[$num_righe]['pax_totali'] = $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
						}
					}
					if($i > 0) $num_righe++;
					
					for($i = 0 ; $i < $num_righe ; $i++) {
						echo '<tr class="riga_barca">';
						if($_SESSION['tipo_lista'] == 2 || $_SESSION['tipo_lista'] == 5) echo '<td>'.$riga[$i]['barca'].'</td>';
						echo '<td>'.date('H:i', $riga[$i]['ora']).'</td>';
						echo '<td>'.$riga[$i]['nominativo'].'</td>';
						echo '<td>'.$riga[$i]['pax_totali'].'</td>';
						echo '<td>'.$riga[$i]['tragitto'].'</td>';
						echo '</tr>';
					}
					
					echo '</table>';
				}
				else echo '<p class="no_arrivi">NESSUN VIAGGIO CONFERMATO</p>';
				
				echo '</div>'; // Fine arrivi_box
				echo '</div>'; // Fine liste_box
			}
			
			$db->connection = NULL;

			
			
			echo '</div>';
			
		
		?></div>
	</body>
</html><?php
} // Fin de "Si l'admin s'est bien identifié"

else { // Si pass ou pseudo n'existent pas on renvoie à la page de login administrateur
	header('Location: index.php');
}
?>