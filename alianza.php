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
  $stmt = $coneccion->prepare("SELECT * FROM users WHERE id = ?;");
  $stmt->bind_param('i', $idUser);
  $stmt->execute();
  $result = $stmt->get_result();
  $user=mysqli_fetch_array($result);

  $stmt = $coneccion->prepare("SELECT * FROM labs, (SELECT owner, nombre AS nombCIUDAD, id AS idCity FROM ciudades WHERE owner = ?) AS CIUDAD WHERE ciudad=CIUDAD.idCity AND oro>=30000");
  $stmt->bind_param('i', $idUser);
  $stmt->execute();
  $consultalabs = $stmt->get_result();
  $error="";
  $select="";
  $mandar="";
  if(isset($_POST["crear"])){
    $nombrealianza = $_POST["alianzaname"];
    $idlab = $_POST["idlab"];
    if($nombrealianza!=""){
      //$consultalab=mysqli_query($coneccion, "SELECT * FROM labs, (SELECT owner, nombre AS nombCIUDAD, id AS idCity FROM ciudades WHERE owner=$idUser) AS CIUDAD WHERE ciudad=CIUDAD.idCity AND oro>=30000 AND id='$idlab'");
      $stmt = $coneccion->prepare("SELECT * FROM labs, (SELECT owner, nombre AS nombCIUDAD, id AS idCity FROM ciudades WHERE owner = ?) AS CIUDAD WHERE ciudad=CIUDAD.idCity AND oro>=30000 AND id= ?");
      $stmt->bind_param('is', $idUser, $idlab);
      $stmt->execute();
      $consultalab = $stmt->get_result();
      $countlab=mysqli_num_rows($consultalab);
      if($countlab==1){
        $laboratorio=mysqli_fetch_array($consultalab);
        $consulta=mysqli_query($coneccion, "SELECT * FROM alianzas WHERE nombre = '$nombrealianza';");
        $countaly=mysqli_num_rows($consulta);
        if($countaly==0){
              $stmt = $coneccion->prepare('INSERT INTO alianzas(nombre) VALUES(?)');
              $stmt->bind_param('s',$nombrealianza);
              $stmt->execute();

              $stmt = $coneccion->prepare('SELECT * FROM alianzas WHERE nombre =?');
              $stmt->bind_param('s',$nombrealianza);
              $stmt->execute();

              $result = $stmt->get_result();
              $alianza=mysqli_fetch_array($result);
              $stmt = $coneccion->prepare('INSERT INTO miembros(iduser, idalianza, rango, aceptado) VALUES(?, ?, ?, ?)');
              $rango=1;
              $stmt->bind_param('iiii',$idUser, $alianza["id"], $rango, $rango);
              $stmt->execute();

              $stmt = $coneccion->prepare('UPDATE labs SET oro =? WHERE id=?');
              $newgold = $laboratorio['oro']-30000;
              $stmt->bind_param('ii', $newgold, $laboratorio['id']);
              $stmt->execute();
        }else{
          $error="<div class='alert alert-danger' role='alert'>Ya existe una alianza con ese nombre.</div>";
        }
      }else{
        $error="<div class='alert alert-danger' role='alert'>Error buscando el laboratorio.</div>";
      }
    }
    else{
      $error="<div class='alert alert-danger' role='alert'>Ingresa un nombre para tu alianza.</div>";
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
  $consulta=mysqli_query($coneccion, "SELECT * FROM miembros WHERE iduser = '$idUser' AND aceptado=1;");
  $titulo="Alianza";
  $countalied=mysqli_num_rows($consulta);
  if($countalied==0){
    $mandar='<div class="container">
    <!--Mensaje de bienvenida, varia segun el caso-->
      <br>
      <br>
      <br>
      <br>
    <!--Aparece en caso de no tener Alianza-->
      <div class="col-md-12">
        <button type="button" class="form-control btn btn-success" data-toggle="modal" data-target="#myModal">Crea una Alianza</button>
        <br><br>'.$error.'
      </div>
    </div>';
  }else{
    $countRow=0;

    $membresia=mysqli_fetch_array($consulta);

    $stmt = $coneccion->prepare('SELECT * FROM alianzas WHERE id = ? ');
    $stmt->bind_param('i', $membresia["idalianza"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $alianza=mysqli_fetch_array($result);
    $titulo=$alianza["nombre"];
    $mandar='    <div class="col-md-12">';
    if($membresia["rango"]=="1"||$membresia["rango"]=="2")
      $mandar.=' 
        <div class="input-group">
          <input id="inputsugerencias" type="text" class="form-control" placeholder="Buscar nuevos integrantes" onKeyUp="checkUsers()">
          <span class="input-group-btn">
            <button class="btn btn-success" onClick="enviarInvitacion()" type="button">Agregar</button>
          </span>
          </div>
          <div id="sugerencias">
          </div>';
    $mandar.=' 
    <div class="boton">
    </div>
      <div role="tabpanel" class="tab-pane active" id="home">
      <table class="table table-hover" id="tablausers">
        <thead>
          <tr class="success msn">
           <th><h4>Posicion</h4></th>
            <th><h4>Nombre</h4></th>
            <th><h4>Nº Laboratorios</h4></th>
            <th><h4>Rango</h4></th>
            <th><h4>Nivel</h4></th>';

            if($membresia["rango"]=="1")
              $mandar.='<th><h4>Acciones</h4></th>';
           $mandar.='</tr> 
        </thead>
        <tbody>';
        $consulta=mysqli_query($coneccion, "SELECT iduser, nickname, nivel, rango FROM miembros, users WHERE idalianza = '".$membresia["idalianza"]."' AND aceptado=1 AND users.id = miembros.iduser ORDER BY rango ASC;");
        while($submembresia=mysqli_fetch_array($consulta)){
          $countRow++;
           $mandar.='<tr class="success">
            <td>
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  Ciudades
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
                $subconsulta=mysqli_query($coneccion, "SELECT * FROM ciudades WHERE owner='".$submembresia["iduser"]."';");
                while($ciudades=mysqli_fetch_array($subconsulta)){
                  $mandar.='<li><a href="world.php?cx='.$ciudades["x"].'&cy='.$ciudades["y"].'">Ir a '.$ciudades["nombre"].' ('.$ciudades["x"].', '.$ciudades["y"].')  <span class="glyphicon glyphicon-globe"></span></a></li>';
                }
                $mandar.='</ul>
              </div>
            </td>
            <td>'.$submembresia["nickname"].'</td>';
            $subconsulta=mysqli_query($coneccion, "SELECT * FROM (SELECT id AS idciu FROM ciudades WHERE owner='".$membresia["iduser"]."') AS CIUDAD, labs WHERE ciudad=CIUDAD.idciu;");
            $mandar.='<td>'.mysqli_num_rows($subconsulta).'</td>
            <td><div id="contenedorrango'.$countRow.'">';
            switch($submembresia["rango"]){
            case "1":
              $mandar.='Líder';
              break;
              case "2":
              $mandar.='Oficial'; 
              break;
              case "3":
              $mandar.='Miembro';
              break;
            }
             $mandar.='</div></td>  
            <td>'.$submembresia["nivel"].'</td>';
            if($submembresia["iduser"]!=$idUser && $membresia["rango"]==1){
             
              $mandar.='<td>';
              if($submembresia["rango"]==3||$submembresia["rango"]==2){
                //acordarse de editar el estilo en la funcion javascript
                
                  if($submembresia["rango"]==2){
                    $mandar.='<div id="contenedormain'.$countRow.'"><table><tr class="input-group-btn"><td style="margin-right: 1%;"><div id="contenedorasc'.$countRow.'"></div></td>';
                    $mandar.='<td style="margin-right: 1%;"><div id="contenedordeg'.$countRow.'"><button class="btn btn-warning" role="button" onClick="descender(\''.$submembresia["nickname"].'\', \''.$submembresia["iduser"].'\', \''.$countRow.'\')" aria-expanded="false">Degradar</button></div></td>';
                  }else{
                    $mandar.='<div id="contenedormain'.$countRow.'"><table><tr class="input-group-btn"><td style="margin-right: 1%;"><div id="contenedorasc'.$countRow.'"><button onClick="ascender(\''.$submembresia["nickname"].'\', \''.$submembresia["iduser"].'\', \''.$countRow.'\')" class="btn btn-info"role="button" aria-expanded="false">Ascender</button></div></td>';
                    $mandar.='<td><div id="contenedordeg'.$countRow.'"></div></td>';
                  }
                $mandar.='<td style="margin-right: 1%;"><button onClick="borrar(\''.$submembresia["nickname"].'\', \''.$submembresia["iduser"].'\', \''.$countRow.'\')" class="btn btn-danger" role="button" aria-expanded="false">Borrar</button></td></tr></table></div>';}
              
            $mandar.='</td>';

            }else
              $mandar.='<td></td>';
          $mandar.='</tr>';
        }

          $mandar.='
        </tbody>
      </table>
      </div>
    </div>
    <div class="col-md-12">
  <button class="btn btn-danger" onClick="salir(\''.$user["nickname"].'\')" style="float: right;">Salirse</button>
</div>';
  }
  $consulta=mysqli_query($coneccion, "SELECT * FROM notificaciones WHERE receptor='$idUser' AND visto=0;");
$countnotificaciones=mysqli_num_rows($consulta);
}
?><!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"> 
<title>SickLabs</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/core.css" rel="stylesheet">
<head>
    <script type="text/javascript">
    function checkName(){
      var input = document.getElementById("recipient-name");
      var div = document.getElementById("divmensaje");
      if(input.value==""){
        div.innerHTML= '<div class="alert alert-danger">Ingresa un nombre para tu alianza</div>';
      }
      else{

        div.innerHTML= '';
        var xmlhttp;
        if (window.XMLHttpRequest){
            xmlhttp=new XMLHttpRequest();
        }else{
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
          {
            if(xmlhttp.responseText=="0"){
             div.innerHTML= '<div class="alert alert-success">Parece que ese nombre si esta disponible</div>';
            }else{
              div.innerHTML= '<div class="alert alert-danger">Ese nombre no se encuentra disponible</div>';
            }             
          }
        }         
      xmlhttp.open("GET","checkalianzaname.php?name="+input.value,true);
      xmlhttp.send();
      }
    }
   function salir(nombre){
      var title = document.getElementById("primetitle");
      var content = document.getElementById("primecontent");
      title.innerHTML="Salir de la alianza";
      content.innerHTML=nombre+" estas seguro que deseas salir de la alianza?";
      
      var boton= document.getElementById("primeconfirm");
      boton.onclick = function() { 
          var xmlhttp;
          if (window.XMLHttpRequest)            
            xmlhttp=new XMLHttpRequest();            
          else
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");            
          xmlhttp.onreadystatechange=function()
            {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
              
              if(xmlhttp.responseText.indexOf("exito")!=-1)
                   window.location="alianza.php";
              else {
                content.innerHTML=xmlhttp.responseText;    
                boton.onclick = function() {
                   $('#modalprime').modal('hide')
                };  
              }                
            }
          }         
          xmlhttp.open("GET","kickmealianza.php?alianza="+<?php echo $membresia["idalianza"];?>,true);
          xmlhttp.send();
      };  
      $('#modalprime').modal('show')
   }
   function ascender(nombre, id, index){
      var title = document.getElementById("primetitle");
      var content = document.getElementById("primecontent");
      title.innerHTML="Ascender Usuario";
      content.innerHTML="Esta a punto de ascender a "+nombre+" en la alianza!";
      
      var boton= document.getElementById("primeconfirm");
      boton.onclick = function() { 
          var xmlhttp;
          if (window.XMLHttpRequest)            
            xmlhttp=new XMLHttpRequest();            
          else
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");            
          xmlhttp.onreadystatechange=function()
            {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
              content.innerHTML=xmlhttp.responseText;
              boton.value="Aceptar";
              if(xmlhttp.responseText.indexOf("ha sido ascendido en la alianza")!=-1)
                document.getElementById("contenedordeg"+index).innerHTML='<td style="margin-right: 1%;"><button onClick="descender(\''+nombre+'\', \''+id+'\', \''+index+'\')" class="btn btn-warning"role="button" aria-expanded="false">Degradar</button></td>';
                document.getElementById("contenedorasc"+index).innerHTML='';
                document.getElementById("contenedorrango"+index).innerHTML='Oficial';
                boton.onclick = function() {
                  $('#modalprime').modal('hide')
                };                        
            }
          }         
          xmlhttp.open("GET","ascalianza.php?name="+nombre+"&id="+id+"&alianza="+<?php echo $membresia["idalianza"];?>,true);
          xmlhttp.send();
      };  
      $('#modalprime').modal('show')
   }
   function descender(nombre, id, index){
      var title = document.getElementById("primetitle");
      var content = document.getElementById("primecontent");
      title.innerHTML="Degradar Usuario";
      content.innerHTML="Esta a punto de degradar a "+nombre+" en la alianza!";
      
      var boton= document.getElementById("primeconfirm");
      boton.onclick = function() { 
          var xmlhttp;
          if (window.XMLHttpRequest)            
            xmlhttp=new XMLHttpRequest();            
          else
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");            
          xmlhttp.onreadystatechange=function()
            {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
              content.innerHTML=xmlhttp.responseText;
              boton.value="Aceptar";
              if(xmlhttp.responseText.indexOf("ha sido degradado en la alianza")!=-1)
                document.getElementById("contenedordeg"+index).innerHTML='';
                document.getElementById("contenedorasc"+index).innerHTML='<button onClick="ascender(\''+nombre+'\', \''+id+'\', \''+index+'\')" class="btn btn-info"role="button" aria-expanded="false">Ascender</button>';
                document.getElementById("contenedorrango"+index).innerHTML='Miembro';
                boton.onclick = function() {
                  $('#modalprime').modal('hide')
                };                        
            }
          }         
          xmlhttp.open("GET","descalianza.php?name="+nombre+"&id="+id+"&alianza="+<?php echo $membresia["idalianza"];?>,true);
          xmlhttp.send();
      };  
      $('#modalprime').modal('show')
   }
   function borrar(nombre, id, index){
      var title = document.getElementById("primetitle");
      var content = document.getElementById("primecontent");
      title.innerHTML="Borrar Usuario";
      content.innerHTML="Esta a punto de borrar a "+nombre+" de la alianza!";
      
      var boton= document.getElementById("primeconfirm");
      boton.onclick = function() { 
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
              content.innerHTML=xmlhttp.responseText;
              boton.value="Aceptar";
              if(xmlhttp.responseText.indexOf("ha sido expulsado de la alianza")!=-1)
                document.getElementById("tablausers").deleteRow(index);
                boton.onclick = function() {
                  $('#modalprime').modal('hide')
                };                        
            }
          }         
          xmlhttp.open("GET","kickalianza.php?name="+nombre+"&id="+id+"&alianza="+<?php echo $membresia["idalianza"];?>,true);
          xmlhttp.send();
      };  
      $('#modalprime').modal('show')
   }
   
    
    function put(helow){
      var input = document.getElementById("inputsugerencias");
      var div = document.getElementById("sugerencias");
      div.innerHTML= '';
      input.value=helow;
    }
    function checkUsers(){
      var input = document.getElementById("inputsugerencias");
      var out="";
      out='<table class="table table-hover" id="sugerencias"><thead><tr class="success msn"><td><center><h4>Usuarios encontrados</h4></center></td></tr></thead>';
      var div = document.getElementById("sugerencias");
      if(input.value==""){
        div.innerHTML= '';
      }
      else{
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
            if(data.usuarios.length==0){
              div.innerHTML="";
            }else{
              for(var k in data.usuarios) {
                console.log(k+"sadasd"+data.usuarios[k]);
                  out+='<tr class="success msnsuger"><td onClick="put(\''+data.usuarios[k]+'\')"><center>'+data.usuarios[k]+'</center></td></tr>';

              }
              out+='</table>';
       
              div.innerHTML=out;
            }
             
                     
          }
        }         
        xmlhttp.open("GET","checkusername.php?name="+input.value+"&alianza="+<?php echo $membresia["idalianza"];?>,true);
        xmlhttp.send();
     
      }
    }
    function enviarInvitacion(){
      var div = document.getElementById("resultadoinvitacion");
      var input = document.getElementById("inputsugerencias");
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
          div.innerHTML="<center><h3>"+xmlhttp.responseText+"</h3></center>";
          input.value="";
        }
      }         
      xmlhttp.open("GET","sendinvite.php?name="+input.value+"&alianza="+<?php echo $membresia["idalianza"];?>,true);
      xmlhttp.send();
      
      $('#myModalSend').modal('show')
    }
    </script>
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
			  <p class="navbar-text"><?php echo $titulo;?></p>
			</div>
		  
		  <ul class="nav navbar-nav navbar-right">
			<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge glyphicon glyphicon-globe"><?php echo $countnotificaciones ?></span><span class="caret"></span></a>
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
	  </div>
	</nav>
	<!--TERMINA LA NAVBAR-->
</head>
<body>
    <?php echo $mandar;?>



<div class="modal fade" id="modalprime" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><div id="primetitle"></div></h4>
      </div> 
       <div class="modal-body" id="primecontent">
      </div>  
      <div class="modal-footer">
            <button type="button" id="primeconfirm" class="btn btn-info">Confirmar</button>
            <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
          </div>        
    </div>
  </div>
</div>

<div class="modal fade" id="myModalSend" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Pedido de alianza</h4>
      </div> 
       <div class="modal-body">
        <div id="resultadoinvitacion">

        </div>
      </div>  
      <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>        
    </div>
  </div>
</div>


    <!-- Modal Nueva Alianza-->
   
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="myModalLabel">Nueva Alianza</h4>
		      </div>
          <form method="POST" action="#">         
		      <div class="modal-body">
		        
		          <div class="form-group">
		            <input type="text" onKeyUp="checkName()" class="form-control" id="recipient-name" name="alianzaname" placeholder="Nombre de tu alianza">
		          </div>
	            <div class="form-group">
		        		<div class="alert alert-warning"><label for="recipient-name" class="control-label">Crear una alianza te costara 30.000 unidades de oro. A continuacion escoge de que ciudad quieres tomarlos</label></div>
		          </div>
		          <div class="form-group">
                <select name="idlab" class="form-control">
                  <?php echo $select;?>
                </select>
		         	</div>
              <div class="form-group" id="divmensaje">                
              </div>	        	
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		        <button type="submit" name="crear" class=<?php echo $puede;?>><?php echo $puedebtn;?></button>
		      </div>
          </form>
		    </div>
		  </div>
		</div>
</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>