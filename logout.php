<?php
session_start(); // Si lancia la sezione

// Si verifica che ci sia effettivamente una sessione in corso
if(isset($_SESSION['pseudo'])) { 
 	$_SESSION = array();
 	session_destroy();
}

// Si ridirige sulla pagina di login
header('Location: index.php');
?>