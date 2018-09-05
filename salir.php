<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
<?php
//Inicia una nueva sesión o reanuda la existente
    session_start();
//Destruye toda la información registrada de una sesión
    session_destroy();

//Redirecciona a la página de login
    header('location: login.html');
?>
  </body>
</html>
