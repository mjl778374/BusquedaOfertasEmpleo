<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$Nombre = "";
$Direccion = "";
$Telefonos = "";
$IdCanton = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;
$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["NombreNegocio"]))
{
    $SePretendeGuardarInformacion = true;

    $Nombre = $_POST["NombreNegocio"];
    $Direccion = $_POST["Direccion"];;
    $Telefonos = $_POST["Telefonos"];;
    $IdCanton = $_POST["IdCanton"];
} // if (isset($_POST["NombreNegocio"]))

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
        $IdNegocio = CParametrosGet::ValidarIdEntero("IdNegocio", $NumError);
        if ($NumError == 1)
            throw new Exception("Debe incorporar el parámetro 'IdNegocio'.");
        elseif ($NumError == 2)
            throw new Exception("'IdNegocio' debe ser un número entero mayor o igual que 0.");
        elseif ($NumError != 0)
            throw new Exception("No se manejó el error número " . $NumError . " en el parámetro 'IdNegocio'.");

        if (strcmp($_GET["IdNegocio"], $IdNegocio) != 0)
            header("Location: " . "negocio.php?Modo=" . $Modo . "&IdNegocio=" . $IdNegocio);
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
    $ObjNegocio = NULL;
    include_once "CNegocios.php";

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $Negocios = new CNegocios();

        if (strcmp($Modo, $MODO_ALTA) == 0)
            $Negocios->AltaNegocio($Nombre, $Direccion, $Telefonos, $IdCanton, $NumError, $ObjNegocio);

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
            $Negocios->CambioNegocio($IdNegocio, $Nombre, $Direccion, $Telefonos, $IdCanton, $NumError, $ObjNegocio);

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $Negocios = new CNegocios();
        $Negocios->ConsultarXNegocio($IdNegocio, $Existe, $ObjNegocio);

        if (!$Existe)
            $NumError = 2;
    } // if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)

    include_once "CCantones.php";
    $Cantones = new CCantones();
    $ListadoCantones = $Cantones->DemeTodosCantones();
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

if ($ObjNegocio != NULL)
{
    $IdNegocio = $ObjNegocio->DemeIdNegocio();
    $Nombre = $ObjNegocio->DemeNombre();
    $Direccion = $ObjNegocio->DemeDireccion();
    $Telefonos = $ObjNegocio->DemeTelefonos();
    $IdCanton = $ObjNegocio->DemeIdCanton();
} // if ($ObjNegocio != NULL)

if ($SeGuardoInformacionExitosamente && strcmp($Modo, $MODO_ALTA) == 0)
{
    echo "<script>window.top.location.href = 'mainNegocio.php?Modo=" . $MODO_CAMBIO . "&IdNegocio=" . $IdNegocio . "';</script>"; // Se carga el negocio guardado.
    exit;
} // if ($SeGuardoInformacionExitosamente && strcmp($Modo, $MODO_ALTA) == 0)

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteNegocioConIdEspecificado = "No existe el negocio o empresa con el id " . $IdNegocio . ".";
$ErrorNombreInvalido = "El nombre debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
$ErrorDireccionInvalida = "La dirección debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
$ErrorTelefonosInvalidos = "Los teléfonos deben tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();
$ErrorDebeSeleccionarCanton = "Debe seleccionar un cantón";
$ErrorNoExisteCanton = "El cantón seleccionado no existe";
  
if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteNegocioConIdEspecificado);
    else
    {
        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNombreInvalido);
            elseif ($NumError == 1002)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDireccionInvalida);
            elseif ($NumError == 1003)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorTelefonosInvalidos);
            elseif ($NumError == 2001)
            {
                if ($IdCanton == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDebeSeleccionarCanton);
                else
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteCanton);
            } // elseif ($NumError == 2001)
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'AltaNegocio'.");
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteNegocioConIdEspecificado);
            else if ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNombreInvalido);
            elseif ($NumError == 2002)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDireccionInvalida);
            elseif ($NumError == 2003)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorTelefonosInvalidos);
            elseif ($NumError == 3001)
            {
                if ($IdCanton == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDebeSeleccionarCanton);
                else
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteCanton);
            } // elseif ($NumError == 3001)
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'CambioNegocio'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
    } // else
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se guardó el negocio o empresa exitosamente.");

$Nombre = htmlspecialchars($Nombre);
$Direccion = htmlspecialchars($Direccion);
$Telefonos = htmlspecialchars($Telefonos);

$MaximoTamanoCampoNombre = CNegocios::MAXIMO_TAMANO_CAMPO_NOMBRE;
$MaximoTamanoCampoDireccion = CNegocios::MAXIMO_TAMANO_CAMPO_DIRECCION;
$MaximoTamanoCampoTelefonos = CNegocios::MAXIMO_TAMANO_CAMPO_TELEFONOS;
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "Negocio"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
       <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="NombreNegocio">Nombre</label>
                <input type="text" class="form-control" id="NombreNegocio" name="NombreNegocio" placeholder="Ingrese el nombre del negocio o empresa" value="<?php echo $Nombre; ?>" maxlength="<?php echo $MaximoTamanoCampoNombre;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Direccion">Dirección</label>
                <textarea class="form-control" id="Direccion" name="Direccion" rows="4" placeholder="Ingrese la dirección" maxlength="<?php echo $MaximoTamanoCampoDireccion;?>"><?php echo $Direccion; ?></textarea>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Telefonos">Teléfonos</label>
                <input type="text" class="form-control" id="Telefonos" name="Telefonos" placeholder="Ingrese los teléfonos" value="<?php echo $Telefonos; ?>" maxlength="<?php echo $MaximoTamanoCampoTelefonos;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="IdCanton">Cantón</label>
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoCantones;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Seleccione un Cantón...");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdCanton;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion="IdCanton"; $NombreListaSeleccion="IdCanton"; include "componenteListaSeleccion.php" ?>
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.top.location.href='negocios.php';">Regresar</button>
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
