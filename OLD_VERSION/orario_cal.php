<?php
session_start(); // Si lancia la sezione
require('funzioni_admin.php');

if(isset($_SESSION['tipo_user']) && $_SESSION['tipo_user'] == 0) { // Si verifica che sia l'amministratore

	$sql_agg_cam = 'INSERT INTO transfer (id, ora_barca_arr_cal, ora_barca_par_cal) VALUES ';

	$db = db_connect();
	
	$request = $db->query('SELECT * FROM transfer ORDER BY id');
	$all_trans = $request->fetchAll(PDO::FETCH_ASSOC);
	$num_trans = count($all_trans);
	
	$db->connection = NULL;
	
	for($i = 0 ; $i < $num_trans ; $i++) {
		if($i != 0) $sql_agg_cam .= ',';
		$sql_agg_cam .= '('.$all_trans[$i]['id'].',';
		
		$ora_barca_arr_cal = orario_arrivo($all_trans[$i]['porto_partenza_arr'], $all_trans[$i]['porto_arrivo_arr'], $all_trans[$i]['ora_barca_arr'], $all_trans[$i]['barca_arr']);
echo 'ARR ora partenza : '.$all_trans[$i]['ora_barca_arr'].' - calcolata : ';
echo $ora_barca_arr_cal.'<br />';
		if($ora_barca_arr_cal != NULL) $sql_agg_cam .= $ora_barca_arr_cal.',';
		else 									 $sql_agg_cam .= 'NULL,';
		
		$ora_barca_par_cal = orario_arrivo($all_trans[$i]['porto_partenza_par'], $all_trans[$i]['porto_arrivo_par'], $all_trans[$i]['ora_barca_par'], $all_trans[$i]['barca_par']);
		if($ora_barca_par_cal != NULL) $sql_agg_cam .= $ora_barca_par_cal;
		else 									 $sql_agg_cam .= 'NULL';
echo 'PAR ora partenza : '.$all_trans[$i]['ora_barca_par'].' - calcolata : ';
echo $ora_barca_par_cal.'<br />';

		$sql_agg_cam .= ')';
	}
echo $sql_agg_cam;
	// Se ci sono aggiornamenti aggiorniamo il database
	if($num_trans > 0) {
		$sql_agg_cam .= 'ON DUPLICATE KEY UPDATE ora_barca_arr_cal=VALUES(ora_barca_arr_cal),ora_barca_par_cal=VALUES(ora_barca_par_cal);';
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
	echo $num_trans.' orari transfer calcolati';

} // Fin de "Si l'admin s'est bien identifié"

else { // Si pass ou pseudo n'existent pas on renvoie à la page de login administrateur
	header('Location: index.php');
}
?>