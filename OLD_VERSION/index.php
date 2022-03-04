<?php
session_start(); // Si lancia la sezione
ini_set('display_errors', 'On');
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

$usernames = array('admin', 'governante', 'facchini', 'fabir3', 'fabir4', 'elicompany', 'massimi', 'tommaso');
$passwords = array('admin', 'governante', 'facchini', 'fabir3', 'fabir4', 'elicompany', 'massimi', 'tommaso');
$num_users = count($usernames);

$redirect  = array('lista_trans.php', 'governante.php', 'facchini.php', 'mezzi.php', 'mezzi.php', 'taxi.php', 'taxi.php', 'taxi.php');

$erreur = 0; // Code erreur :   -1 = L'admin s'est correctement identifié    1 = pseudo ou pass non remplis    3 = Mot de passe ou pseudo incorrects

if(isset($_POST['pseudo']) && isset($_POST['pass'])) { // Si les deux variables existent => l'utilisateur à appuyé sur le bouton
	$pseudo = htmlspecialchars($_POST['pseudo']);
	$pass = htmlspecialchars($_POST['pass']);
	
	if($pseudo == '' || $pass == '') $erreur = 1; // Si l'une des deux variables est vide on dit = "c'est pas bon"
	
	else {
		// Settiamo la password come non corretta
		$erreur = 3;
		for($i = 0 ; $i < $num_users ; $i++) {
			if($pseudo == $usernames[$i]) {
				if($pass == $passwords[$i]) {
					$erreur = 0;
					
					$_SESSION['tipo_user'] = $i;
					$_SESSION['pseudo'] = $pseudo;
					$_SESSION['pass']	  = $pass;
				}			
			}
		}
	}
	
	if($erreur == 0) { // Tout s'est bien passé, on renvoie l'admin à la page qu'il a choisie

		// Inizializziamo i valori di default
		require('funzioni_admin.php');
		valori_default();

		header('Location: '.$redirect[$_SESSION['tipo_user']]);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" >
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
<p id="admin">NTM (New Transfer Method) v. 0.2</p>
<p>HOTEL NAME HERE</p>
</div>

<div id="login">
 	
<form action="index.php" method="post">
<fieldset>
<legend>Identificarsi</legend>
<?php if($erreur == 1) { ?><p class="messageBad">Tutti i campi sono obligatori</p> <?php }
elseif($erreur == 3) { ?><p class="messageBad">Password o username incorretti</p> <?php } ?>
<p class="infoChamps"><label for="pseudo">Username : </label><input type="text" name="pseudo" id="pseudo" autofocus /></p>
<p class="infoChamps"><label for="password">Password : </label><input type="password" name="pass" id="password" /></p>
<br />
<p><input class="bottone" type="submit" value="IDENTIFICARSI" /></p>
</fieldset>
</form>

</div>

</body>
</html>