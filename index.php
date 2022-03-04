<?php

// Si lancia la sezione
session_start();

/* STORICO VERSIONI
25/05/18 - v. 0.0.3 DAY7
PRIMA PROVA
12/07/18 - v. 0.1 LATE CHECK-OUT
TANTISSIMI BUGFIXES
AGGIUNTA FUNZIONALITA VARIE
VERSIONE STABILE
21/07/18 - v. 0.2 NTM
SEPARATI ORARI DI ARRIVO ROTATION E FABIRS PER EVITARE RITARDI
*/


$erreur = 0;
/*
Error Codes :
0 = No errors
1 = Empty fields
2 = Incorrect fields
*/

// If user clicked on the submit button
if(isset($_POST['login'])) {
	$pseudo = htmlspecialchars($_POST['pseudo']);
	$pass = htmlspecialchars($_POST['pass']);
	
	// If there is empty fields we set the error code
	if($pseudo == '' || $pass == '') $erreur = 1;
	
	else {
		// We initialize the valid login info for all the users
		$usernames = array('admin', 'governante', 'facchini', 'fabir3', 'fabir4', 'elicompany', 'massimi', 'tommaso');
		$passwords = array('admin', 'governante', 'facchini', 'fabir3', 'fabir4', 'elicompany', 'massimi', 'tommaso');
		$num_users = count($usernames);

		$redirect  = array('lista_trans.php', 'governante.php', 'facchini.php', 'mezzi.php', 'mezzi.php', 'taxi.php', 'taxi.php', 'taxi.php');
		
		// We set the error as incorrect user/password
		$erreur = 2;
		for($i = 0 ; $i < $num_users ; $i++) {
			if($pseudo == $usernames[$i] && $pass == $passwords[$i]) {
				$erreur = 0;
				
				// We set user info
				$_SESSION['tipo_user'] = $i;
				$_SESSION['pseudo'] = $pseudo;
				$_SESSION['pass']	  = $pass;				
				break;
			}
		}
	}
	
	if($erreur == 0) { // Tout s'est bien passé, on renvoie l'admin à la page qu'il a choisie

		// Inizializziamo i valori di default
		require('config.php');
		valori_default();
		header('Location: '.$redirect[$_SESSION['tipo_user']]);
	}
}
?><!DOCTYPE html>
<html lang="it" >
	<head>
		<title>Identificazione Amministratore</title>
		<link rel="icon" href="favicon.png" type="image/png" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="content-language" content="it" />
		<link rel="stylesheet" href="layout/identification.css" type="text/css" />
		<meta name="author" content="michele" />
		<meta name="copyright" content="Michele Sessa" />
		<meta name="robots" content="none" />
	</head>
	<body>

	<div id="head">
		<h1>7DAYS</h1>
		<p id="admin">v. 1 ALPHA!</p>
		<p>HOTEL NAME HERE</p>
	</div>

	<div id="login">
 	
		<form action="index.php" method="post">
			<fieldset>
				<legend>Identificarsi</legend>
				<?php if($erreur == 1) { ?><p class="messageBad">Tutti i campi sono obligatori</p> <?php }
				elseif($erreur == 2) { ?><p class="messageBad">Password o username incorretti</p> <?php } ?>
				<p class="infoChamps"><label for="pseudo">Username : </label><input type="text" name="pseudo" id="pseudo" autofocus /></p>
				<p class="infoChamps"><label for="password">Password : </label><input type="password" name="pass" id="password" /></p>
				<br />
				<p><input class="bottone" type="submit" name="login" value="IDENTIFICARSI" /></p>
			</fieldset>
		</form>
	
	</div>

</body>
</html>