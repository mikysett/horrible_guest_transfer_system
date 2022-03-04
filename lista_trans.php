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

?><!DOCTYPE html>
<html lang="it">
	<head>
		<title>LISTA TRANSFER</title>
		<link rel="stylesheet" href="layout/admin.css" type="text/css" />
		<link rel="stylesheet" href="layout/jquery.qtip.min.css" type="text/css" />
		<link rel="icon" href="favicon.png" type="image/png" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0">	
		<meta name="author" content="michele" />
		<meta name="copyright" content="Michele Sessa" />
		<meta name="robots" content="none" />
		<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	</head>
	<body>
		<nav id="head_menu">
			<button class="info_generiche xqtip">i
			</button>
			<div class="menu_top">
				<a href="lista_trans.php">HOME</a>
			</div>
			<div class="menu_top">
				<p class="titolo_menu">LISTE</p>
				<div class="voci_menu" id="list_view">
					<p class="titolo_lista">LISTE HOTEL</p>
					<button class="tipo_lista lista_sel" value="0">RECEPTION</button>
					<button class="tipo_lista" value="1">FACCHINI</button>
					<button class="tipo_lista" value="2">MEZZO</button>
					<!--<button class="tipo_lista" value="3">FABIR 3</button>
					<button class="tipo_lista" value="4">FABIR 4</button>-->
					<button class="tipo_lista" value="5">ELICOMPANY</button>
					<p class="titolo_lista">LISTE TAXI</p>
					<button class="tipo_lista" value="6">MASSIMI</button>
					<button class="tipo_lista" value="7">TOMMASO</button>
				</div>
			</div>
			<div class="menu_top">
				<p class="titolo_menu">OPZIONI <span id="num_hide"></span></p>
				<div class="voci_menu" id="trans_type_view">
					<p id="type_view_opz">
						<button class ="opz_si" value="0">PERSONE</button>
						<button class ="opz_si" value="2">IN ESCLUSIVA</button>
						<button class ="opz_si" value="1">MERCE</button>
						<button class ="opz_si" value="3">DIPENDENTI</button>
						<button class ="opz_si" value="4">VILLE</button>
						<button class ="opz_si" value="5">PROPRIETARI</button>
						<button class ="opz_si" value="6">RISTORANTE</button>
						<button class ="opz_si" value="7">ALTRO</button>
					</p>
					<p class="titolo_lista"></p>
					<button class ="tipo_lista" value="-1">MOSTRA TUTTI</button>
					<button class ="opz_si" value="-2">NASCONDI TUTTI</button>
				</div>
			</div>
			<div class="menu_top">
				<p class="titolo_menu">AVANZATE</p>
				<div class="voci_menu">
					<button id="assegna_camere" class="tipo_lista" value="assegna">ASSEGNA CAMERE</button>
					<a class="tipo_lista" href="statistiche.php">STATISTICHE</a>
					<a class="tipo_lista" href="lista_trans.php?backup=1">SCARICA BACKUP</a>
					<a class="tipo_lista" href="disconnettersi.php">LOGOUT</a>
				</div>
			</div>
			<div class="menu_top">
				<a href="ge_trans.php">NUOVO TRANSFER</a>
			</div>
		</nav>
		
		<nav id="menu_nav">
			<button id="add_note_img">
			</button>
		
			<div id="box_nav">
				<div id="ricerca_data">
					<span id="ts_day_week"></span>
					<!--<input type="hidden" id="date_list_ts" name="date_list_ts" value="<?php echo $_SESSION['oggi'] ?>" />-->
					<input type="hidden" id="date_list_ts" name="date_list_ts" value="1532728800" />
					<input placeholder="DATA" type="text" name="data_cercata" class="field" id="date_list" value="" />
					<button class="change_day" id="set_day">Go</button>
					<button class="change_day" id="prev_day">&lt;</button>
					<button class="change_day" id="next_day">></button>
					
				</div>
				<div style="clear:both"></div>
			</div>
		</nav>
		
		<div id="corpo_a">
		

		
			<div id="liste_box">
			
				<div id="note_gg_box">
				</div>
			
			
			<?php
		
			$tipo = array('arr', 'par');
			$tipo_nome = array('arrivi', 'partenze');
			for($i = 0 ; $i < 2 ; $i++) {
				
				echo '<p class="title_list">';
					echo '<span>'.$tipo_nome[$i].'</span>';
					echo '<span class="ultima_mod_lista" id="ultima_mod_'.$tipo[$i].'"></span>';
				echo '</p>';
		
				echo '<div class="trans_box" id="box_'.$tipo[$i].'">';
					
					echo '<table class="lista_trans" id="lista_'.$tipo[$i].'">';
					
					echo '</table>';
				echo '</div>';
			}
			
			?>
			</div>


			<div id="mezzi_box">
				
				<div id="liste_mezzi_box">
					<div id="stato_mezzi_box">
						<div id="mezzo1" class="mezzo_cont"><span class="nome_mezzo">Fabir 3</span><span id="stato_mezzo1"></span></div>
						<div id="mezzo2" class="mezzo_cont"><span class="nome_mezzo">Fabir 4</span><span id="stato_mezzo2"></span></div>
						<p class="clx_float"></p>
					</div>
				
					<div id="scelta_mezzo">
						<button class="mezzo_sel mezzo_focus" value="0">Fabirs</button>
						<button class="mezzo_sel" value="1">Fabir 3</button>
						<button class="mezzo_sel" value="2">Fabir 4</button>
					</div>
					<div id="box_trans_mezzo">
					</div>
				</div>
			
			</div>



		</div>



		<script src="js/jquery-3.4.1.js"></script>
		<script src="js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
		<script src="js/moment.js"></script>
		<script src="js/moment-timezone.js"></script>
		<script src="js/jquery.qtip.min.js"></script>
		<script src="js/trans.class.js"></script>
		<script src="js/trans.initialize.js"></script>
		<script src="js/trans.reception.js"></script>
		<script src="js/trans.events.js"></script>
	</body>
</html><?php
} // Fine dello skip per il backup
} // Fin de "Si l'admin s'est bien identifié"

else { // Si pass ou pseudo n'existent pas on renvoie à la page de login administrateur
	header('Location: index.php');
}
?>