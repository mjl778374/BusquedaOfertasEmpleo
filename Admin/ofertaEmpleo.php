<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$BitEstaVigente = 0;
$IdProfesionOficio = $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION;
$FechaVencimiento = "";
$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["FechaVencimiento"]))
{
    $SePretendeGuardarInformacion = true;

    if ($_POST["EstaVigente"] == "on")
        $BitEstaVigente = 1;

    $IdProfesionOficio = $_POST["IdProfesionOficio"];
    $FechaVencimiento = $_POST["FechaVencimiento"];
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
            header("Location: " . "ofertaEmpleo.php?Modo=" . $Modo . "&IdNegocio=" . $IdNegocio . "&Consecutivo=" . $Consecutivo);
    } // if (strcmp($Modo, $MODO_CAMBIO) == 0)
    elseif (strcmp($_GET["IdNegocio"], $IdNegocio) != 0)
        header("Location: " . "ofertaEmpleo.php?Modo=" . $Modo . "&IdNegocio=" . $IdNegocio);
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)

// A continuación el código fuente de la implementación
try
{
    if ($NumError == 0 && !$SePretendeGuardarInformacion && $FechaVencimiento == "")
    {
        include_once "CFechasHoras.php";
        include_once "constantesApp.php";
        $FechasHoras = new CFechasHoras();
        $FechaVencimiento = $FechasHoras->DemeFechaHoy($FORMATO_FECHAS_SQL);
    } // if ($NumError == 0 && !$SePretendeGuardarInformacion && $FechaVencimiento == "")

    $ObjOfertaEmpleo = NULL;
    include_once "COfertasEmpleo.php";

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $OfertasEmpleo = new COfertasEmpleo();

        if (strcmp($Modo, $MODO_ALTA) == 0)
            $OfertasEmpleo->AltaOfertaEmpleo($IdNegocio, $BitEstaVigente, $IdProfesionOficio, $FechaVencimiento, $NumError, $ObjOfertaEmpleo);

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
            $OfertasEmpleo->CambioOfertaEmpleo($IdNegocio, $Consecutivo, $BitEstaVigente, $IdProfesionOficio, $FechaVencimiento, $NumError, $ObjOfertaEmpleo);

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $OfertasEmpleo = new COfertasEmpleo();
        $OfertasEmpleo->ConsultarXOfertaEmpleo($IdNegocio, $Consecutivo, $Existe, $ObjOfertaEmpleo);

        if (!$Existe)
            $NumError = 2;
    } // if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)

    include_once "CProfesionesOficios.php";
    $ProfesionesOficios = new CProfesionesOficios();
    $ListadoProfesionesOficios = $ProfesionesOficios->DemeTodasProfesionesOficios();
} // try
catch (Exception $e)
{
    $NumError = 1;
    $MensajeOtroError = $e->getMessage();
} // catch (Exception $e)
// El anterior fue el código fuente de la implementación

if ($ObjOfertaEmpleo != NULL)
{
    $IdNegocio = $ObjOfertaEmpleo->DemeIdNegocio();
    $Consecutivo = $ObjOfertaEmpleo->DemeConsecutivo();
    $BitEstaVigente = $ObjOfertaEmpleo->DemeEstaVigente();
    $IdProfesionOficio = $ObjOfertaEmpleo->DemeIdProfesionOficio();
    $FechaVencimiento = $ObjOfertaEmpleo->DemeFechaVencimiento();
    $UrlOferta = $ObjOfertaEmpleo->DemeUrlOferta();
} // if ($ObjOfertaEmpleo != NULL)

if ($SeGuardoInformacionExitosamente && strcmp($Modo, $MODO_ALTA) == 0)
    header("Location: mainOfertaEmpleo.php?Modo=" . $MODO_CAMBIO . "&IdNegocio=" . $IdNegocio . "&Consecutivo=" . $Consecutivo); // Se carga la oferta de empleo guardada.

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteNegocioConIdEspecificado = "No existe el negocio o empresa con el id " . $IdNegocio . ".";
$ErrorNoExisteOfertaEmpleoConIdNegocioYConsecutivoEspecificados = "No existe el registro con el id de negocio " . $IdNegocio . " y el consecutivo " . $Consecutivo  . ".";
$ErrorNoExisteProfesionOficioConIdEspecificado = "No existe la profesión u oficio con el id " . $IdProfesionOficio . ".";
$ErrorDebeSeleccionarProfesionOficio = "Debe seleccionar una profesión u oficio";
  
if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteOfertaEmpleoConIdNegocioYConsecutivoEspecificados);
    else
    {
        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteNegocioConIdEspecificado);
            elseif ($NumError == 2001)
            {    
                if ($IdProfesionOficio == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDebeSeleccionarProfesionOficio);
                else
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteProfesionOficioConIdEspecificado);
            } // elseif ($NumError == 2001)
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'AltaOfertaEmpleo'.");
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteOfertaEmpleoConIdNegocioYConsecutivoEspecificados);
            elseif ($NumError == 2001)
            {    
                if ($IdProfesionOficio == $ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION)
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorDebeSeleccionarProfesionOficio);
                else
                    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteProfesionOficioConIdEspecificado);
            } // elseif ($NumError == 2001)
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'CambioOfertaEmpleo'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
    } // else
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se guardó la oferta de empleo exitosamente.");

include_once "FuncionesUtiles.php";
$UrlOferta = htmlspecialchars(FormatearTextoJS($UrlOferta));

$EstaVigenteSeleccionado = "";

if ($BitEstaVigente)
    $EstaVigenteSeleccionado = "checked";

$FrameRegreso = "window.parent";

if (strcmp($Modo, $MODO_ALTA) == 0)
    $FrameRegreso = "window";
?>
<!DOCTYPE html>
<html>
<?php
$IncluirEncabezadosFecha = 1; // Este es un parámetro que recibe "encabezados.php"
include "encabezados.php";
?>
<body>
<form method="post">
    <div class="container mt-4">
<?php if (strcmp($Modo, $MODO_CAMBIO) == 0) { ?>
       <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="Consecutivo">Consecutivo</label>
                <input type="text" class="form-control" id="Consecutivo" name="Consecutivo" value="<?php echo $Consecutivo; ?>" readonly>
            </div>
        </div>
<?php } // if (strcmp($Modo, $MODO_CAMBIO) == 0) { ?>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <input type="hidden" name="FechaVencimiento" id="FechaVencimiento"></text>
                <label for="ControlFechaVencimiento">Fecha de Vencimiento</label>
                <div id="ControlFechaVencimiento" name="ControlFechaVencimiento"></div>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="IdProfesionOficio">Profesión u Oficio</label>
<?php
$PrimerItemListaSeleccion = [];
$ItemesListaSeleccion = $ListadoProfesionesOficios;
$PrimerItemListaSeleccion[] = array($ID_ITEM_NO_SELECCIONADO_EN_LISTA_SELECCION, "Seleccione una Profesión u Oficio...");
$ItemesListaSeleccion = array_merge($PrimerItemListaSeleccion, $ItemesListaSeleccion);
$IdItemSeleccionado = $IdProfesionOficio;
// Los anteriores son parámetros que se le envían a la lista de selección
?>
                <?php $IdListaSeleccion="IdProfesionOficio"; $NombreListaSeleccion="IdProfesionOficio"; include "componenteListaSeleccion.php" ?>
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="custom-control custom-checkbox col-8 col-md-6 col-lg-4">
                <input type="checkbox" class="custom-control-input" id="EstaVigente" name="EstaVigente" <?php echo $EstaVigenteSeleccionado; ?>>
                <label class="custom-control-label" for="EstaVigente">Está Vigente</label>
           </div>
        </div>
<?php if (strcmp($Modo, $MODO_CAMBIO) == 0) { ?>
       <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <input type="text" class="form-control" id="ProbarUrl" name="Url" value="<?php echo $UrlOferta; ?>" readonly>
                <button type="button" class="btn btn-primary" onclick="window.open('<?php echo $UrlOferta; ?>','_new');">Probar</button>
            </div>
        </div>
<?php } // if (strcmp($Modo, $MODO_CAMBIO) == 0) { ?>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="<?php echo $FrameRegreso?>.location.href='ofertasEmpleo.php?Modo=<?php echo $MODO_CAMBIO?>&IdNegocio=<?php echo $IdNegocio?>';">Regresar</button>
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
