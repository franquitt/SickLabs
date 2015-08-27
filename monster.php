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
  $globant=$user;
  $labid=0;
  if(isset($_GET['labid'])){
    $labid= $_GET['labid'];
    if(is_intF($labid))
      $labid= $_GET['labid'];
    else
      $labid=-1;   
  }
  else
    $labid=-1; 

  if($labid==-1){
    $consulta=mysqli_query($coneccion, "SELECT lab FROM (SELECT id AS idlabs FROM (SELECT id AS idciu FROM ciudades WHERE owner='$idUser') AS CIUDAD, labs WHERE ciudad=CIUDAD.idciu) AS LAB, monstruos WHERE monstruos.lab= LAB.idlabs LIMIT 1;");
    $miniMonster=mysqli_fetch_array($consulta);
    $labid = $miniMonster["lab"];
  }
  $consulta=mysqli_query($coneccion, "SELECT * FROM labs WHERE id = '$labid';");
  $laboratorio=mysqli_fetch_array($consulta);
  $consulta=mysqli_query($coneccion, "SELECT * FROM monstruos WHERE lab = '$labid';");
  $monster=mysqli_fetch_array($consulta);
  $ciudadid=$laboratorio['ciudad'];
  $consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE id = '$ciudadid';");
  $ciudad=mysqli_fetch_array($consulta);
  if($ciudad['owner']!=$user['id'])
    header("Location: home.php");

}
$consulta=mysqli_query($coneccion, "SELECT * FROM notificaciones WHERE receptor='$idUser' AND visto=0;");
$countnotificaciones=mysqli_num_rows($consulta);
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="images/favicon.ico">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/core.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <title>SickLabs</title>

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
          <p class="navbar-text">MONSTERS</p>
        </div>
      
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge glyphicon glyphicon-globe"><?php echo $countnotificaciones;?></span><span class="caret"></span></a>
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
  <!-- ++++++++++++++++++++++++++++++++++++ MONSTRUO ++++++++++++++++++++++++++++++++++++ -->
    <div class="col-sm-4 panelmons">
      <div class="page-header">
        <h3 class="gris">Monstruo</h3>
      </div>
      <div class="well">
        <h3 class="pull-left" style="margin:10px"><small id="puntosactuales"><?php echo $laboratorio['puntosgeneticos'];?></small></h3>
        <img src="images/adn.png" class="pull-left" style="margin:5px;" width="40" height="40">
        <img src=<?php echo '"images/type_'.$monster["tipo"].'.png"';?> class="img-thumbnail img" width="487" height="480">

      </div>
    </div>
    <script type="text/javascript">
    window.monsterid=<?php echo $monster['id'];?>;

    function mandala(type){
      var xmlhttp;
      if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        }
      else
        {// code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
      xmlhttp.onreadystatechange=function()
        {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
          var data = JSON.parse(xmlhttp.responseText);
          console.log(data.mensaje);
          if(data.error=="false"){
            document.getElementById("msjwin").innerHTML=data.mensaje;
            $('#modalready').modal('show')
            refresh(data.newpoints, parseInt(data.newgenetics), type);
            //document.getElementById("energylost2").innerHTML=data.energyLose;
          }else{
            document.getElementById("msjlost").innerHTML=data.mensaje;
            $('#modalerror').modal('show')
          }             
        }
      }         
      xmlhttp.open("GET","addpoints.php?monsterid="+window.monsterid+"&type="+type,true);
      xmlhttp.send();
    }
    function refresh(newpoints, newgenetics, type){
      document.getElementById("puntosactuales").innerHTML=newgenetics;
      document.getElementById("bad"+type).innerHTML=newpoints*2;
      document.getElementById("cont"+type).innerHTML=newpoints;
      var types = ["fuerza", "velocidad", "garras", "veneno", "evasivo", "escamas", "resistencia"];
      for(var tipo in types){
        var cant = parseInt(document.getElementById("bad"+types[tipo]).innerHTML);
        if(cant>newgenetics){
          console.log("no puede aumentar "+types[tipo]+" porque sale "+cant+"y tiene "+newgenetics);
          document.getElementById("btn"+types[tipo]).className="btn btn-primary disabled";
        }
      }
    }
    </script>
  <!-- ++++++++++++++++++++++++++++++++++++ EXPERIENCIA ++++++++++++++++++++++++++++++++++++ -->
    <div class="col-sm-5 panelmons">
      <div class="page-header">
        <h3 class="gris">Puntos de Experiencia</h3>
      </div>  
      <div class="well" style="overflow: auto; height: 380px;">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4><?php
                if($monster["tipo"]==4||$monster["tipo"]==5)
                  echo '<span class="glyphicon glyphicon-star"></span>';
                 else
                  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                echo " Fuerza: <span id='contfuerza'>";
                echo $monster['fuerza']."</span>";
                $disabled="";
                if($monster['fuerza']!=0){
                  if($laboratorio['puntosgeneticos']<$monster['fuerza']*2)
                    $disabled=" disabled";}
                else{
                  if($laboratorio['puntosgeneticos']==0)
                     $disabled=" disabled";}
                ?></h4>
              </div>
              <div class="col-sm-3">
                  <button type="button" id="btnfuerza" onClick="mandala('fuerza')" class=<?php echo '"btn btn-primary'.$disabled.'"';?>><span class="glyphicon glyphicon-plus-sign"></span>  
                    <span class="badge" id="badfuerza" ><?php if($monster['fuerza']!=0)echo $monster['fuerza']*2;else echo "1";?></span>
                  </button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>
                <?php
                if($monster["tipo"]==1||$monster["tipo"]==2||$monster["tipo"]==3)
                  echo '<span class="glyphicon glyphicon-star"></span>';
                 else
                  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                echo " Velocidad: <span id='contvelocidad'>";
                echo $monster['velocidad']."</span>";
                $disabled="";
                if($monster['velocidad']!=0){
                  if($laboratorio['puntosgeneticos']<$monster['velocidad']*2)
                    $disabled=" disabled";}
                else{
                  if($laboratorio['puntosgeneticos']==0)
                     $disabled=" disabled";}
                ?></h4>
              </div>
              <div class="col-sm-3">
                  <button type="button" id="btnvelocidad" onClick="mandala('velocidad')" class=<?php echo '"btn btn-primary'.$disabled.'"';?>><span class="glyphicon glyphicon-plus-sign"></span>  
                    <span class="badge" id="badvelocidad" ><?php if($monster['velocidad']!=0)echo $monster['velocidad']*2;else echo "1";?></span>
                  </button>
              </div>
            </div>
          </div>
        </div>
                <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>
                  <?php
                if($monster["tipo"]==2||$monster["tipo"]==3||$monster["tipo"]==5)
                  echo '<span class="glyphicon glyphicon-star"></span>';
                else
                  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                echo " Garras: <span id='contgarras'>";
                echo $monster['garras']."</span>";
                $disabled="";
                if($monster['garras']!=0){
                  if($laboratorio['puntosgeneticos']<$monster['garras']*2)
                    $disabled=" disabled";}
                else{
                  if($laboratorio['puntosgeneticos']==0)
                     $disabled=" disabled";}
                   ?></h4>
              </div>
              <div class="col-sm-3">
                  <button type="button" id="btngarras" onClick="mandala('garras')" class=<?php echo '"btn btn-primary'.$disabled.'"';?>><span class="glyphicon glyphicon-plus-sign"></span>  
                    <span class="badge" id="badgarras" ><?php if($monster['garras']!=0)echo $monster['garras']*2;else echo "1";?></span>
                  </button>
              </div>
            </div>
          </div>
        </div>
                <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>
                   <?php
                if($monster["tipo"]==1)
                  echo '<span class="glyphicon glyphicon-star"></span>';
                 else
                  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                echo " Veneno: <span id='contveneno'>";
                echo $monster['veneno']."</span>";
                $disabled="";
                if($monster['veneno']!=0){
                  if($laboratorio['puntosgeneticos']<$monster['veneno']*2)
                    $disabled=" disabled";}
                else{
                  if($laboratorio['puntosgeneticos']==0)
                     $disabled=" disabled";}?></h4>
              </div>
              <div class="col-sm-3">
                  <button type="button" id="btnveneno" onClick="mandala('veneno')" class=<?php echo '"btn btn-primary'.$disabled.'"';?>><span class="glyphicon glyphicon-plus-sign"></span>  
                    <span class="badge" id="badveneno" ><?php if($monster['veneno']!=0)echo $monster['veneno']*2;else echo "1";?></span>
                  </button>
              </div>
            </div>
          </div>
        </div>
                <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>
                  <?php
                if($monster["tipo"]==1||$monster["tipo"]==4)
                  echo '<span class="glyphicon glyphicon-star"></span>';
                 else
                  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                echo " Evasivo: <span id='contevasivo'>";
                echo $monster['evasivo']."</span>";
                $disabled="";
                if($monster['evasivo']!=0){
                  if($laboratorio['puntosgeneticos']<$monster['evasivo']*2)
                    $disabled=" disabled";}
                else{
                  if($laboratorio['puntosgeneticos']==0)
                     $disabled=" disabled";}?></h4>
              </div>
              <div class="col-sm-3">
                  <button type="button" id="btnevasivo" onClick="mandala('evasivo')" class=<?php echo '"btn btn-primary'.$disabled.'"';?>><span class="glyphicon glyphicon-plus-sign"></span>  
                    <span class="badge" id="badevasivo" ><?php if($monster['evasivo']!=0)echo $monster['evasivo']*2;else echo "1";?></span>
                  </button>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>
                  <?php
                if($monster["tipo"]==3||$monster["tipo"]==5)
                  echo '<span class="glyphicon glyphicon-star"></span>';
                 else
                  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                echo " Escamas: <span id='contescamas'>";
                echo $monster['escamas']."</span>";
                $disabled="";
                if($monster['escamas']!=0){
                  if($laboratorio['puntosgeneticos']<$monster['escamas']*2)
                    $disabled=" disabled";}
                else{
                  if($laboratorio['puntosgeneticos']==0)
                     $disabled=" disabled";}?></h4>
              </div>
              <div class="col-sm-3">
                  <button type="button" id="btnescamas" onClick="mandala('escamas')" class=<?php echo '"btn btn-primary'.$disabled.'"';?>><span class="glyphicon glyphicon-plus-sign"></span>  
                    <span class="badge" id="badescamas" ><?php if($monster['escamas']!=0)echo $monster['escamas']*2;else echo "1";?></span>
                  </button>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-sm-9">
                <h4>
                  <?php
                if($monster["tipo"]==2||$monster["tipo"]==4)
                  echo '<span class="glyphicon glyphicon-star"></span>';
                 else
                  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                echo " Resistencia: <span id='contresistencia'>";
                echo $monster['resistencia']."</span>";
                $disabled="";
                if($monster['resistencia']!=0){
                  if($laboratorio['puntosgeneticos']<$monster['resistencia']*2)
                    $disabled=" disabled";}
                else{
                  if($laboratorio['puntosgeneticos']==0)
                     $disabled=" disabled";}?></h4>
              </div>
              <div class="col-sm-3">
                  <button type="button" id="btnresistencia" onClick="mandala('resistencia')" class=<?php echo '"btn btn-primary'.$disabled.'"';?>><span class="glyphicon glyphicon-plus-sign"></span>  
                    <span class="badge" id="badresistencia" ><?php if($monster['resistencia']!=0)echo $monster['resistencia']*2;else echo "1";?></span>
                  </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


  <!-- ++++++++++++++++++++++++++++++++++++ LABORATORIOS ++++++++++++++++++++++++++++++++++++ -->
    <div class="col-sm-3 panelmons">
      <div class="page-header">
        <h3 class="gris">Laboratorios</h3>
      </div>
        <div class="panel-group" id="accordion">
          <?php
            $ciudadid=$laboratorio['ciudad'];
            $consulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE owner = '$idUser';");
            $index=1;
            while($ciudad=mysqli_fetch_array($consulta)){
              $in="";
              $subcityid= $ciudad['id'];
              if($ciudad['id']==$ciudadid)
                $in=" in";
              echo '<div class="panel panel-default"><div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$index.'"><h4 class="panel-title">';
              echo $ciudad['nombre'].'</h4></div><div id="collapse'.$index.'" class="panel-collapse collapse'.$in.'"><div class="panel-body"><table class="table table-hover table-bordered">';
              echo '<thead>
                    <tr>
                      <th>Lab</th>
                      <th>Tipo</th>
                      <th>Puntaje</th>
                    </tr>
                  </thead><tbody>';
              //glyphicon glyphicon-share-alt
              $subconsulta=mysqli_query($coneccion, "SELECT * FROM labs WHERE ciudad = '$subcityid';");
              while($lab=mysqli_fetch_array($subconsulta)){
                $actual="";
                if($lab['id']==$laboratorio['id']){
                  $actual=' class="success"';
                }
                $sub2consulta=mysqli_query($coneccion, "SELECT * FROM monstruos WHERE lab = '".$lab['id']."';");
                $monstruo=mysqli_fetch_array($sub2consulta);
                echo '<tr'.$actual.'><td>'.$lab['nombre'].'  <a href="labs.php?id='.$lab["id"].'"><span class="glyphicon glyphicon-share-alt"></span></a></td><td> <a href="monster.php?labid='.$monstruo["id"].'"><img src="images/iconB_'.$monstruo["tipo"].'.png" height="40" /></a></td><td>'.$monstruo['puntaje'].'</td></tr>';
              }
              echo '</tbody>
                </table>
              </div>
            </div>
          </div>';
              $index++;
            }
          ?>
        </div> 
    </div>
  </div>
<div id="modalready" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Genial !</h4>
      </div>
      <div class="modal-body">
        <h3 style="color:green"><div id="msjwin"></div></h3>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>
<div id="modalerror" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Error !</h4>
      </div>
      <div class="modal-body">
        <h3 style="color:red"><div id="msjlost"></div></h3>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>

</div>
</body>
</html>