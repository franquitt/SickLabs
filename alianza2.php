<?php
session_start();
if(!isset($_SESSION['usuario']))
{
	//header("Location: login.php");
	echo "location: login.php";
}
else{
	include('seguridad.php');
	$coneccion = conectardb();
	$idUser = $_SESSION['usuario'];
	$consulta=mysqli_query($coneccion, "SELECT * FROM users WHERE id = '$idUser';");
	$user=mysqli_fetch_array($consulta);
	$consultalabs=mysqli_query($coneccion, "SELECT * FROM labs, (SELECT owner, nombre AS nombCIUDAD, id AS idCity FROM ciudades WHERE owner=$idUser) AS CIUDAD WHERE ciudad=CIUDAD.idCity AND oro>=30000");
	
  $error="";
  $select="";
  if(isset($_POST["crear"])){
    $nombrealianza = $_POST["alianzaname"];
    $idlab = $_POST["idlab"];
    if($nombrealianza!=""){
      $consultalab=mysqli_query($coneccion, "SELECT * FROM labs, (SELECT owner, nombre AS nombCIUDAD, id AS idCity FROM ciudades WHERE owner=$idUser) AS CIUDAD WHERE ciudad=CIUDAD.idCity AND oro>=30000 AND id='$idlab'");
      $countlab=mysqli_num_rows($consultalab);
      if($countlab==1){
        $consulta=mysqli_query($coneccion, "SELECT * FROM alianzas WHERE nombre = '$nombrealianza';");
        $countaly=mysqli_num_rows($consulta);
        if($countaly==0){
          
        }else{
          $error="Ya existe una alianza con ese nombre.";
        }
      }else{
        $error="Error buscando el laboratorio.";
      }
    }
    else{
      $error="Ingresa un nombre para tu alianza.";
    }
  }
	$puede= '"btn btn-success"';
  $puedebtn= 'Crear Alianza';
  $countlabs=mysqli_num_rows($consultalabs);
  if($countlabs==0){
    $puede= '"btn btn-danger disabled"';
    $puedebtn= 'No tienes los recursos necesarios';
  }else{
    while($laboratorio=mysqli_fetch_array($consultalabs)){
      $select.='<option value="'.$laboratorio["id"].'">'.$laboratorio["nombre"].' (en '.$laboratorio["nombCIUDAD"].' con '.$laboratorio["oro"].' de oro)';
    }

  }
}
?>
<!DOCTYPE html>
<html lang="es">
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
          <p class="navbar-text">ALIANZA</p>
        </div>
      
      <ul class="nav navbar-nav navbar-right disabled">
        <li class="dropdown"> <a href="#" class="dropdown-toggle disabled" data-toggle="dropdown"><span class="badge glyphicon glyphicon-globe">15</span><span class="caret"></span></a>
          <ul class="dropdown-menu disabled" role="menu">  
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
		<div class="container">
    	<div class="panel-heading">
    		<h3 class="text-center h3panel">Bienvenido <?php echo $user['nickname'];?>, te interesa crear una alianza?..</h3>
    	 <h4 class="text-center h3panel">30.000 de oro pa´h</h4>
      </div>

 		<form class="form-horizontal" method="POST" action="#">         
      <ul class="list-group">
	  		<li class="list-group-item ul">
	        <input type="text" name="alianzaname" class="form-control"  placeholder="Nombre de tu tu ciudad">
	      </li>
        <li class="list-group-item ul">
          <select name="idlab" class="form-control">
            <?php echo $select;?>
          </select>
        </li>
	      <li class="list-group-item ul">
          <h4 class="text-center h3panel"><?php echo $error;?></h4>
	      	<center><button type="submit" name="crear" class=<?php echo $puede;?>><?php echo $puedebtn;?></button></center>
        </li>         	  
    </form>
 	</div>
</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>
