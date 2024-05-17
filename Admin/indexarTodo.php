<?php
try
{
    include_once "constantesApp.php";
    include_once "CSession.php";
    $UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
    // La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
    // transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.

    $ObjUsuario = NULL;
    $UsuarioSesionEsAdmin = false;

    if ($UsuarioSesionIngresoOKApp)
        $ObjUsuario = CSession::DemeObjUsuarioSesion();

    if ($ObjUsuario != NULL)
        $UsuarioSesionEsAdmin = $ObjUsuario->DemeEsAdministrador();
} // try
catch (Exception $e)
{}

if (!$UsuarioSesionIngresoOKApp || $ObjUsuario == NULL || !$UsuarioSesionEsAdmin)
    header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$SeDebeIndexarTodo = false;
$MensajesXDesglosar = [];
$MENSAJE_OK = 1;
$MENSAJE_ERROR = 2;
$IdMensajeSeVanIndexarTodosUsuarios = 1;
$IdMensajeSeIndexaronTodosUsuarios = 2;

$IdMensajeSeVanIndexarTodasRegiones = 3;
$IdMensajeSeIndexaronTodasRegiones = 4;

$IdMensajeSeVanIndexarTodasProvincias = 5;
$IdMensajeSeIndexaronTodasProvincias = 6;

$IdMensajeSeVanIndexarTodosCantones = 7;
$IdMensajeSeIndexaronTodosCantones = 8;

$IdMensajeSeVanIndexarTodosAliasProfesionesOficios = 9;
$IdMensajeSeIndexaronTodosAliasProfesionesOficios = 10;

$IdMensajeSeVanIndexarTodosNegocios = 11;
$IdMensajeSeIndexaronTodosNegocios = 12;

$IdMensajeSeIndexoTodoExitosamente = 0;

if (isset($_POST["IndexarTodo"]))
{
    $SeDebeIndexarTodo = true;
} // if (isset($_POST["IndexarTodo"]))

// A continuación el código fuente de la implementación
try
{
    if ($SeDebeIndexarTodo)
    {
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeVanIndexarTodosUsuarios);
        include_once "CUsuarios.php";
        $Usuarios = new CUsuarios();
        $Usuarios->IndexarTodo();
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexaronTodosUsuarios);

        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeVanIndexarTodasRegiones);
        include_once "CRegionesGeograficas.php";
        $Regiones = new CRegionesGeograficas();
        $Regiones->IndexarTodo();
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexaronTodasRegiones);

        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeVanIndexarTodasProvincias);
        include_once "CProvincias.php";
        $Provincias = new CProvincias();
        $Provincias->IndexarTodo();
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexaronTodasProvincias);

        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeVanIndexarTodosCantones);
        include_once "CCantones.php";
        $Cantones = new CCantones();
        $Cantones->IndexarTodo();
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexaronTodosCantones);

        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeVanIndexarTodosAliasProfesionesOficios);
        include_once "CProfesionesOficios.php";
        $ProfesionesOficios = new CProfesionesOficios();
        $ProfesionesOficios->IndexarTodo();
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexaronTodosAliasProfesionesOficios);

        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeVanIndexarTodosNegocios);
        include_once "CNegocios.php";
        $Negocios = new CNegocios();
        $Negocios->IndexarTodo();
        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexaronTodosNegocios);

        $MensajesXDesglosar[] = array($MENSAJE_OK, $IdMensajeSeIndexoTodoExitosamente);
    } // if ($SeDebeIndexarTodo)
} // try
catch (Exception $e)
{
    $MensajesXDesglosar[] = array($MENSAJE_ERROR, $e->getMessage());
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

include_once "CFormateadorMensajes.php";
$MensajeXDesglosar = "";

for($i = 0; $i < count($MensajesXDesglosar); $i++)
{
    $MensajeActual = $MensajesXDesglosar[$i];

    if ($MensajeActual[0] == $MENSAJE_OK)
    {
        if ($MensajeActual[1] == $IdMensajeSeVanIndexarTodosUsuarios)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se van a indexar los usuarios...");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexaronTodosUsuarios)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexaron los usuarios exitosamente.");

        elseif ($MensajeActual[1] == $IdMensajeSeVanIndexarTodasRegiones)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se van a indexar las regiones geográficas...");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexaronTodasRegiones)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexaron las regiones geográficas exitosamente.");

        elseif ($MensajeActual[1] == $IdMensajeSeVanIndexarTodasProvincias)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se van a indexar las provincias...");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexaronTodasProvincias)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexaron las provincias exitosamente.");

        elseif ($MensajeActual[1] == $IdMensajeSeVanIndexarTodosCantones)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se van a indexar los cantones...");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexaronTodosCantones)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexaron los cantones exitosamente.");

        elseif ($MensajeActual[1] == $IdMensajeSeVanIndexarTodosAliasProfesionesOficios)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se van a indexar las profesiones y oficios...");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexaronTodosAliasProfesionesOficios)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexaron las profesiones y oficios exitosamente.");

        elseif ($MensajeActual[1] == $IdMensajeSeVanIndexarTodosNegocios)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se van a indexar los negocios y empresas...");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexaronTodosNegocios)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexaron los negocios y empresas exitosamente.");

        elseif ($MensajeActual[1] == $IdMensajeSeIndexoTodoExitosamente)
            $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeOK("Se indexó todo exitosamente.");
    } // if ($MensajeActual[0] == $MENSAJE_OK)
    elseif ($MensajeActual[0] == $MENSAJE_ERROR)
        $MensajeXDesglosar = $MensajeXDesglosar . CFormateadorMensajes::FormatearMensajeError($MensajeActual[1]);
} // for($i = 0; $i < count($MensajesXDesglosar); $i++)

?>
<!DOCTYPE html>
<html>
<?php
    include "encabezados.php";
?>
<body>
<?php
    $FormularioActivo = "IndexarTodo"; // Este es un parámetro que recibe "menuApp.php"
    include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary btn-lg btn-block" name="IndexarTodo">Indexar Todo</button>
            </div>
        </div>
    </div>
    <?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</form>
</body>
</html>
