<?php

function stampa_commenti(&$commenti, &$num_com, $tipo_com) {
	// Stampiamo i commenti già salvati				
	for($i = 0 ; $i < $num_com ; $i++) {
		if($commenti[$i]['tipo_commento'] != $tipo_com) continue; // Se il commento è della sezione successiva
		// Stampiamo il commento
		echo '<div class="commento com_ins">';
		echo '<p class="testata_com">';
		
		echo '<span class="box_clx"><input type="checkbox" id="clx_com' . $i . '" name="clx_com' . $i . '" value="' . $commenti[$i]['id'] . '" />';
		echo '<label class="clx_com" for="clx_com' . $i . '"> CLX</label></span>';
		
		echo '<span class="ora_com">'.date('d/m|H:i', $commenti[$i]['data_creazione']).'</span> ';
		echo '<span class="reparti">';
		if($commenti[$i]['tutti'] == 1)		echo 'TUTTI ';
		if($commenti[$i]['reception'] == 1)	echo 'RECEP ';
		if($commenti[$i]['governante'] == 1)echo 'GOV ';
		if($commenti[$i]['ristorante'] == 1)echo 'RISTO ';
		if($commenti[$i]['facchini'] == 1)	echo 'FACC ';
		if($commenti[$i]['barca'] == 1)		echo 'BARCA ';
		if($commenti[$i]['taxi'] == 1)		echo 'TAXI ';
		if($commenti[$i]['cliente'] == 1)	echo 'CLIENTE ';
		echo ' - </span>';
		
		echo '<span class="testo_com">'.str_show($commenti[$i]['testo']);
		if($commenti[$i]['operatore'] != '') echo '<span class="op_com">-'.str_show($commenti[$i]['operatore']).'</span>';
		
		echo '</span>';
		
		echo '</p>';
	
		echo '</div>';
	}
}

function ge_commenti($tipo_com) {
	// $tipo_com : 0 = GENRICI, 1 = ARRIVO, 2 = PARTENZA
	$titolo_com = array('GENERICI', 'ARRIVO', 'PARTENZA');
	
	
	echo '<div class="cont_com">';
	echo '<div class="form1_650 form1_com ge_pre">';
	echo '<p class="titolo_com">COMMENTI ';
	
	
	// Inseriamo scelte rapide se non siamo nei generici
	if($tipo_com != 0) {
		echo '<span class="com_rapidi_box">';
		echo '<span class="com_rapido">';
		echo '<input type="checkbox" id="'.$tipo_com.'rapid_com0" name="'.$tipo_com.'rapid_com0" value="CONFERMA TELEFONICA" />';
		echo '<label class="rapid_com" for="'.$tipo_com.'rapid_com0"> CONF TEL</label>';
		echo '</span>';
		
		echo '<span class="com_rapido">';
		echo ' <input type="checkbox" id="'.$tipo_com.'rapid_com1" name="'.$tipo_com.'rapid_com1" value="CONFERMA VIA EMAIL" />';
		echo '<label class="rapid_com" for="'.$tipo_com.'rapid_com1"> CONF EMAIL</label>';
		echo '</span>';
		
		echo '<span class="com_rapido">';
		echo ' <input type="checkbox" id="'.$tipo_com.'rapid_com2" name="'.$tipo_com.'rapid_com2" value="LASCIATO MSG IN SEGRETERIA" />';
		echo '<label class="rapid_com" for="'.$tipo_com.'rapid_com2"> MSG SEG</label>';
		echo '</span>';
		
		echo '<span class="com_rapido">';
		echo ' <input type="checkbox" id="'.$tipo_com.'rapid_com3" name="'.$tipo_com.'rapid_com3" value="INVIATO EMAIL" />';
		echo '<label class="rapid_com" for="'.$tipo_com.'rapid_com3"> INV EMAIL</label>';
		echo '</span>';
		echo '</span>';
	}
	
	echo '</p>';
	
	// Inseriamo form per aggiungere commenti
	echo '<div class="commento">';
	echo '<p class="testata_com">';
	echo '<span class="reparti">';
	echo ' <input type="checkbox" id="tutti_com' . $tipo_com . '" name="tutti_com' . $tipo_com . '" value="' . $tipo_com . '" />';
	echo '<label class="tutti_com" for="tutti_com' . $tipo_com . '"> TUTTI</label>';
	echo ' <input type="checkbox" id="reception_com' . $tipo_com . '" name="reception_com' . $tipo_com . '" value="' . $tipo_com . '" />';
	echo '<label class="reception_com" for="reception_com' . $tipo_com . '"> RECEP</label>';
	echo ' <input type="checkbox" id="governante_com' . $tipo_com . '" name="governante_com' . $tipo_com . '" value="' . $tipo_com . '" />';
	echo '<label class="governante_com" for="governante_com' . $tipo_com . '"> GOV</label>';
	echo ' <input type="checkbox" id="ristorante_com' . $tipo_com . '" name="ristorante_com' . $tipo_com . '" value="' . $tipo_com . '" />';
	echo '<label class="ristorante_com" for="ristorante_com' . $tipo_com . '"> RISTO</label>';
	echo ' <input type="checkbox" id="facchini_com' . $tipo_com . '" name="facchini_com' . $tipo_com . '" value="' . $tipo_com . '" />';
	echo '<label class="facchini_com" for="facchini_com' . $tipo_com . '"> FACC</label>';
	echo ' <input type="checkbox" id="barca_com' . $tipo_com . '" name="barca_com' . $tipo_com . '" value="' . $tipo_com . '" />';
	echo '<label class="barca_com" for="barca_com' . $tipo_com . '"> BARCA</label>';
	echo ' <input type="checkbox" id="taxi_com' . $tipo_com . '" name="taxi_com' . $tipo_com . '" value="' . $tipo_com . '" />';
	echo '<label class="taxi_com" for="taxi_com' . $tipo_com . '"> TAXI</label>';
	echo ' <input type="checkbox" id="cliente_com' . $tipo_com . '" name="cliente_com' . $tipo_com . '" value="' . $tipo_com . '" />';
	echo '<label class="taxi_com" for="cliente_com' . $tipo_com . '"> CLIENTE</label>';
	echo '</span>';
	echo '</p>';
	
	echo '<input class="field op_com" placeholder="OP" type="text" name="operatore_com'.$tipo_com.'" value="" />';
	echo '<input placeholder="COMMENTO" type="text" name="testo_com'.$tipo_com.'" class="field testo_com" value="" />';
	echo '<div style="clear:both"></div>';

	echo '</div>';
	
	echo '</div>';

	echo '</div>';
}

function lista_barche(&$info_pre, $a_p, $db) {
// Stampiamo la lista delle barche per il giorno di arrivo
			if($info_pre['data_'.$a_p] != NULL) {
				$tipo_lista = 2; // Fabirs
				$colspan_com = 5;
				$max_char = 3;
				$sql_query_bar_arr = 'SELECT transfer.* FROM transfer WHERE data_arr='.$info_pre['data_'.$a_p].' AND (barca_arr=1 OR barca_par=1 OR barca_arr=2 OR barca_par=2) ORDER BY data_'.$a_p;
				$sql_query_bar_par = 'SELECT transfer.* FROM transfer WHERE data_par='.$info_pre['data_'.$a_p].' AND (barca_arr=1 OR barca_par=1 OR barca_arr=2 OR barca_par=2) ORDER BY data_'.$a_p;
			
				$rep_barche_arr1 = $db->query($sql_query_bar_arr);
				$rep_barche_par1 = $db->query($sql_query_bar_par);
	
				$trans_arr1 = $rep_barche_arr1->fetchAll(PDO::FETCH_ASSOC);
				$trans_par1 = $rep_barche_par1->fetchAll(PDO::FETCH_ASSOC);
				
				$rep_barche1 = array_merge($trans_par1, $trans_arr1);
				$lista_barche = gestione_barche($rep_barche1, $info_pre['data_'.$a_p]);
				$num_barche = count($lista_barche);
				$num_righe = 0;
					
				
				echo '<div class="arrivi_box box_barche_ge_trans">';
				
				// Se non ci sono partenze non stampiamo la lista
				if($num_barche > 0) {
					echo '<table class="lista_trans lista_barche">';
					
					// Stampiamo la testata degli arrivi
					echo '<tr class="head_list_trans">';
					echo '<th>BARCA</th>';
					echo '<th>ORA</th>';
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
					for($i = 0, $com_now = 0 ; $i < $num_barche ; $i++) {
						// Se siamo alla prima riga
						if($i == 0) {
							$riga[$num_righe]['barca'] = $lista_barche[$i]['barca'];
							$riga[$num_righe]['ora'] = $lista_barche[$i]['ora_barca'];
							$riga[$num_righe]['nominativo'] = '<img class="tipo_trans_img_bar" src="layout/img/trans_'.$lista_barche[$i]['tipo_transfer'].'.png"> ';
							$riga[$num_righe]['nominativo'] .= $_SESSION['stato_trans'][$lista_barche[$i]['stato']].' ';
							$riga[$num_righe]['nominativo'] .= $lista_barche[$i]['pax_ad'];
							if($lista_barche[$i]['pax_bam'] > 0) $riga[$num_righe]['nominativo'] .= '+'.$lista_barche[$i]['pax_bam'];
							$riga[$num_righe]['nominativo'] .= ' <a href="ge_trans.php?mp='.$lista_barche[$i]['id'].'" target="_blank">'.substr($lista_barche[$i]['nominativo'],0,$max_char).'</a>';
							$riga[$num_righe]['tragitto'] = $lista_barche[$i]['tragitto'];
							$riga[$num_righe]['pax_totali'] = $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
							$riga[$num_righe]['link'] = $lista_barche[$i]['link'];
							$riga[$num_righe]['commenti'] = '';
							
							// Se ci sono commenti da inserire li inseriamo
							for($j = 0 ; $j < $num_com_barche ; $j++) {
								if($lista_barche[$i]['id'] == $com_barche[$j]['id_transfer']) {
									$riga[$num_righe]['commenti'] .= '<tr><td class="td_com" colspan="'.$colspan_com.'">';
									$riga[$num_righe]['commenti'] .= '<span class="nome_x_barche">'.$lista_barche[$i]['nominativo'].'</span> <span class="testo_com">'.str_show($com_barche[$j]['testo']).'</span>';
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
							$riga[$num_righe]['nominativo'] .= ' <a href="ge_trans.php?mp='.$lista_barche[$i]['id'].'" target="_blank">'.substr($lista_barche[$i]['nominativo'],0,$max_char).'</a>';
							$riga[$num_righe]['pax_totali'] += $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
							
							
							// Se ci sono commenti da inserire li inseriamo
							for($j = 0 ; $j < $num_com_barche ; $j++) {
								if($lista_barche[$i]['id'] == $com_barche[$j]['id_transfer']) {
									$riga[$num_righe]['commenti'] .= '<tr><td class="td_com" colspan="'.$colspan_com.'">';
									$riga[$num_righe]['commenti'] .= '<span class="nome_x_barche">'.$lista_barche[$i]['nominativo'].'</span> <span class="testo_com">'.str_show($com_barche[$j]['testo']).'</span>';
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
							$riga[$num_righe]['nominativo'] .= ' <a href="ge_trans.php?mp='.$lista_barche[$i]['id'].'" target="_blank">'.substr($lista_barche[$i]['nominativo'],0,$max_char).'</a>';
							$riga[$num_righe]['tragitto'] = $lista_barche[$i]['tragitto'];
							$riga[$num_righe]['pax_totali'] = $lista_barche[$i]['pax_ad']+$lista_barche[$i]['pax_bam'];
							$riga[$num_righe]['link'] = $lista_barche[$i]['link'];
							$riga[$num_righe]['commenti'] = '';
							
							// Se ci sono commenti da inserire li inseriamo
							for($j = 0 ; $j < $num_com_barche ; $j++) {
								if($lista_barche[$i]['id'] == $com_barche[$j]['id_transfer']) {
									$riga[$num_righe]['commenti'] .= '<tr><td class="td_com" colspan="'.$colspan_com.'">';
									$riga[$num_righe]['commenti'] .= '<span class="nome_x_barche">'.$lista_barche[$i]['nominativo'].'</span> <span class="testo_com">'.str_show($com_barche[$j]['testo']).'</span>';
									$riga[$num_righe]['commenti'] .= '</td></tr>';
								}
							}
						}
					}
					if($i > 0) $num_righe++;
					
					for($i = 0 ; $i < $num_righe ; $i++) {
						echo '<tr class="riga_barca">';
						echo '<td>'.$riga[$i]['barca'].'</td>';
						echo '<td><b><a href="ge_trans.php?mp='.$info_pre['id'].'&amp;'.$riga[$i]['link'].'">'.date('H:i', $riga[$i]['ora']).'</a></b></td>';
						echo '<td>'.$riga[$i]['tragitto'].'</td>';
						echo '<td>'.$riga[$i]['nominativo'].'</td>';
						echo '<td style="text-align: center;">'.$riga[$i]['pax_totali'].'</td>';
						echo '</tr>';
						if(isset($riga[$i]['commenti'])) echo $riga[$i]['commenti'];
					}
					
					echo '</table>';
				}
				else echo '<p class="no_arrivi">NESSUN VIAGGIO CONFERMATO</p>';

				echo '</div>'; // Fine liste_box
				
			} // Fine lista arrivi Partenze
}
?>