<?php
include_once "constantesApp.php";
include_once "CSession.php";
$UsuarioSesionIngresoOKApp = CSession::UsuarioSesionIngresoOK();
// La validación anterior se debe hacer una sola vez por acceso a sesión, pues en ella se verifica el tiempo
// transcurrido desde el último acceso a la sesión y después se actualiza la hora de último acceso a la hora actual.
if (!$UsuarioSesionIngresoOKApp)
   header("Location: " . $URL_PAGINA_INGRESO); // Se redirecciona a la página de ingreso a la aplicación

$SePretendeCambiarContrasena = false;
$SeCambioContrasenaExitosamente = false;

if (isset($_POST["ContrasenaActual"]))
{
    $SePretendeCambiarContrasena = true;
    $ContrasenaAnterior = $_POST["ContrasenaActual"];
    $NuevaContrasena = $_POST["NuevaContrasena"];
    $ConfirmacionNuevaContrasena = $_POST["ConfirmarNuevaContrasena"];
} // if (isset($_POST["ContrasenaActual"]))

// A continuación el código fuente de la implementación
include_once "CUsuarios.php";

if ($SePretendeCambiarContrasena)
{
    try
    {
        $Usuarios = new CUsuarios();
        $ObjUsuario = CSession::DemeObjUsuarioSesion();

        if ($ObjUsuario != NULL)
        {
            $Usuarios->CambiarContrasena($ObjUsuario->DemeIdUsuario(), $ContrasenaAnterior, $NuevaContrasena, $ConfirmacionNuevaContrasena, $NumError, $LongitudMinimaContrasena, $CaracteresEspeciales);

            if ($NumError == 0)
                $SeCambioContrasenaExitosamente = true;
        } // if ($ObjUsuario != NULL)
    } // try
    catch (Exception $e)
    {
        $NumError = 1;
        $MensajeOtroError = $e->getMessage();
    } // catch (Exception $e)
} // if ($SePretendeCambiarContrasena)
// El anterior fue el código fuente de la implementación

include_once "CFormateadorMensajes.php";
$MensajeXDesglosar = "";

if ($NumError != 0)
{
    if ($NumError == 1)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError($MensajeOtroError);
    elseif ($NumError == 1001)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("La contraseña actual es incorrecta.");
    elseif ($NumError == 2001)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("La nueva contraseña no coincide con su confirmación.");
    elseif ($NumError == 3001)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("La nueva contraseña debe tener al menos " . $LongitudMinimaContrasena . " caracteres.");
    elseif ($NumError == 3002)
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("La nueva contraseña debe estar conformada por al menos un caracter alfabético en mayúscula, un caracter alfabético en minúscula, un dígito decimal y un caracter especial entre " . $CaracteresEspeciales);
    else
        $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeError("No se manejó el error número " . $NumError . ".");
} // if ($NumError != 0)
elseif ($SeCambioContrasenaExitosamente)
    $MensajeXDesglosar = CFormateadorMensajes::FormatearMensajeOK("Se cambió la contraseña exitosamente.");

$MaximoTamanoCampoContrasena = CUsuarios::MAXIMO_TAMANO_CAMPO_CONTRASENA;
?>
<!DOCTYPE html>
<html>
<?php
include "encabezados.php";
?>
<body>
<?php
$FormularioActivo = "CambiarContrasena"; // Este es un parámetro que recibe "menuApp.php"
include "menuApp.php";
?>
<form method="post">
    <div class="container mt-4">
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="ContrasenaActual">Contraseña Actual</label>
                <input type="password" class="form-control" id="ContrasenaActual" name="ContrasenaActual" placeholder="Ingrese su contraseña actual" maxlength="<?php echo $MaximoTamanoCampoContrasena;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="NuevaContrasena">Nueva Contraseña</label>
                <input type="password" class="form-control" id="NuevaContrasena" name="NuevaContrasena" placeholder="Ingrese su nueva contraseña" maxlength="<?php echo $MaximoTamanoCampoContrasena;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <label for="ConfirmarNuevaContrasena">Confirmación de Nueva Contraseña</label>
                <input type="password" class="form-control" id="ConfirmarNuevaContrasena" name="ConfirmarNuevaContrasena" placeholder="Confirme su nueva contraseña" maxlength="<?php echo $MaximoTamanoCampoContrasena;?>">
            </div>
        </div>
        <div class="form-row justify-content-center">
            <div class="form-group col-8 col-md-6 col-lg-4">
                <button type="submit" class="btn btn-primary">Enviar</button>
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
