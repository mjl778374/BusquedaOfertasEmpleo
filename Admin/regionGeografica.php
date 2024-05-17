<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$Region = "";
$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["Region"]))
{
    $SePretendeGuardarInformacion = true;
    $Region = $_POST["Region"];
} // if (isset($_POST["Region"]))

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
        $IdRegion = CParametrosGet::ValidarIdEntero("IdRegion", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdRegion'.");
        elseif ($NumError == 2)
            throw new Exception("'IdRegion' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdRegion'.");

        if (strcmp($_GET["IdRegion"], $IdRegion) != 0)
            header("Location: " . "regionGeografica.php?Modo=" . $Modo . "&IdRegion=" . $IdRegion);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)

// A continuación el código fuente de la implementación
try
{
    $ObjRegion = NULL;
    include_once "CRegionesGeograficas.php";

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $Regiones = new CRegionesGeograficas();

        if (strcmp($Modo, $MODO_ALTA) == 0)
            $Regiones->AltaRegionGeografica($Region, $NumError, $ObjRegion);

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
            $Regiones->CambioRegionGeografica($IdRegion, $Region, $NumError, $ObjRegion);

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Regiones = new CRegionesGeograficas();
        $Regiones->ConsultarXRegionGeografica($IdRegion, $Existe, $ObjRegion);

        if (!$Existe)
            $NumError = 2;
    } // if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

if ($ObjRegion != NULL)
{
    $IdRegion = $ObjRegion->DemeIdRegion();
    $Region = $ObjRegion->DemeRegion();
} // if ($ObjRegion != NULL)

if ($SeGuardoInformacionExitosamente && strcmp($Modo, $MODO_ALTA) == 0)
    header("Location: regionGeografica.php?Modo=" . $MODO_CAMBIO . "&IdRegion=" . $IdRegion); // Se carga la región guardada.

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteRegionConIdEspecificado = "No existe la región con el id " . $IdRegion . ".";
$ErrorRegionInvalida = "La región debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteRegionConIdEspecificado);
    else
    {
        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe la región " . $Region . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorRegionInvalida);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'AltaRegionGeografica'.");
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe la región " . $Region . " con otro id.");
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteRegionConIdEspecificado);
            elseif ($NumError == 3001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorRegionInvalida);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'CambioRegionGeografica'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
    } // else
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se guardó la región exitosamente.");

$Region = htmlspecialchars($Region);

$MaximoTamanoCampoRegion = CRegionesGeograficas::MAXIMO_TAMANO_CAMPO_REGION_GEOGRAFICA;

?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "RegionGeografica"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Region">Región</label>
                <input type="text" class="form-control" id="Region" name="Region" placeholder="Ingrese la región" value="<?php echo $Region; ?>" maxlength="<?php echo $MaximoTamanoCampoRegion;?>">
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='regionesGeograficas.php';">Regresar</button>
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
