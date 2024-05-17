<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["SubirArchivo"]))
{
    $SePretendeGuardarInformacion = true;
    $RutaOrigen = $_FILES['ArchivoXSubir']['tmp_name'];
    $NumErrorAlSubir = $_FILES['ArchivoXSubir']['error'];
    $TipoArchivoXSubir = $_FILES['ArchivoXSubir']['type'];
} // if (isset($_POST["SubirArchivo"]))

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

    $IdNegocio = CParametrosGet::ValidarIdEntero("IdNegocio", $NumError);
    if ($NumError == 1)
        throw new Exception("Debe incorporar el parámetro 'IdNegocio'.");
    elseif ($NumError == 2)
        throw new Exception("'IdNegocio' debe ser un número entero mayor o igual que 0.");
    elseif ($NumError != 0)
        throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdNegocio'.");

    if (strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Consecutivo = CParametrosGet::ValidarIdEntero("Consecutivo", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'Consecutivo'.");
        elseif ($NumError == 2)
            throw new Exception("'Consecutivo' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'Consecutivo'.");

        if (strcmp($_GET["IdNegocio"], $IdNegocio) != 0 || strcmp($_GET["Consecutivo"], $Consecutivo) != 0)
            header("Location: " . "subirArchivoOferta.php?Modo=" . $Modo . "&IdNegocio=" . $IdNegocio . "&Consecutivo=" . $Consecutivo);
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
    $ObjOfertaEmpleo = NULL;
    include_once "COfertasEmpleo.php";

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $OfertasEmpleo = new COfertasEmpleo();
        $OfertasEmpleo->ConsultarXOfertaEmpleo($IdNegocio, $Consecutivo, $Existe, $ObjOfertaEmpleo);

        if (!$Existe)
            $NumError = 2;
    } // if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $OfertaEmpleo = new COfertaEmpleo($IdNegocio, $Consecutivo, 0, 0, "", "");
        $OfertaEmpleo->SubirArchivoOferta($RutaOrigen, $NumErrorAlSubir, $TipoArchivoXSubir, $NumError);

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteOfertaEmpleoConIdNegocioYConsecutivoEspecificados = "No existe el registro con el id de negocio " . $IdNegocio . " y el consecutivo " . $Consecutivo  . ".";
$ErrorTipoArchivoInvalido = "Los archivos por subir debe ser del tipo '" . COfertaEmpleo::TIPO_ARCHIVOS_X_SUBIR_ACEPTADO . "'.";
$ErrorAlSubir = "Ocurrió un error al intentar subir el archivo.";
  
if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteOfertaEmpleoConIdNegocioYConsecutivoEspecificados);
    elseif ($NumError == 1001)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorTipoArchivoInvalido);
    elseif ($NumError == 1002)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorAlSubir);
    elseif ($NumError != 0)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError);
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se subió el archivo exitosamente.");
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<form method="post" enctype="multipart/form-data">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4 custom-file">
                <input type="hidden" name="SubirArchivo" id="SubirArchivo" value="1"></text>
                <input type="file" class="custom-file-input" id="ArchivoXSubir" name="ArchivoXSubir">
                <label class="custom-file-label" for="ArchivoXSubir">Seleccione un archivo...</label>
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Subir</button>
            </div>
        </div>
    </div>
<?php
if ($MensajeXDesglosar != "")
    echo $MensajeXDesglosar;
?>
</form>
</body>
<?php
include_once "constantesApp.php";
$IdControl = "ControlFechaVencimiento"; $FormatoFecha = $FORMATO_FECHAS_CONTROLES_FECHA; $FechaInicial = $FechaVencimiento; $IdControlCopia="FechaVencimiento"; include "componenteFecha.php";
?>
</html>
