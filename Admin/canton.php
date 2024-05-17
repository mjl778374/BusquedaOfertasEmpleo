<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$Canton = "";
$IdRegionGeografica = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;
$IdProvincia = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;
$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["Canton"]))
{
    $SePretendeGuardarInformacion = true;
    $Canton = $_POST["Canton"];
    $IdRegionGeografica = $_POST["IdRegionGeografica"];
    $IdProvincia = $_POST["IdProvincia"];
} // if (isset($_POST["Canton"]))

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
        $IdCanton = CParametrosGet::ValidarIdEntero("IdCanton", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdCanton'.");
        elseif ($NumError == 2)
            throw new Exception("'IdCanton' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdCanton'.");

        if (strcmp($_GET["IdCanton"], $IdCanton) != 0)
            header("Location: " . "canton.php?Modo=" . $Modo . "&IdCanton=" . $IdCanton);
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
    $ObjCanton = NULL;
    include_once "CCantones.php";

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $Cantones = new CCantones();

        if (strcmp($Modo, $MODO_ALTA) == 0)
            $Cantones->AltaCanton($Canton, $IdRegionGeografica, $IdProvincia, $NumError, $ObjCanton);

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
            $Cantones->CambioCanton($IdCanton, $Canton, $IdRegionGeografica, $IdProvincia, $NumError, $ObjCanton);

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Cantones = new CCantones();
        $Cantones->ConsultarXCanton($IdCanton, $Existe, $ObjCanton);

        if (!$Existe)
            $NumError = 2;
    } // if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)

    include_once "CProvincias.php";
    $Provincias = new CProvincias();
    $ListadoProvincias = $Provincias->DemeTodasProvincias();

    include_once "CRegionesGeograficas.php";
    $RegionesGeograficas = new CRegionesGeograficas();
    $ListadoRegionesGeograficas = $RegionesGeograficas->DemeTodasRegionesGeograficas();
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

if ($ObjCanton != NULL)
{
    $IdCanton = $ObjCanton->DemeIdCanton();
    $Canton = $ObjCanton->DemeCanton();
    $IdRegionGeografica = $ObjCanton->DemeIdRegion();
    $IdProvincia = $ObjCanton->DemeIdProvincia();
} // if ($ObjCanton != NULL)

if ($SeGuardoInformacionExitosamente && strcmp($Modo, $MODO_ALTA) == 0)
    header("Location: canton.php?Modo=" . $MODO_CAMBIO . "&IdCanton=" . $IdCanton); // Se carga el cantón guardado.

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteCantonConIdEspecificado = "No existe el cantón con el id " . $IdCanton . ".";
$ErrorCantonInvalido = "El cantón debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
$ErrorDebeSeleccionarRegionGeografica = "Debe seleccionar una región geográfica";
$ErrorDebeSeleccionarProvincia = "Debe seleccionar una provincia";
$ErrorNoExisteRegionGeografica = "La región geográfica seleccionada no existe";
$ErrorNoExisteProvincia = "La provincia seleccionada no existe";

if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteCantonConIdEspecificado);
    else
    {
        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe el cantón " . $Canton . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                 $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorCantonInvalido);
            elseif ($NumError == 2002)
            {
                if ($IdRegionGeografica == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDebeSeleccionarRegionGeografica);
                else
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteRegionGeografica);
            } // elseif ($NumError == 2002)
            elseif ($NumError == 2003)
            {
                if ($IdProvincia == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDebeSeleccionarProvincia);
                else
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteProvincia);
            } // elseif ($NumError == 2003)
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'AltaCanton'.");
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe el cantón " . $Canton . " con otro id.");
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteCantonConIdEspecificado);
            elseif ($NumError == 3001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorCantonInvalido);
            elseif ($NumError == 3002)
            {
                if ($IdRegionGeografica == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDebeSeleccionarRegionGeografica);
                else
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteRegionGeografica);
            } // elseif ($NumError == 3002)
            elseif ($NumError == 3003)
            {
                if ($IdProvincia == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDebeSeleccionarProvincia);
                else
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteProvincia);
            } // elseif ($NumError == 3003)
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'CambioCanton'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
    } // else
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se guardó el cantón exitosamente.");

$Canton = htmlspecialchars($Canton);

$MaximoTamanoCampoCanton = CCantones::MAXIMO_TAMANO_CAMPO_CANTON;
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "Canton"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Canton">Cantón</label>
                <input type="text" class="form-control" id="Canton" name="Canton" placeholder="Ingrese el cantón" value="<?php echo $Canton; ?>" maxlength="<?php echo $MaximoTamanoCampoCanton;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="IdRegionGeografica">Región Geográfica</label>
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoRegionesGeograficas;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Seleccione una Región Geográfica...");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdRegionGeografica;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion="IdRegionGeografica"; $NombreListaSeleccion="IdRegionGeografica"; include "componenteListaSeleccion.php" ?>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="IdProvincia">Provincia</label>
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoProvincias;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Seleccione una Provincia...");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdProvincia;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion="IdProvincia"; $NombreListaSeleccion="IdProvincia"; include "componenteListaSeleccion.php" ?>
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='cantones.php';">Regresar</button>
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
