<?php

function preconectardb(){
	date_default_timezone_set('America/Argentina/Buenos_Aires');
	return mysqli_connect("localhost", "root", "password", "sicklabs");
}

function conectardb(){
	$coneccion=preconectardb();
	mysqli_set_charset($coneccion, 'utf8');
	return $coneccion;
}
function encriptar($stringAEncriptar) {
	return md5($stringAEncriptar);
}

function logout() {
	session_start();
	session_destroy();
	
	header('location: login.php');
}
function is_intF($input){
    return(ctype_digit(strval($input)));
}
function validacionemail($email) {
	if (@ereg("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$", $email)) {
		return true;
	} else {
		return false;
	}
}
?>