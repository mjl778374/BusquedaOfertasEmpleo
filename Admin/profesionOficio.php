<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$Alias = "";
$SePretendeGuardarInformacion = false;
$SeGuardoInformacionExitosamente = false;

if (isset($_POST["Alias"]))
{
    $SePretendeGuardarInformacion = true;
    $Alias = $_POST["Alias"];
} // if (isset($_POST["Alias"]))

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
        {
            echo "<script>window.top.location.href = 'mainProfesionOficio.php?Modo=" . $Modo . "&IdProfesionOficio=" . $IdProfesionOficio . "&IdAlias=" . $IdAlias . "'</script>";
            exit;
        } // if (strcmp($_GET["IdProfesionOficio"], $IdProfesionOficio) != 0 || strcmp($_GET["IdAlias"], $IdAlias) != 0)
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
    $ObjAliasProfesionOficio = NULL;
    include_once "CProfesionesOficios.php";

    if ($NumError == 0 && $SePretendeGuardarInformacion)
    {
        $AliasProfesionesOficios = new CProfesionesOficios();

        if (strcmp($Modo, $MODO_ALTA) == 0)
            $AliasProfesionesOficios->AltaProfesionOficio($Alias, $NumError, $ObjAliasProfesionOficio);

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
            $AliasProfesionesOficios->CambioAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Alias, $NumError, $ObjAliasProfesionOficio);

        if ($NumError == 0)
            $SeGuardoInformacionExitosamente = true;

        if ($SeGuardoInformacionExitosamente)
        {
            $IdAliasOriginal = $IdAlias;
            $IdNuevoAlias = $ObjAliasProfesionOficio->DemeIdAlias();
        } // if ($SeGuardoInformacionExitosamente)
    } // if ($NumError == 0 && $SePretendeGuardarInformacion)

    if ($NumError == 0 && strcmp($Modo, $MODO_CAMBIO) == 0)
    {
        $AliasProfesionesOficios = new CProfesionesOficios();
        $AliasProfesionesOficios->ConsultarXAliasProfesionOficio($IdProfesionOficio, $IdAlias, $Existe, $ObjAliasProfesionOficio);

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

if ($ObjAliasProfesionOficio != NULL)
{
    $IdProfesionOficio = $ObjAliasProfesionOficio->DemeIdProfesionOficio();
    $IdAlias = $ObjAliasProfesionOficio->DemeIdAlias();
    $Alias = $ObjAliasProfesionOficio->DemeAlias();
} // if ($ObjAliasProfesionOficio != NULL)

if ($SeGuardoInformacionExitosamente && (strcmp($Modo, $MODO_ALTA) == 0 || strcmp($Modo, $MODO_CAMBIO) == 0 && $IdAliasOriginal != $IdNuevoAlias))
{
    echo "<script>window.top.location.href='mainProfesionOficio.php?Modo=" . $MODO_CAMBIO . "&IdProfesionOficio=" . $IdProfesionOficio . "&IdAlias=" . $IdNuevoAlias . "';</script>";
    exit;
} // if ($SeGuardoInformacionExitosamente && (strcmp($Modo, $MODO_ALTA) == 0 || strcmp($Modo, $MODO_CAMBIO) == 0 && $IdAliasOriginal != $IdNuevoAlias))

include_once "CFormateadorMensajes.php";
include_once "CPalabras.php";

$ErrorNoExisteProfesionOficioConIdEspecificado = "No existe la profesión u oficio con el id " . $IdProfesionOficio . ".";
$ErrorNoExisteProfesionOficioConIdProfesionOficioYAliasEspecificados = "No existe el registro con el id de profesión u oficio " . $IdProfesionOficio . " y el id de alias " . $IdAlias  . ".";
$ErrorAliasInvalido = "La profesión u oficio debe tener al menos uno de los siguientes caracteres " . CPalabras::DemeCaracteresValidos();

if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 2)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteProfesionOficioConIdProfesionOficioYAliasEspecificados);
    else
    {
        if (strcmp($Modo, $MODO_ALTA) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe un alias de otra profesión u oficio con el nombre " . $Alias . ". No se puede insertar nuevamente.");
            elseif ($NumError == 2001)
                 $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorAliasInvalido);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'AltaProfesionOficio'.");
        } // if (strcmp($Modo, $MODO_ALTA) == 0)

        elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
        {
            if ($NumError == 1001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorNoExisteProfesionOficioConIdEspecificado);
            elseif ($NumError == 2001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("Ya existe un alias de otra profesión u oficio con el nombre " . $Alias);
            elseif ($NumError == 3001)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($ErrorAliasInvalido);
            elseif ($NumError != 0)
                $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . " en el proceso 'CambioAliasProfesionOficio'.");
        } // elseif (strcmp($Modo, $MODO_CAMBIO) == 0)
    } // else
} // if ($NumError != 0)
elseif ($SeGuardoInformacionExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se guardó la profesión u oficio exitosamente.");

$Alias = htmlspecialchars($Alias);

$MaximoTamanoCampoAlias = CProfesionesOficios::MAXIMO_TAMANO_CAMPO_ALIAS;
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "ProfesionOficio"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="ProfesionOficio">Profesión u Oficio</label>
                <input type="text" class="form-control" id="Alias" name="Alias" placeholder="Ingrese la profesión u oficio" value="<?php echo $Alias; ?>" maxlength="<?php echo $MaximoTamanoCampoAlias;?>">
            </div>
        </div>
        <div class="form-row justify-content-center mt-4">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-primary" onclick="window.top.location.href='profesionesOficios.php';">Regresar</button>
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
