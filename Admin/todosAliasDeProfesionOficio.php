<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

try
{
    include_once "CParametrosGet.php";

    $Modo = CParametrosGet::ValidarModo("Modo", $NumError);

    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'Modo'.");
    elseif ($NumError == 2)
        throw new Exception("'Modo' inválido.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Modo'.");

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $IdProfesionOficio = CParametrosGet::ValidarIdEntero("IdProfesionOficio", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdProfesionOficio'.");
        elseif ($NumError == 2)
            throw new Exception("'IdProfesionOficio' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdProfesionOficio'.");

        $IdAlias = CParametrosGet::ValidarIdEntero("IdAlias", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdAlias'.");
        elseif ($NumError == 2)
            throw new Exception("'IdAlias' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdAlias'.");

        if (strcmp($_GET["IdProfesionOficio"], $IdProfesionOficio) != 0 || strcmp($_GET["IdAlias"], $IdAlias) != 0)
            header("Location: " . "todosAliasDeProfesionOficio.php?Modo=" . $Modo . "&IdProfesionOficio=" . $IdProfesionOficio . "&IdAlias=" . $IdAlias);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)

    else
        throw new Exception("Solo se admite el modo '" . $MODO_CAMBIO . "'.");
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)

// A continuación el código fuente de la implementación
try
{
    $ListadoAliasProfesionOficio = [];

    if ($NumError == 0)
    {
        include_once "CProfesionesOficios.php";
        $AliasProfesionOficio = new CProfesionesOficios();
        $ListadoAliasProfesionOficio = $AliasProfesionOficio->DemeTodosAliasDeProfesionOficio($IdProfesionOficio, $IdAlias);
    } // if ($NumError == 0)
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
include_once "constantesApp.php";
$NumPaginas = 0;

if (count($ListadoAliasProfesionOficio) > 0 && $MAX_NUM_RESULTADOS_X_PAGINA > 0)
    $NumPaginas = ceil(count($ListadoAliasProfesionOficio) / $MAX_NUM_RESULTADOS_X_PAGINA);

include_once "CParametrosGet.php";
$NumPaginaActual = CParametrosGet::DemeNumPagina("NumPagina", $NumPaginas);
// Los anteriores son parámetros que manipulan "componenteTabla.php" y "componentePaginacion.php"

if (isset($_GET["NumPagina"]) && strcmp($_GET["NumPagina"], $NumPaginaActual) != 0)
    header("Location: " . "todosAliasDeProfesionOficio.php?Modo=" . $Modo . "&IdProfesionOficio=" . $IdProfesionOficio . "&IdAlias=" . $IdAlias . "&NumPagina=" . $NumPaginaActual);

include_once "constantesApp.php";

if ($NumPaginaActual > 0)
{
    $EncabezadoTabla = array("Alias");
    $Filas = [];
    $IndiceInicial = ($NumPaginaActual - 1) * $MAX_NUM_RESULTADOS_X_PAGINA;
    $IndiceFinal = $IndiceInicial + $MAX_NUM_RESULTADOS_X_PAGINA - 1;

    if ($IndiceFinal >= count($ListadoAliasProfesionOficio))
        $IndiceFinal = count($ListadoAliasProfesionOficio) - 1;

    for ($i = $IndiceInicial; $i <= $IndiceFinal; $i++)
    {
        $ObjAliasProfesionOficio = $ListadoAliasProfesionOficio[$i];

        $Fila = array("aliasProfesionOficio.php?Modo=" . $MODO_CAMBIO . "&IdProfesionOficio=" . $ObjAliasProfesionOficio->DemeIdProfesionOficio() . "&IdAlias=" . $ObjAliasProfesionOficio->DemeIdAlias() . "&IdAliasExcluir=" . $IdAlias, $ObjAliasProfesionOficio->DemeAlias());
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
<?php if (strcmp($Modo, $MODO_CAMBIO) == 0) { ?>
<div class="container mt-4 mb-4">
<?php
include_once "constantesApp.php";
?>
<a href="aliasProfesionOficio.php?Modo=<?php echo $MODO_ALTA;?>&IdProfesionOficio=<?php echo $IdProfesionOficio?>&IdAliasExcluir=<?php echo $IdAlias?>" class="btn btn-primary" role="button" aria-pressed="true">Agregar Alias</a>
</div>
<?php } // if (strcmp($Modo, $MODO_CAMBIO) == 0) { ?>

<?php
$URL = "todosAliasDeProfesionOficio.php";
$ParametrosURL = "?Modo=" . $Modo . "&IdProfesionOficio=" . $IdProfesionOficio . "&IdAlias=" . $IdAlias;
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
