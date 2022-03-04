<?php
session_start(); // Si lancia la sezione
require('../funzioni_admin.php');

if(isset($_SESSION['tipo_user']) && $_SESSION['tipo_user'] == 0) { // Si verifica che sia l'amministratore

	$azione = 0;
	$num_testi_trovati = 0;
	$piatti_totali = 0;
	$campo_ricerca = '';
	
// Se si è chiesto il backup
if(isset($_GET['backup'])) {
	$backup = backup_tables('*');
	
}
else { // Se si fa il backup si skippa tutto
	
	$db = db_connect();

	// Se è stata effettuata una ricerca
	if(isset($_POST['it'])) {
		$campo_ricerca = formatta_salvataggio($_POST['it']);

		// Se si cerca tra i vari testi
		try {
		 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$db_risp = $db->query("SELECT *, MATCH(it)
				AGAINST('$campo_ricerca*' IN BOOLEAN MODE) AS attinenza
				FROM testi_trans
				WHERE MATCH(it)
				AGAINST('$campo_ricerca*' IN BOOLEAN MODE) ORDER BY attinenza DESC");
		}
		catch(Exception $e) {
			echo 'Exception -> ';
		 	var_dump($e->getMessage());
		}

		$testi_trovati = $db_risp->fetchAll(PDO::FETCH_ASSOC);
		$num_testi_trovati = count($testi_trovati);
	}
	

	// Se non abbiamo fatto ricerche stampiamo tutti i testi
	else {

		// Se si cerca in tutti i tipi di piatto
		try {
		 	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$db_risp = $db->query('SELECT id,sezione,titolo,a_scadenza,inizio_validita,fine_validita,it FROM testi_trans ORDER BY sezione, inizio_validita');
		}
		catch(Exception $e) {
			echo 'Exception -> ';
		 	var_dump($e->getMessage());
		}

		$testi_trovati = $db_risp->fetchAll(PDO::FETCH_ASSOC);
		$num_testi_trovati = count($testi_trovati);
	}
	
	
	$db->connection = NULL;
	
	
	//se si tratta di eliminare si chiede conferma
	if(isset($_GET['clx'])) {
		$id_testo_trans = $_GET['clx'];

		$db = db_connect();
		$nome_grezzo = $db->query('SELECT it FROM testi_trans WHERE id=' . $id_testo_trans);
		$db->connection = NULL;
		
		$nome_grezzo_tab = $nome_grezzo->fetch(PDO::FETCH_ASSOC);
		$nome_el = $nome_grezzo_tab['it'];

		$azione = 'ASK TO DELETE VOICE';
	}
	
	//se si tratta di eliminare e si é confermato si elimina
	elseif(isset($_POST['conferma_testo'])) {
		$id_testo_trans_clx = $_POST['conferma_testo'];
		
		$db = db_connect();
		// Si elimina il piatto
		$db->query('DELETE FROM testi_trans WHERE id=' . $id_testo_trans_clx);
		$db->connection = NULL;
		
		$azione = 'TESTO DELETED';
	}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
	<title>LISTA TESTI TRANSFER</title><?php
	header_testi_trans();
?></head>
<body><?php
	$testo_menu_top = ' | <a href="add.php" target="_blank">NUOVO TESTO</a>';	menu_top($testo_menu_top);
?>
		<div id="corpo_a"><?php
		// STAMPIAMO LA LISTA DEI PIATTI
		
			// STAMPIAMO LA COLONNA SINISTRA
		
			echo '<div class="colonna_sx"><div class="liste_sx">';
			
			// STAMPIAMO IL FORMULARIO PER LA RICERCA
			echo '<form name="dati" action="index.php" method="post" enctype="multipart/form-data">';
			echo '<div class="div_ricerca">';
			
			echo '<div style="width:70%; margin: 20px auto">';
			echo '<input placeholder="TESTO ITALIANO" type="text" name="it" class="field" value="'.formatta_visualizzazione($campo_ricerca).'" autofocus />';
			echo '</div>';
			
			echo '<div class="pulsanti_bottom">';
			echo '<input class="bottone" name="cerca" type="submit" value="CERCA" />';
			
			
			if($num_testi_trovati > 0) echo '<div class="field_ricerca">'.$num_testi_trovati.' TESTI TROVATI</div>';
				
			echo'</div></div>';
		
			echo '</form>';
			
			echo '</div></div>';
			
			echo '<div class="cont_dx">';
			
			if($azione === 'ASK TO DELETE VOICE') {
				echo '<div class="form1_all form_green">';
				echo '<p class="titolo">CONFERMA ELIMINAZIONE</p>';
			
				echo '<div class="cont_op"><p class="form_txt">sei sicuro di voler eliminare il testo <span>' . $nome_el . '</span> definitivamente ?</p></div>';
		
				echo '<div class="pulsanti_bottom">';
				echo '<form style="display: inline;" name="dati" action="index.php" method="post" enctype="multipart/form-data">';
				echo '<input class="bottone" type="submit" value="NO" />';
				echo '</form>';
				
				echo '<form style="display: inline;" name="dati" action="index.php" method="post" enctype="multipart/form-data">';
				echo '<input class="bottone bottone_r" type="submit" value="SI" />';
				echo '<input type="hidden" name="conferma_testo" value="' . $id_testo_trans . '" />';
				echo '</form>';
				echo '</div>';
				echo '</div>';
			}
			
			elseif($azione === 'TESTO DELETED') {
				echo '<div class="form1_all form_green">';
				echo '<p class="titolo">TESTO ELIMINATO CON SUCCESSO</p>';
				echo '</div>';
			}
			
			if($num_testi_trovati > 0) {
				echo '<table class="lista_trans" style="margin:20px 0 0 300px">';
				echo '<tr>';
				echo '<th>SEZIONE</th><th>TITOLO</th><th>INIZIO</th><th>FINE</th><th>TESTO</th>';
				echo '</tr>';
				
				for($i = 0 ; $i < $num_testi_trovati ; $i++) {
					echo '<tr>';
					
					echo '<td><a href="add.php?mp='.$testi_trovati[$i]['id'].'" target="_blank">'.$testi_trovati[$i]['sezione'].'</a></td>';
					echo '<td><a href="add.php?mp='.$testi_trovati[$i]['id'].'" target="_blank">'.$testi_trovati[$i]['titolo'].'</a></td>';
					echo '<td>';
					if($testi_trovati[$i]['a_scadenza'] != 0) echo date('d/m/y', $testi_trovati[$i]['inizio_validita']);
					echo '</td>';
					echo '<td>';
					if($testi_trovati[$i]['a_scadenza'] != 0) echo date('d/m/y', $testi_trovati[$i]['fine_validita']);
					echo '</td>';
					echo '<td><a href="add.php?mp='.$testi_trovati[$i]['id'].'" target="_blank">'.$testi_trovati[$i]['it'].'</a></td>';
					
					echo '</tr>';
					
				}
				echo '</table>';
			}
			
		echo '</div>';
		
		?></div>
	</body>
</html><?php
} // Fine di Se si sta effettuando un backup
} // Fin de "Si l'admin s'est bien identifié"

else { // Si pass ou pseudo n'existent pas on renvoie à la page de login administrateur
	header('Location: ../index.php');
}

?>