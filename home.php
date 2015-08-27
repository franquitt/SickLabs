<?php
session_start();
if(!isset($_SESSION['usuario']))
{
	header("Location: login.php");
}
else{
	include('seguridad.php');
	$coneccion = conectardb();
	$idUser = $_SESSION['usuario'];
	$consulta=mysqli_query($coneccion, "SELECT * FROM users WHERE id = '$idUser';");
	$user=mysqli_fetch_array($consulta);
	$consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE owner = '$idUser';");
	
	$countciudades=mysqli_num_rows($consulta);
	if($countciudades==0){
		header("Location: startcity.php");
	}
	$consulta=mysqli_query($coneccion, "SELECT fuerza FROM (SELECT id AS idlabs FROM (SELECT id AS idciu FROM ciudades WHERE owner='$idUser') AS CIUDAD, labs WHERE ciudad=CIUDAD.idciu) AS LAB, monstruos WHERE monstruos.lab= LAB.idlabs;");
	
	$countmostros=mysqli_num_rows($consulta);
	if($countciudades==0 || $countmostros==0){
		header("Location: addmonster.php");
	}
	$consulta=mysqli_query($coneccion, "SELECT * FROM miembros WHERE iduser='$idUser' AND aceptado=0;");
	$countinvitaciones=mysqli_num_rows($consulta);
	$consulta=mysqli_query($coneccion, "SELECT * FROM notificaciones WHERE receptor='$idUser' AND visto=0;");
	$countnotificaciones=mysqli_num_rows($consulta);
}
?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"> 
<title>SickLabs</title>
<!-- Bootstrap -->

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/core.css" rel="stylesheet">
<head>
	<!--EMPIEZA LA NAVBAR-->    
	<nav class="navbar navbar-core" role="navigation">
	<div class="container-fluid">
	<!-- Brand and toggle get grouped for better mobile display -->

	<div class="navbar-header">
	  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
	  <a class="navbar-brand brand" href="home.php">SickLabs</a> </div>
	
	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse navheadings" id="bs-example-navbar-collapse-1">
	  
	  <ul class="nav navbar-nav">
		
		<li ><a href="alianza.php">Alianza</a></li>
		
		<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown">Acerca De<span class="caret"></span></a>
		  <ul class="dropdown-menu" role="menu">
			<li><a href="informe.pdf">Informe Base de datos</a></li>
			<li><a href="ayuda.html">Ayuda</a></li>
		  </ul>
		</li>  
	  </ul>
		<div class="navbar-center">
		  <p class="navbar-text">HOME</p>
		</div>
	  
	  <ul class="nav navbar-nav navbar-right">
		<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge glyphicon glyphicon-globe">15</span><span class="caret"></span></a>
		  <ul class="dropdown-menu" role="menu">  
			<li><a href="#">Mensajes</a></li>
			<li class="divider"></li>
			<li><a href="#">Sucesos</a></li>
		  </ul>
		</li>
		
		<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span><span class="caret"></span></a>
		  <ul class="dropdown-menu" role="menu">
			<li><a href="#">Editar Perfil</a></li>
			<li><a href="logout.php">Cerrar sesion</a></li>
		  </ul>
		</li>
	  </ul>
	  
	</div>
	<!-- /.navbar-collapse --> 
  </div>
  <!-- /.container-fluid --> 
</nav>
<!--TERMINA LA NAVBAR-->
</head>
<body>
	<?php
		/*if($countnotificaciones+$countinvitaciones ==1)
			echo "Tienes ".($countnotificaciones+$countinvitaciones)." notificacion nueva";
		else
			echo "Tienes ".($countnotificaciones+$countinvitaciones)." notificaciones nuevas";
			*/
		?>
	<center>
	<div class="container-fluid">
	<div class="row menus">
		  <div class="col-sm-6 col-md-4">
			<a href="labs.php">
			  <div  class="jumbotron background-lab">
				  <h3 class="h3menu">Lab</h3>
			  </div>
			</a>
		  </div>
		  <div class="col-sm-6 col-md-4">
			<a href="monster.php">
			  <div  class="jumbotron background-monster">
				<h3 class="h3menu">Montser</h3>
			  </div>
			</a>
		  </div>
		  <div class="col-sm-6 col-md-4">
			<a href="world.php">
			  <div class="jumbotron background-world">
				  <h3 class="h3menu">World</h3>
			  </div>
			</a>
		  </div>
	</div>
	</div>
	</center>	
</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>