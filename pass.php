<!DOCTYPE html>
<html lang="en">
    
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>AutoConsulta San Antonio</title>
    <link rel="shorcut icon" href="img/sanantonio.ico">

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="vendor/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
        var focos=[]; //arreglo para guardar los focos.
        function id( el ){
                return document.getElementById( el );
        }
        function val( destino, valor ){
                destino.value += valor;
        }
        var focus = false;
        window.onload = function(){
                var botoes = id('teclado').getElementsByTagName('input');
                for( var i=0; i<botoes.length; i++ ){
                        botoes[i].onclick = function(){
                                if( !focus ){ alert('toque en algun campo');exit(); }

                                val( id( focus ), this.value );
                                id( focus ).focus();
                        }
                }
                var inputs = id('area').getElementsByTagName('input');
                for( var i=0; i<inputs.length; i++ ){
                        inputs[i].onfocus = function(){
                                focus = this.id;
                                focos.push(this.id);//guarda el ultimo foco
                        }
                }
                id('entrada_1').focus();
        }
        //funcion para borrar el ultimo elemento
        function deleteTag(){
          var largo=focos.length;
          var Ultimofoco= focos[largo-1];
          var strng=document.getElementById(Ultimofoco).value;
          document.getElementById(Ultimofoco).value=strng.substring(0,strng.length-1);
        }
      </script>

</head>
<body>
    <?php 
        session_start();
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 360)) {
            // last request was more than 30 minutes ago
            session_unset();     // unset $_SESSION variable for the run-time
            session_destroy();   // destroy session data in
            header('location: login.html');
        }
    ?> <!-- con esta lidea puedes usar las variables de sesion en la pagina-->

    <div id="wrapper">  
    <div id="waitani" style="position: absolute;top:98px;left:532px;width:88px;height:85px;z-index: 999;visibility: hidden;"><img src="img/loading.gif"></div>
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="dashboard.php"><img src="img/Logo-Autoconsulta.png" style="margin-top: -15px;"></a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <li><a href="#"><i class="fa fa-user fa-fw"></i><?php print_r($_SESSION['id_usuario'])?></a>
                </li>
                
            </ul>
            <!-- /.navbar-top-links -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="dashboard.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-dashboard fa-fw"></i> Inicio</button></a>
                        </li>
                        <li>
                            <a href="eecc.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-calendar-check-o fa-fw"></i> Estado de Cuenta</button></a>
                            
                        </li>
                        <li>
                            <a href="cpp.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-bar-chart-o fa-fw"></i> Cuotas pendientes</button></a>
                            
                        </li>
                        <li>
                            <a href="mys.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-folder fa-fw"></i> Movimientos</button></a>
                            
                        </li>
                        <!--
                        <li>
                            <a href="#"><i class="fa fa-table fa-fw"></i> Estado de cuenta</a>
                        </li>
                        -->
                        <li>
                            <a href="pass.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-lock fa-fw"></i> Contraseña</button></a>
                            
                        </li>
                        <li><a href="salir.php"><button type="button" class="btn btn-outline btn-primary btn-lg btn-block"><i class="fa fa-sign-out fa-fw"></i> Salir</button></a> <!-- te cambio el link, para destruir las variables de sesion -->
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Cambio de Contraseña</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div id="area" class="col-lg-6 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Complete los siguientes campos para continuar
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" method="POST" action="cambiopass.php">
                                        <div class="form-group">
                                            <label>Contraseña Actual</label>
                                            <input id="entrada_1" class="form-control" type="password" name="oldpass" maxlength="20" placeholder="Ingrese aqui su contraseña actual">
                                        </div>
                                        <div class="form-group">
                                            <label>Nueva Contraseña</label>
                                            <p class="help-block">Máximo 20 caracteres</p>
                                            <input id="entrada_2" class="form-control" type="password" name="newpass" maxlength="20" placeholder="Ingrese aqui su nueva contraseña">
                                        </div>
                                        <div class="form-group">
                                            <input id="entrada_3" class="form-control" type="password" name="newpass1" maxlength="20" placeholder="Repita su nueva contraseña">
                                            
                                        </div>
                                        <button type="submit" class="btn btn-success">Cambiar Contraseña</button>
                                        <button type="button" class="btn btn-danger" name="clean" onclick="deleteTag()"><i class="fa fa-caret-square-o-left"></i> Borrar</button>
                                    </form>
                                </div>
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
               <div class="col-lg-4 col-md-4">
                    <div class="btn-group text-center">
                      <fieldset> <!-- no me preguntes porque es necesario este fieldset, pero sin el el boton se ve feo-->
                        <fieldset id="teclado">
                          <fieldset id="numbers">
                            <fieldset style="padding-bottom: 4px;">
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="1" value="1" />
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="2" value="2" />
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="3" value="3" />
                            </fieldset>
                            <fieldset style="padding-bottom: 4px;">
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="4" value="4" />
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="5" value="5" />
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="6" value="6" />
                            </fieldset>
                            <fieldset style="padding-bottom: 4px;">
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="7" value="7" />
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="8" value="8" />
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="9" value="9" />
                            </fieldset>
                            <fieldset>
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="0" value="0" />
                              <input class="btn btn-primary btn-circle btn-xl" type="button" name="K" value="K" />
                            </fieldset>
                          </fieldset>
                        </fieldset>
                        <br>
                    </fieldset>
                    </div>
              </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>

    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

</body>

</html>
