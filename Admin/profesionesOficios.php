<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

session_start();
$TextoXBuscar = "";

if (isset($_GET["TextoXBuscar"]))
    $TextoXBuscar = $_GET["TextoXBuscar"];
elseif (isset($_SESSION["ProfesionesOficios_TextoXBuscar"]))
    $TextoXBuscar = $_SESSION["ProfesionesOficios_TextoXBuscar"];

// A continuación el código fuente de la implementación
try
{
    include_once "CProfesionesOficios.php";
    $ProfesionesOficios = new CProfesionesOficios();
    $ListadoProfesionesOficios = $ProfesionesOficios->ConsultarXTodosAliasProfesionesOficios($TextoXBuscar);
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

$_SESSION["ProfesionesOficios_TextoXBuscar"] = $TextoXBuscar;
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "ProfesionesOficios";
$URLFormularioActivo = "profesionesOficios.php";
// Los anteriores son parámetros que recibe "menuApp.php"
include "menuApp.php";

include_once "constantesApp.php";
$NumPaginas = 0;

if (count($ListadoProfesionesOficios) > 0 && $MAX_NUM_RESULTADOS_X_PAGINA > 0)
    $NumPaginas = ceil(count($ListadoProfesionesOficios) / $MAX_NUM_RESULTADOS_X_PAGINA);

include_once "CParametrosGet.php";
$NumPaginaActual = CParametrosGet::DemeNumPagina("NumPagina", $NumPaginas);
// Los anteriores son parámetros que manipulan "componenteTabla.php" y "componentePaginacion.php"

if (isset($_GET["NumPagina"]) && strcmp($_GET["NumPagina"], $NumPaginaActual) != 0)
    header("Location: " . "profesionesOficios.php?NumPagina=" . $NumPaginaActual);

include_once "constantesApp.php";

if ($NumPaginaActual > 0)
{
    $EncabezadoTabla = array("Profesión u Oficio");
    $Filas = [];
    $IndiceInicial = ($NumPaginaActual - 1) * $MAX_NUM_RESULTADOS_X_PAGINA;
    $IndiceFinal = $IndiceInicial + $MAX_NUM_RESULTADOS_X_PAGINA - 1;

    if ($IndiceFinal >= count($ListadoProfesionesOficios))
        $IndiceFinal = count($ListadoProfesionesOficios) - 1;

    for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)
    {
        $ObjAliasProfesionOficio = $ListadoProfesionesOficios[$i];

        $Fila = array("mainProfesionOficio.php?Modo=" . $MODO_CAMBIO . "&IdProfesionOficio=" . $ObjAliasProfesionOficio->DemeIdProfesionOficio() . "&IdAlias=" . $ObjAliasProfesionOficio->DemeIdAlias(), $ObjAliasProfesionOficio->DemeAlias());
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
<a href="profesionOficio.php?Modo=<?php echo $MODO_ALTA;?>" class="btn btn-primary" role="button" aria-pressed="true">Agregar Profesión u Oficio</a>
</div>

<?php
$URL = "profesionesOficios.php";
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
