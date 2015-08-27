<?php
session_start();
if(!isset($_SESSION['usuario']))
{
header("Location: index.php");
}
else
{
$us = $_SESSION['usuario'];
require('seguridad.php');
$coneccion = conectardb();
$consulta = mysqli_query($coneccion, "SELECT * FROM users WHERE id = '$us'");
$row = mysqli_fetch_array($consulta);
}

$consultita = mysqli_query($coneccion, "SELECT * FROM labs WHERE id = '$us'");
$a = mysqli_fetch_array($consultita); 
  $oro = $a["oro"];
  $ene = $a["energia"];
  $int = $a["internos"];
  $gen = $a["puntosgeneticos"];
  $pob = $a["poblacionnivel"];
  $x = $a["x"];
  $y = $a["y"];
  $nombre = $a["nombre"];

?>


<!DOCTYPE html>
<html lang="en">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/core.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <title>SickLabs</title>
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
          <p class="navbar-text">LABORATORIOS</p>
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
<div class="container-fluid">
  <div class="row">
  <!-- ++++++++++++++++++++++++++++++++++++ Laboratorio ++++++++++++++++++++++++++++++++++++ -->
    <div class="col-sm-4 panelmons">
      <div class="page-header">
        <h3 class="gris"><?php echo $nombre?>
      </div>
      <div class="well">
        <img src="images/laboratory_lab.jpg" class="img-thumbnail" style="width:400px; height:400px">

      </div>
      <div class="paddingyo">
        <h4 class="gris">Posicion en X: <?php echo $int?> Posicion en Y: <?php echo $y?></h4>
      </div>
    </div>
  <!-- ++++++++++++++++++++++++++++++++++++ EXPERIENCIA ++++++++++++++++++++++++++++++++++++ -->
    <div class="col-sm-5 panelmons">
      <div class="page-header">
        <h3 class="gris">Puntos de Experiencia</h3>
      </div>  
      <div class="well">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>Oro: <?php echo $oro?> </h4>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>Energia: <?php echo $ene?> </h4>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>Internos: <?php echo $int?> </h4>
              </div>
              <div class="col-sm-3">
                  <button type"button" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span>  <span class="badge">1</span></button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>Puntos Geneticos: <?php echo $int?> </h4>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>Nivel de Poblacion: <?php echo $pob?> </h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <!-- ++++++++++++++++++++++++++++++++++++ LABORATORIOS ++++++++++++++++++++++++++++++++++++ -->
    <div class="col-sm-3 panelmons" >
      <div class="page-header">
        <h3 class="gris">Laboratorios</h3>
      </div>
        <div class="panel-group" id="accordion">
          <div class="panel panel-default">
            <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse1">
              <h4 class="panel-title">
                Tierra
              </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse in">
              <div class="panel-body">
                <table class="table table-hover table-bordered">
                  <thead>
                    <tr>
                      <th>Lab</th>
                      <th>Puntaje</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Hydra</td>
                      <td>55040</td>
                    </tr>
                    <tr>
                      <td>Cerberus</td>
                      <td>35432</td>
                    </tr>
                    <tr>
                      <td>Caronte</td>
                      <td>85234</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse2">
              <h4 class="panel-title">
                Marte
              </h4>
            </div>
            <div id="collapse2" class="panel-collapse collapse">
              <div class="panel-body">
                <table class="table table-hover table-bordered">
                  <thead>
                    <tr>
                      <th>Lab</th>
                      <th>Puntaje</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Nazi</td>
                      <td>103040</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse3">
              <h4 class="panel-title">
                Jupiter
              </h4>
            </div>
            <div id="collapse3" class="panel-collapse collapse">
              <div class="panel-body">
                <table class="table table-hover table-bordered">
                  <thead>
                    <tr>
                      <th>Lab</th>
                      <th>Puntaje</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Allies</td>
                      <td>30459</td>
                    </tr>
                  </tbody>
                </table>
              </div>
          </div>
        </div> 
    </div>
  </div>
</div>
</body>
</html>