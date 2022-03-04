<?php
session_start(); // Si lancia la sezione
require('funzioni.php');

// Se abbiamo solo il link ma non abbiamo inizializzato il transfer
if(isset($_GET['link']) && !isset($_SESSION['trans'])) {
	valori_default(formatta_salvataggio($_GET['link']));
}

// Se abbiamo già inizializzato il transfer
if(isset($_SESSION['trans']) && isset($_SESSION['trans'])) {

	$db = db_connect();
	
	
	$db->connection = NULL;
}

// Non si è trovato il transfer, informiamo e diamo la possibilità di inserire manualmente il link
else {
	
}

echo '<!doctype html>'.
	  '<html lang="fr">'.
	  '<head>'.
	  '<meta charset="UTF-8">'.
	  '<link rel="stylesheet" href="layout/layout.css" type="text/css" />'.
	  '<link rel="icon" href="favicon.png" type="image/png" />'.
	  '<meta name="author" content="michele" />'.
	  '<meta name="copyright" content="Michele Sessa" />'.
	  '<meta name="robots" content="none" />';

echo '<title>';
if(isset($_POST['nome'])) {
	echo formatta_visualizzazione($info_pre['nome']) . ' - MODIFICA TRANSFER';
}
else 							echo 'Gestion des transferts';
echo '</title>';

echo '</head>';
echo '<body>';

echo '<div id="corpo_a">';

// Inizio formulario
echo '<form name="dati" action="transfer.php" method="post" enctype="multipart/form-data">';

echo '</form>';

echo '</div>'; // Fine corpo_a

echo '</body></html>';
?>