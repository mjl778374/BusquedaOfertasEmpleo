<?php
// El siguiente es el código fuente de la implementación
include_once "constantesApp.php";
include_once "CSession.php";

try
{
    $UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
    // La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
    // transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.

    $ObjUsuario = NULL;

    if ($UsuarioSesionIngresoOKApp)
        $ObjUsuario = CSession::DemeObjUsuarioSesion();

    if ($ObjUsuario != NULL)
    {
        $NombreUsuario = $ObjUsuario->DemeNombre();
        $TextoXDesplegar = trim("Bienvenid@ " . trim($NombreUsuario));
        $TextoXDesplegar = htmlspecialchars($TextoXDesplegar);
    } // if ($ObjUsuario != NULL)
} // try
catch (Exception $e)
{}

if (!$UsuarioSesionIngresoOKApp || $ObjUsuario == NULL)
    header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

// A continuación sigue el código fuente de la interfaz
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "Main"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<div class="container mt-4">
<blockquote class="blockquote text-center">
  <h1 class="display-4"><?php echo $TextoXDesplegar;?></h1>
</blockquote>
</div>
</body>
</html>
