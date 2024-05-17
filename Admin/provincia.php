<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$Provincia = "";
$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["Provincia"]))
{
    $SePretendeGuardarInformacion = true;
    $Provincia = $_POST["Provincia"];
} // if (isset($_POST["Provincia"]))

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
        $IdProvincia = CParametrosGet::ValidarIdEntero("IdProvincia", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdProvincia'.");
        elseif ($NumError == 2)
            throw new Exception("'IdProvincia' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdProvincia'.");

        if (strcmp($_GET["IdProvincia"], $IdProvincia) != 0)
            header("Location: " . "provincia.php?Modo=" . $Modo . "&IdProvincia=" . $IdProvincia);
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
    $ObjProvincia = NULL;
    include_once "CProvincias.php";

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $Provincias = new CProvincias();

        if (strcmp($Modo, $MODO_ALTA) == 0)
            $Provincias->AltaProvincia($Provincia, $NumError, $ObjProvincia);

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
            $Provincias->CambioProvincia($IdProvincia, $Provincia, $NumError, $ObjProvincia);

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Provincias = new CProvincias();
        $Provincias->ConsultarXProvincia($IdProvincia, $Existe, $ObjProvincia);

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

if ($ObjProvincia != NULL)
{
    $IdProvincia = $ObjProvincia->DemeIdProvincia();
    $Provincia = $ObjProvincia->DemeProvincia();
} // if ($ObjProvincia != NULL)

if ($SeGuardoInformacionExitosamente && strcmp($Modo, $MODO_ALTA) == 0)
    header("Location: provincia.php?Modo=" . $MODO_CAMBIO . "&IdProvincia=" . $IdProvincia); // Se carga la provincia guardada.

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteProvinciaConIdEspecificado = "No existe la provincia con el id " . $IdProvincia . ".";
$ErrorProvinciaInvalida = "La provincia debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteProvinciaConIdEspecificado);
    else
    {
        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe la provincia " . $Provincia . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorProvinciaInvalida);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'AltaProvincia'.");
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe la provincia " . $Provincia . " con otro id.");
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteProvinciaConIdEspecificado);
            elseif ($NumError == 3001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorProvinciaInvalida);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'CambioProvincia'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
    } // else
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se guardó la provincia exitosamente.");

$Provincia = htmlspecialchars($Provincia);

$MaximoTamanoCampoProvincia = CProvincias::MAXIMO_TAMANO_CAMPO_PROVINCIA;

?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "Provincia"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Provincia">Provincia</label>
                <input type="text" class="form-control" id="Provincia" name="Provincia" placeholder="Ingrese la provincia" value="<?php echo $Provincia; ?>" maxlength="<?php echo $MaximoTamanoCampoProvincia;?>">
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='provincias.php';">Regresar</button>
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
