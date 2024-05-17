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

session_start();
$TextoXBuscar = "";

if (isset($_GET["TextoXBuscar"]))
    $TextoXBuscar = $_GET["TextoXBuscar"];
elseif (isset($_SESSION["Usuarios_TextoXBuscar"]))
    $TextoXBuscar = $_SESSION["Usuarios_TextoXBuscar"];

// A continuación el código fuente de la implementación
try
{
    include_once "CUsuarios.php";
    $Usuarios = new CUsuarios();
    $ListadoUsuarios = $Usuarios->ConsultarXTodosUsuarios($TextoXBuscar);
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

$_SESSION["Usuarios_TextoXBuscar"] = $TextoXBuscar;
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "Usuarios";
$URLFormularioActivo = "usuarios.php";
// Los anteriores son parámetros que recibe "menuApp.php"
include "menuApp.php";

include_once "constantesApp.php";
$NumPaginas = 0;

if (count($ListadoUsuarios) > 0 && $MAX_NUM_RESULTADOS_X_PAGINA > 0)
    $NumPaginas = ceil(count($ListadoUsuarios) / $MAX_NUM_RESULTADOS_X_PAGINA);

include_once "CParametrosGet.php";
$NumPaginaActual = CParametrosGet::DemeNumPagina("NumPagina", $NumPaginas);
// Los anteriores son parámetros que manipulan "componenteTabla.php" y "componentePaginacion.php"

if (isset($_GET["NumPagina"]) && strcmp($_GET["NumPagina"], $NumPaginaActual) != 0)
    header("Location: " . "usuarios.php?NumPagina=" . $NumPaginaActual);

include_once "constantesApp.php";

if ($NumPaginaActual > 0)
{
    $EncabezadoTabla = array("Usuario", "Cédula", "Nombre", "Es Administrador");
    $Filas = [];
    $IndiceInicial = ($NumPaginaActual - 1) * $MAX_NUM_RESULTADOS_X_PAGINA;
    $IndiceFinal = $IndiceInicial + $MAX_NUM_RESULTADOS_X_PAGINA - 1;

    if ($IndiceFinal >= count($ListadoUsuarios))
        $IndiceFinal = count($ListadoUsuarios) - 1;

    for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)
    {
        $ObjUsuario = $ListadoUsuarios[$i];
        $EsAdministrador = "No";

        if ($ObjUsuario->DemeEsAdministrador())
            $EsAdministrador = "Sí";

        $Fila = array("usuario.php?Modo=" . $MODO_CAMBIO . "&IdUsuario=" . $ObjUsuario->DemeIdUsuario(), $ObjUsuario->DemeUsuario(), $ObjUsuario->DemeCedula(), $ObjUsuario->DemeNombre(), $EsAdministrador);
        $Filas[] = $Fila;
        // Los anteriores son parámetros que recibe "componenteTabla.php"
    } // for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)

    if (count($Filas) > 0)
        include "componenteTabla.php";
} // if ($NumPaginaActual > 0)

include_once "CFormateadorMensajes.php";
$MensajeXDesglosar = "";

if ($NumError == 1)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
?>
<div class="container mt-4 mb-4">
<?php
include_once "constantesApp.php";
?>
<a href="usuario.php?Modo=<?php echo $MODO_ALTA;?>" class="btn btn-primary" role="button" aria-pressed="true">Agregar Usuario</a>
</div>

<?php
$URL = "usuarios.php";
// Los anteriores son parámetros que recibe "componentePaginacion.php"

if ($NumPaginas > 0)
    include "componentePaginacion.php";
?>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</body>
</html>
